<?php

namespace App\Livewire\Components\Board;

use App\Models\Card as CardModel;
use Livewire\Component;

class Column extends Component
{
    public $column;
    public $sprint;
    public $cards;
    public $users;

    public $createNewCard = false;
    public $cardName = '';

    public $refreshKey = 0;

    public function mount($column, $sprint, $users, $refreshKey) {
        $this->column = $column->load([
            'cards' => function ($query) use ($sprint) {
                $query->where('sprint_uuid', $sprint->uuid)
                    ->with('assignees.user');
            }
        ]);

        $this->sprint = $sprint;
        $this->cards = $this->column->cards->sortBy('card_index');
        $this->users = $users;
        $this->refreshKey = $refreshKey;
    }

    public function addCard() {
        CardModel::create([
            'sprint_uuid' => $this->sprint->uuid,
            'title' => $this->cardName ?: 'New Card',
            'column_id' => $this->column->id,
        ]);

        $this->dispatch('refreshBoard');
    }

    public function cancelCardCreation() {
        $this->createNewCard = false;
        $this->cardName = '';
    }

    public function render()
    {
        return view('livewire.components.board.column');
    }
}
