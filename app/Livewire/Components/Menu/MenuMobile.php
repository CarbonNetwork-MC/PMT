<?php

namespace App\Livewire\Components\Menu;

use App\Models\Project;
use Livewire\Component;

class MenuMobile extends Component
{
    public $user;
    public $userProfilePicture;

    public $selectedProject = null;

    public function mount() {
        $this->user = auth()->user();
        $this->userProfilePicture = $this->user->profile_photo_path
            ? asset('storage/' . $this->user->profile_photo_path)
            : null;
            
        $this->selectedProject = request()->route('uuid') ? Project::where('uuid', request()->route('uuid'))->first() : null;
    }

    public function render()
    {
        return view('livewire.components.menu.menu-mobile');
    }
}
