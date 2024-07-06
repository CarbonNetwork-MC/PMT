<?php

namespace App\Livewire\Projects;

use App\Models\Sprint;
use Livewire\Component;

class Sprints extends Component
{
    public $uuid;
    public $sprints;
    public $createSprintModal = false;
    public $name, $start_date, $end_date;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->sprints = Sprint::where('project_id', $uuid)->get();
    }

    public function createSprint() {
        $this->createSprintModal = false;
    }

    public function render()
    {
        $activeSprints = Sprint::where('project_id', $this->uuid)->where('status', 'active')->get();
        $doneSprints = Sprint::where('project_id', $this->uuid)->where('status', 'done')->get();

        return view('livewire.projects.sprints', [
            'activeSprints' => $activeSprints,
            'doneSprints' => $doneSprints,
        ]);
    }
}
