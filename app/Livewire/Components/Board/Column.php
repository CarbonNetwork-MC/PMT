<?php

namespace App\Livewire\Components\Board;

use App\Helpers\CheckIfUserIsAdmin;
use App\Models\Card as CardModel;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
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
        $card = CardModel::create([
            'sprint_uuid' => $this->sprint->uuid,
            'title' => $this->cardName ?: 'New Card',
            'column_id' => $this->column->id,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->sprint->project_uuid,
            'sprint_uuid' => $this->sprint->uuid,
            'card_id' => $card->id,
            'action' => 'create',
            'table' => 'cards',
            'data' => json_encode([
                'title' => $this->cardName ?: 'New Card',
                'column_id' => $this->column->id,
                'sprint_uuid' => $this->sprint->uuid,
            ]),
            'description' => __('logs.board.card_created', [
                'card' => $this->cardName ?: 'New Card', 
                'column' => $this->column->name, 
                'sprint' => $this->sprint->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->sprint->project_uuid)
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
