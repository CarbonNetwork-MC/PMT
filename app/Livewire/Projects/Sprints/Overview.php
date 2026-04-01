<?php

namespace App\Livewire\Projects\Sprints;

use App\Models\Project;
use Livewire\Component;

class Overview extends Component
{
    public $project;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
    }
    
    public function render()
    {
        return view('livewire.projects.sprints.overview');
    }
}
