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

    public function mount($column, $sprint, $users) {
        $this->column = $column->load([
            'cards' => function ($query) use ($sprint) {
                $query->where('sprint_uuid', $sprint->uuid)
                    ->with('assignees.user');
            }
        ]);

        $this->sprint = $sprint;
        $this->cards = $this->column->cards->sortBy('card_index');
        $this->users = $users;
    }

    // TODO: This should create a new 'card', where the user has to fill in the name, it then creates the card and refreshes.
    public function addCard($columnId) {
        $card = CardModel::create([
            'sprint_uuid' => $this->sprint->uuid,
            'name' => 'New Card',
            'column_id' => $columnId,
        ]);

        return redirect()->route('projects.board.render', ['uuid' => $this->sprint->project->uuid, 'sprintUuid' => $this->sprint->uuid])->success(__('board.toast.card_created'));
    }

    public function render()
    {
        return view('livewire.components.board.column');
    }
}
