<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public $user;

    public function mount()
    {
        $this->user = User::find(auth()->id())->with('role')->first();

        // Clear the selected project session
        if (session()->has('selected_project')) {
            session()->forget('selected_project');
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
