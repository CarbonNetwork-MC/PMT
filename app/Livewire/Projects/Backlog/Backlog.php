<?php

namespace App\Livewire\Projects\Backlog;

use App\Models\Log;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Backlog as BacklogModel;
use App\Models\BacklogCard;

class Backlog extends Component
{
    public $uuid;
    public $buckets;
    public $selectedBucket;
    public $numOfCards = 0;

    public $id;
    public $bucket;
    public $card;

    public $createBucketModal = false;
    public $editBucketModal = false;
    public $deleteBucketModal = false;

    public $createCardModal = false;
    public $editCardModal = false;
    public $deleteCardModal = false;

    public $name, $description, $assignedTo;

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

    /**
     * Select a Bucket
     * 
     * @param int $id
     * 
     * @return void
     */
    public function selectBucket($id) {
        $this->selectedBucket = $this->buckets->where('uuid', $id)->first(); 
    }

    /**
     * Create a new bucket
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createBucket() {
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);
        
        $data['uuid'] = Str::uuid()->toString();
        $data['project_id'] = $this->uuid;
        $data['status'] = 'active';

        $bucket = BacklogModel::create($data);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $bucket->uuid,
            'action' => 'create',
            'data' => json_encode($bucket),
            'table' => 'backlogs',
            'description' => 'Created bucket <b>' . $data['name'] . '</b>',
            'environment' => config('app.env')
        ]);

        $this->createBucketModal = false;

        return redirect()->route('projects.backlog.render', $this->uuid);
    }

    /**
     * Open the Edit Bucket Modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function editBucket($id) {
        $this->id = $id;
        $this->editBucketModal = true;
        $this->bucket = $this->buckets->where('uuid', $id)->first();

        $this->name = $this->bucket->name;
        $this->description = $this->bucket->description;
    }

    /**
     * Update the Bucket
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBucket() {
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $bucket = BacklogModel::where('uuid', $this->id)->first();

        $bucket->update($data);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $bucket->uuid,
            'action' => 'update',
            'data' => json_encode($data),
            'table' => 'backlogs',
            'description' => 'Updated bucket <b>' . $data['name'] . '</b>',
            'environment' => config('app.env')
        ]);

        $this->editBucketModal = false;

        return redirect()->route('projects.backlog.render', $this->uuid);
    }

    /**
     * Open the Delete Bucket Modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function deleteBucket($id) {
        $this->id = $id;
        $this->deleteBucketModal = true;
    }

    /**
     * Delete the Bucket
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyBucket() {
        $bucket = BacklogModel::where('uuid', $this->id)->first();

        $bucket->delete();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $bucket->uuid,
            'action' => 'delete',
            'data' => json_encode($bucket),
            'table' => 'backlogs',
            'description' => 'Deleted bucket <b>' . $bucket->name . '</b>',
            'environment' => config('app.env')
        ]);

        $this->deleteBucketModal = false;

        return redirect()->route('projects.backlog.render', $this->uuid);
    }

    /**
     * Create a new card
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCard() {
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $data['backlog_id'] = $this->selectedBucket->uuid;
        $data['assignee_id'] = $this->assignedTo;

        $card = BacklogCard::create($data);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $card->id,
            'action' => 'create',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Created card <b>' . $data['name'] . '</b>',
            'environment' => config('app.env')
        ]);

        $this->createCardModal = false;

        return redirect()->route('projects.backlog.render', $this->uuid);
    }

    /**
     * Render the component
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.projects.backlog.backlog');
    }
}

