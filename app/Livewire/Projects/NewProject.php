<?php

namespace App\Livewire\Projects;

use App\Models\ColumnColor;
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

        // Create the default columns for the project
        $colorRose = ColumnColor::where('name', 'rose')->first();
        $colorSky = ColumnColor::where('name', 'sky')->first();
        $colorGreen = ColumnColor::where('name', 'green')->first();

        $project->columns()->createMany([
            ['project_uuid' => $project->uuid, 'name' => 'To Do', 'position' => 1, 'color_id' => $colorRose->id],
            ['project_uuid' => $project->uuid, 'name' => 'In Progress', 'position' => 2, 'color_id' => $colorSky->id],
            ['project_uuid' => $project->uuid, 'name' => 'Done', 'position' => 3, 'color_id' => $colorGreen->id],
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
