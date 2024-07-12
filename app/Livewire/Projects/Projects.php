<?php

namespace App\Livewire\Projects;

use App\Models\Log;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Str;

class Projects extends Component
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

    /**
     * Create a new project
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
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
            'role_id' => '3',
        ]);

        // Create a new Log - Create Project
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $project->uuid,
            'action' => 'create',
            'table' => 'projects',
            'data' => json_encode($data),
            'description' => 'Created project <b>' . $data['name'] . '</b>',
        ]);

        // Create a new Log - Add User as Project Member
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $project->uuid,
            'action' => 'create',
            'table' => 'project_members',
            'data' => json_encode(['project_id' => $project->uuid, 'user_id' => auth()->user()->uuid, 'role_id' => '3']),
            'description' => 'Added user as project member',
        ]);

        // Redirect to project dashboard
        return redirect()->route('projects.projects.render');
    }

    public function render()
    {
        return view('livewire.projects.projects');
    }
}
