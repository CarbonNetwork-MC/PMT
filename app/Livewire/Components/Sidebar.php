<?php

namespace App\Livewire\Components;

use App\Models\Project;
use Livewire\Component;

class Sidebar extends Component
{
    public $user;
    public $userProfilePicture;

    public $selectedProject = null;

    public function mount() {
        $this->user = auth()->user();
        $this->userProfilePicture = $this->user->profile_picture
            ? asset('storage/' . $this->user->profile_picture)
            : null;

        $this->selectedProject = request()->route('uuid') ? Project::where('uuid', request()->route('uuid'))->first() : null;
    }

    public function render()
    {
        return view('livewire.components.sidebar');
    }
}
