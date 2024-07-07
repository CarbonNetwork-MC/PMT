<?php

namespace App\Livewire\Projects\Settings;

use Livewire\Component;
use App\Models\Project;

class Overall extends Component
{
    public $uuid;
    public $project;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->project = Project::where('uuid', $uuid)->first();
    }

    public function render()
    {
        return view('livewire.projects.settings.overall');
    }
}
