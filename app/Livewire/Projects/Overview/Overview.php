<?php

namespace App\Livewire\Projects\Overview;

use App\Models\Log;
use Livewire\Component;
use App\Models\Project;

class Overview extends Component
{
    public $uuid;
    public $logs;
    public $allLogs;

    public $selectedProject;

    public $numberOfActiveSprints;

    public function mount($uuid)
    {
        $this->uuid = $uuid;

        $this->logs = Log::where('project_id', $uuid)->with('user')->orderBy('created_at', 'desc')->take(10)->get();
        $this->allLogs = Log::where('project_id', $uuid)->with('user')->orderBy('created_at', 'desc')->get();

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

    /**
     * Load more logs
     * 
     * @return void
     */
    public function loadMoreLogs() {
        // Load 10 more logs each click
        $this->logs = Log::where('project_id', $this->uuid)->orderBy('created_at', 'desc')->take($this->logs->count() + 10)->get();
    }

    /**
     * Render the component
     * 
     * @return Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.projects.overview.overview');
    }
}
