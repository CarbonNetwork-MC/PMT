<?php

namespace App\Livewire\Components;

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

        // TODO: Get the user's selected project from the request.
    }

    public function render()
    {
        return view('livewire.components.sidebar');
    }
}
