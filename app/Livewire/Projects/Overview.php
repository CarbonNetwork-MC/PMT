<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Str;

class Overview extends Component
{
    public $user;
    public $projects;
    public $createProjectModal = false;

    public $name, $description;

    public function mount() {

        $this->user = auth()->user();
        // $this->projects = Project::where('owner_id', $this->user->uuid)->with('sprints')->with('members')->get();
        // Get the project where the user is a member of
        $this->projects = Project::whereHas('members', function($query) {
            $query->where('user_id', $this->user->uuid);
        })->with('sprints')->with('members')->get();

        // Clear the selected project
        if (session()->has('selected_project')) {
            session()->forget('selected_project');
        }
    }

    public function createProject() {
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

    public function render()
    {
        return view('livewire.projects.overview');
    }
}
