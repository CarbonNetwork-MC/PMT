<?php

namespace App\Livewire\Projects\Overview;

use App\Models\Log;
use Livewire\Component;
use App\Models\Project;

class Overview extends Component
{
    public $uuid;
    public $logs;

    public function mount($uuid)
    {
        $this->uuid = $uuid;

        $this->logs = Log::where('project_id', $uuid)->orderBy('created_at', 'desc')->take(10)->get();

        $project = Project::find($this->uuid);
        if ($project) {
            // Set the selected project
            session()->put('selected_project', $this->uuid);
            session()->put('selected_backlog', $project->backlogs->first()->id);
        }    
    }

    public function render()
    {
        return view('livewire.projects.overview.overview');
    }
}
