<?php

namespace App\Livewire\Projects\Sprints;

use App\Helpers\CheckIfUserIsAdmin;
use App\Models\Log;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NewSprint extends Component
{
    public $project;

    public $name;
    public $start_date;
    public $end_date;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
    }

    public function createSprint() {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $sprint = Sprint::create([
            'uuid' => \Str::uuid(),
            'project_uuid' => $this->project->uuid,
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'planned',
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $sprint->uuid,
            'action' => 'create',
            'table' => 'sprints',
            'data' => json_encode($sprint->toArray()),
            'description' => __('logs.sprints.created', ['sprint' => $sprint->name]),
            'environment' => config('app.env'),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        return redirect()->route('projects.sprints.render', ['uuid' => $this->project->uuid])->success(__('sprints.toast.sprint-created'));
    }

    public function render()
    {
        return view('livewire.projects.sprints.new-sprint');
    }
}
