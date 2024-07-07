<?php

namespace App\Livewire\Components;

use App\Models\Project;
use Livewire\Component;

class SettingsTabs extends Component
{
    public $uuid;
    public $owner;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        
        $project = Project::where('uuid', $uuid)->first();

        $this->owner = $project->owner_id;
    }

    public function render()
    {
        return view('livewire.components.settings-tabs');
    }
}
