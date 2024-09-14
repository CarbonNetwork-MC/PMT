<?php

namespace App\Livewire\Projects\Backlog;

use App\Models\Backlog as BacklogModel;
use Livewire\Component;

class Backlog extends Component
{
    public $uuid;
    public $buckets;
    public $numOfCards = 0;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $cards = [];

        $this->buckets = BacklogModel::where('project_id', $uuid)->with('cards.tasks')->get();
        
        foreach ($this->buckets as $bucket) {
            foreach ($bucket->cards as $card) {
                $cards[] = $card;
            }
        }
        
        $this->numOfCards = count($cards);
    }

    public function render()
    {
        return view('livewire.projects.backlog.backlog');
    }
}
