<?php

namespace App\Livewire\Projects\Backlog;

use App\Models\Backlog as BacklogModel;
use Livewire\Component;

class Backlog extends Component
{
    public $uuid;
    public $buckets;
    public $selectedBucket;
    public $numOfCards = 0;

    public $createBucketModal = false;
    public $editBucketModal = false;
    public $deleteBucketModal = false;

    public $createCardModal = false;
    public $editCardModal = false;
    public $deleteCardModal = false;

    public $bucketName, $bucketDescription;
    public $cardName, $cardDescription, $assignedTo;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $cards = [];

        $this->buckets = BacklogModel::where('project_id', $uuid)->with('cards.tasks')->get();
        $this->selectedBucket = $this->buckets->first();
        
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

    public function selectBucket($id) {
        $this->selectedBucket = $this->buckets->where('id', $id)->first(); 
    }
}

