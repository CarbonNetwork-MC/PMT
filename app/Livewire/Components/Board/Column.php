<?php

namespace App\Livewire\Components\Board;

use App\Models\Card as CardModel;
use App\Models\ProjectColumn;
use Livewire\Attributes\On;
use Livewire\Component;

class Column extends Component
{
    public $columnId;
    public $sprint;
    public $users;

    public function mount($column, $sprint, $users) {
        $this->columnId = $column->id;
        $this->sprint = $sprint;
        $this->users = $users;
    }

    public function getColumnProperty() {
        return ProjectColumn::with('cards.assignees.user')->find($this->columnId);
    }

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
