<?php

namespace App\Livewire\Projects\Backlog;

use App\Models\Log;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\BacklogCard;
use App\Models\ProjectMember;
use App\Models\Backlog as BacklogModel;
use App\Models\BacklogCardAssignee;

class Backlog extends Component
{
    public $uuid;
    public $backlogId;

    public $buckets;
    public $numOfCards = 0;
    public $projectMembers;
    
    public $selectedBucket;
    public $selectedCard;
    public $selectedCardApprovalStatus;
    public $selectedCardColor = 'gray';

    public $approvalStatusOptions = [
        'None' => 'gray', 
        'Approved' => 'green', 
        'Rejected' => 'red', 
        'Needs work' => 'yellow'
    ];

    public $bucket;
    public $selectedBucketId;
    public $card;
    public $selectedCardId;

    public $createBucketModal = false;
    public $editBucketModal = false;
    public $deleteBucketModal = false;

    public $createCardModal = false;

    public $selectedCardModal = false;
    public $editCardModal = false;
    public $deleteCardModal = false;

    public $name, $description, $assignedTo;

    public function mount($uuid, $backlogId = null) {
        $this->uuid = $uuid;
        $this->backlogId = $backlogId;

        $cards = [];

        // Get all buckets and cards for the selected project.
        $this->buckets = BacklogModel::where('project_id', $uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->get();

        // Get all project users
        $this->projectMembers = ProjectMember::where('project_id', $uuid)->with('user')->get();

        // Check the session if the user has already selected a backlog and select it, if not, select the first one.
        if (session()->has('selected_backlog')) {
            $this->selectedBucket = BacklogModel::where('uuid', session('selected_backlog'))->with('cards.tasks')->first();
        } else {
            $this->selectedBucket = $this->buckets->first();
        }

        // ! Development - Remove this in production
        // if ($this->selectedBucket && $this->selectedBucket->cards->count() > 0) {
        //     $this->selectedCard = $this->selectedBucket->cards->first();
        //     $this->selectedCardApprovalStatus = $this->selectedCard->approval_status;
        // }
        
        // Get the number of cards in the selected bucket
        foreach ($this->buckets as $bucket) {
            foreach ($bucket->cards as $card) {
                $cards[] = $card;
            }
        }
        
        $this->numOfCards = count($cards);
    }

    public function updated($key, $value) {
        if ($key === 'selectedCardApprovalStatus') {
            $card = BacklogCard::where('id', $this->selectedCard->id)->first();
            
            $card->update([
                'approval_status' => $this->selectedCardApprovalStatus
            ]);

            // Create a new Log
            Log::create([
                'user_id' => auth()->user()->uuid,
                'project_id' => $this->uuid,
                'backlog_id' => $this->selectedBucket->uuid,
                'card_id' => $card->id,
                'action' => 'update',
                'data' => json_encode($card),
                'table' => 'backlog_cards',
                'description' => 'Updated card <b>' . $card->name . '</b>',
                'environment' => config('app.env')
            ]);
        }
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

        // Set the selected backlog in the session
        session()->put('selected_backlog', $this->selectedBucket->uuid);
    }

    /**
     * Create a new bucket
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createBucket() {
        // Validate the data
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);
        
        $data['uuid'] = Str::uuid()->toString();
        $data['project_id'] = $this->uuid;
        $data['status'] = 'active';

        // Create a new bucket
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

        // Close the modal
        $this->createBucketModal = false;

        // Reload the page
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
        // Set the selected bucket ID
        $this->selectedBucketId = $id;

        // Open the edit bucket modal
        $this->editBucketModal = true;

        // Get the bucket
        $this->bucket = $this->buckets->where('uuid', $id)->first();

        // Set the name and description
        $this->name = $this->bucket->name;
        $this->description = $this->bucket->description;
    }

    /**
     * Update the Bucket
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBucket() {
        // Validate the data
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        // Get the bucket and update it
        $bucket = BacklogModel::where('uuid', $this->selectedBucketId)->first();
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

        // Close the modal
        $this->editBucketModal = false;

        // Reload the page
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
        // Set the selected bucket ID
        $this->selectedBucketId = $id;

        // Open the delete bucket modal
        $this->deleteBucketModal = true;
    }

    /**
     * Delete the Bucket
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyBucket() {
        // Get the bucket and delete it
        $bucket = BacklogModel::where('uuid', $this->selectedBucketId)->first();
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

        // Close the modal
        $this->deleteBucketModal = false;

        // Reload the page
        return redirect()->route('projects.backlog.render', $this->uuid);
    }

    /**
     * Create a new card
     * 
     * @return void
     */
    public function createCard() {
        // Validate the data
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $data['backlog_id'] = $this->selectedBucket->uuid;
        $data['assignee_id'] = $this->assignedTo;

        // Create a new card
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

        // Close the modal
        $this->createCardModal = false;

        // Update the selected bucket
        $this->selectedBucket = BacklogModel::where('uuid', $this->selectedBucket->uuid)->with('cards')->first();
    }

    /**
     * Select a Card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function selectCard($id) {
        // Get the selected card
        $this->selectedCard = $this->selectedBucket->cards->where('id', $id)->first();

        // Set selectedCardApprovalStatus
        $this->selectedCardApprovalStatus = $this->selectedCard->approval_status;

        // Set the selected card color based on the approval status
        $this->selectedCardColor = $this->approvalStatusOptions[$this->selectedCard->approval_status];

        // Open the selected card modal
        $this->selectedCardModal = true;
    }

    /**
     * Change the status of a card
     * 
     * @param string $status
     * 
     * @return void
     */
    public function changeStatus($status) {
        BacklogCard::where('id', $this->selectedCard->id)->update([
            'approval_status' => $status
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->first();

        // Update the selected card color
        $this->selectedCardColor = $this->approvalStatusOptions[$status];

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($this->selectedCard),
            'table' => 'backlog_cards',
            'description' => 'Updated status for card <b>' . $this->selectedCard->name . '</b>',
            'environment' => config('app.env')
        ]);

    }

    /**
     * Add an assignee to a card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function addAssignee($id) {
        $projectMember = ProjectMember::where('id', $id)->first();
        $assignee = BacklogCardAssignee::create([
            'backlog_card_id' => $this->selectedCard->id,
            'user_id' => $projectMember->user_id
        ]);

        // Update the selected card
        $card = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Added assignee to card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Remove an assignee from a card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function removeAssignee($id) {
        $projectMember = ProjectMember::where('id', $id)->first();
        $assignee = BacklogCardAssignee::where('user_id', $projectMember->user_id)->where('backlog_card_id', $this->selectedCard->id)->first();
        $assignee->delete();

        // Update the selected card
        $card = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Removed assignee from card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    public function deleteCard($id) {
        // Set the selected card ID
        $this->selectedCardId = $id;

        // Open the delete card modal
        $this->deleteCardModal = true;
    }

    public function destroyCard() {
        // Get the card and delete it
        $card = BacklogCard::where('id', $this->selectedCardId)->first();
        $card->delete();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $card->id,
            'action' => 'delete',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Deleted card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);

        // Close the modal
        $this->deleteCardModal = false;

        // Close the selected card modal (if open)
        $this->selectedCardModal = false;

        // Update the selected bucket
        $this->selectedBucket = BacklogModel::where('uuid', $this->selectedBucket->uuid)->with('cards')->first();
    }

    /**
     * Render the component
     * 
     * @return \Illuminate\View\View
     */
    public function render() {
        return view('livewire.projects.backlog.backlog');
    }
}

