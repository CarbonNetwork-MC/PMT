<?php

namespace App\Livewire\Projects\Overview;

use Livewire\Component;
use App\Models\Project;

class Overview extends Component
{
    public $uuid;

    public function mount($uuid)
    {
        $this->uuid = $uuid;

        $project = Project::find($this->uuid);
        if ($project) {
            // Set the selected project
            session()->put('selected_project', $this->uuid);
        
        }    
    }

    public function render()
    {
        return view('livewire.projects.overview.overview');
    }
}
