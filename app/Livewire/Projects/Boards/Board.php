<?php

namespace App\Livewire\Projects\Boards;

use App\Models\Sprint;
use App\Models\Project;
use Livewire\Component;

class Board extends Component
{
    public $user;
    public $uuid;

    public $isSprint = false;
    public $activeSprints = null;

    public function mount($uuid)
    {
        $this->user = auth()->user();
        $this->uuid = $uuid;

        // Check if the uuid is a sprint or a project
        $sprint = Sprint::find($this->uuid);
        if ($sprint) {
            $this->isSprint = true;
        } else {
            $project = Project::find($this->uuid);
            if ($project) {
                // Set the selected project
                session()->put('selected_project', $this->uuid);
            
            }
        }

        // Get Active Sprints for the project
        $this->activeSprints = Sprint::where('project_id', $this->uuid)
            ->where('status', 'active')
            ->get();
    }

    public function render()
    {
        return view('livewire.projects.boards.board');
    }
}
