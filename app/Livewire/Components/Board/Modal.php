<?php

namespace App\Livewire\Components\Board;

use Livewire\Component;

class Modal extends Component
{
    public $card;
    public $sprint;
    public $users;

    public function mount($card, $sprint, $users) {
        $this->card = $card->load('assignees.user');
        $this->sprint = $sprint;
        $this->users = $users;
    }

    public function closeModal() {
        $this->dispatch('closeCardModal');
    }

    public function render()
    {
        return view('livewire.components.board.modal');
    }
}
