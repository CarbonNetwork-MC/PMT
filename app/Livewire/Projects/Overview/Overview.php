<?php

namespace App\Livewire\Projects\Overview;

use App\Models\Log;
use Livewire\Component;
use App\Models\Project;

class Overview extends Component
{
    public $uuid;
    public $logs;

    public $selectedProject;

    public $numberOfActiveSprints;

    public function mount($uuid)
    {
        $this->uuid = $uuid;

        $this->logs = Log::where('project_id', $uuid)->orderBy('created_at', 'desc')->take(10)->get();

        $this->selectedProject = Project::where('uuid', $this->uuid)->with(['members'], ['backlogs'], ['sprints.cards.tasks'])->first();
        if ($this->selectedProject) {
            // Set the selected project
            session()->put('selected_project', $this->uuid);
            if ($this->selectedProject->backlogs->count() > 0) {
                session()->put('selected_backlog', $this->selectedProject->backlogs->first()->id);
            }
        }

        $this->numberOfActiveSprints = $this->selectedProject->sprints->where('status', 'active')->count();
    }

    public function render()
    {
        return view('livewire.projects.overview.overview');
    }
}
