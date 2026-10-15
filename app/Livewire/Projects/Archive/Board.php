<?php

namespace App\Livewire\Projects\Archive;

use App\Models\Card;
use App\Models\Project;
use App\Models\Sprint;
use Livewire\Attributes\On;
use Livewire\Component;

class Board extends Component
{
    public $project;
    public $sprint;

    public $columns;
    public $users;

    public $selectedCard = null;

    public $refreshKey = 0;

    public function mount($uuid, $sprintUuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->sprint = Sprint::where('uuid', $sprintUuid)->firstOrFail();

        $this->columns = $this->project->columns()
            ->with('cards.assignees.user')
            ->orderBy('position')
            ->get();
        
        // Load all users assigned to the project + the owner
        $this->users = $this->project->members()->with('user')->get()->pluck('user');
        $this->users->push($this->project->owner);
        $this->users = $this->users->unique('uuid');
    }

    #[On('cardSelected')]
    public function handleCardSelected($cardId) {
        $this->selectedCard = Card::where('id', $cardId['cardId'])->first();
    }

    #[On('closeCardModal')]
    public function handleCloseCardModal() {
        $this->selectedCard = null;
    }

    public function render()
    {
        return view('livewire.projects.archive.board');
    }
}
