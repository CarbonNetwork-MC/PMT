<?php

namespace App\Livewire\Components\Board;

use App\Helpers\CheckProjectPermissions;
use App\Models\Card as CardModel;
use App\Models\CardAssignee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Card extends Component
{
    public $cardId;
    public $card;
    public $users;

    public $search = '';
    public $filteredUsers = [];

    public $isProjectAdminOrOwner = false;

    public function mount($card, $users) {
        $this->cardId = $card->id;
        $this->users = $users;
        $this->filteredUsers = $users;

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(Auth::user(), $card->column->project);

        $this->loadCard();
    }

    public function updatedSearch() {
        $searchTerm = strtolower($this->search);
        $this->filteredUsers = $this->users->filter(function ($user) use ($searchTerm) {
            return str_contains(strtolower($user->name), $searchTerm) || str_contains(strtolower($user->email), $searchTerm);
        });
    }

    public function loadCard() {
        $this->card = CardModel::with('assignees.user')->find($this->cardId);
    }

    public function selectCard() {
        $this->dispatch('cardSelected', ['cardId' => $this->cardId]);
    }

    public function toggleAssignee($userUuid, $isChecked) {
        if ($isChecked) {
            CardAssignee::firstOrCreate([
                'card_id' => $this->cardId,
                'user_uuid' => $userUuid,
            ]);
        } else {
            CardAssignee::where('card_id', $this->cardId)
                ->where('user_uuid', $userUuid)
                ->delete();
        }

        $this->loadCard();
    }

    public function clearAssignees() {
        CardAssignee::where('card_id', $this->cardId)->delete();
        $this->loadCard();
    }

    public function assignToMe() {
        CardAssignee::firstOrCreate([
            'card_id' => $this->cardId,
            'user_uuid' => auth()->user()->uuid,
        ]);

        $this->loadCard();
    }

    public function deleteCard() {
        $this->dispatch('cardDeleteInitiated', ['cardId' => $this->cardId]);
    }
    
    public function moveCard() {
        $this->dispatch('cardMoveInitiated', ['cardId' => $this->cardId]);
    }

    public function makeACopy() {
        $this->dispatch('cardCopyInitiated', ['cardId' => $this->cardId]);
    }

    public function render()
    {
        return view('livewire.components.board.card');
    }
}