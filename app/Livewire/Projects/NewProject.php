<?php

namespace App\Livewire\Projects;

use App\Models\Log;
use App\Models\Project;
use Livewire\Component;

class NewProject extends Component
{
    public $name;
    public $description;

    public function createProject() {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $user = auth()->user();

        $project = Project::create([
            'uuid' => \Str::uuid(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'owner_uuid' => $user->uuid,
        ]);

        Log::create([
            'user_uuid' => $user->uuid,
            'project_uuid' => $project->uuid,
            'action' => 'create',
            'table' => 'projects',
            'data' => json_encode($data),
            'description' => __('logs.project.created', ['project' => $project->name]),
            'environment' => config('app.env') 
        ]);

        return redirect()->route('projects.dashboard.render', ['uuid' => $project->uuid])->success(__('projects.toast.project-created'));
    }

    public function render()
    {
        return view('livewire.projects.new-project');
    }
}
