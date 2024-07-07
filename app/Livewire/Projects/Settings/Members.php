<?php

namespace App\Livewire\Projects\Settings;

use Livewire\Component;

class Members extends Component
{
    public $uuid;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
    }

    public function render()
    {
        return view('livewire.projects.settings.members');
    }
}
