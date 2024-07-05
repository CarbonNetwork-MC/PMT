<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Str;

class CreateProject extends Component
{
    public $user;
    public $name;
    public $description;

    public function mount() {
        $this->user = auth()->user();
    }

    public function render()
    {
        return view('livewire.projects.create-project');
    }

    public function createProject() {
        // Validate data
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $data['uuid'] = Str::uuid()->toString();
        $data['owner_id'] = auth()->user()->uuid;

        // Create project
        $project = Project::create($data);

        // Add user as project member
        $project->members()->create([
            'project_id' => $project->uuid,
            'user_id' => auth()->user()->uuid,
            'role' => 'owner',
        ]);

        // Redirect to project dashboard
        return redirect()->route('projects.overview.render');
    }
}
