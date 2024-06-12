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

        dd($this->user);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
