<?php

namespace App\Livewire\Projects\Sprints;

use App\Models\Log;
use App\Models\Card;
use App\Models\Sprint;
use Livewire\Component;
use App\Models\Backlog;
use Illuminate\Support\Str;

class Overview extends Component
{
    public $uuid;
    public $sprints;
    public $archivedSprints;
    public $backlogs;
    
    public $activeSprints;
    public $doneSprints;
    
    public $id;
    public $sprint;

    public $search;
    public $selectedSprint;
    public $numberOfIncompleteCards;
    
    public $createSprintModal = false;
    public $editSprintModal = false;
    public $deleteSprintModal = false;
    public $sprintNotDoneModal = false;

    public $showArchivedSprints = false;

    public $sprintIsDone = false;
    public $sprintOrBacklog = 'sprint';
    public $selectedSprintOrBacklog;

    public $name, $start_date, $end_date, $status;

    public function mount($uuid)
    {
        $this->uuid = $uuid;

        $this->sprints = Sprint::where('project_id', $uuid)->where('is_archived', 0)->with('cards')->orderBy('start_date')->get();
        $this->archivedSprints = Sprint::where('project_id', $uuid)->where('is_archived', 1)->with('cards')->orderBy('start_date')->get();
        $this->backlogs = Backlog::where('project_id', $uuid)->get();

        $this->activeSprints = Sprint::where('project_id', $this->uuid)->where('status', 'active')->get();
        $this->doneSprints = Sprint::where('project_id', $this->uuid)->where('status', 'done')->get();

        $this->selectedSprintOrBacklog = Sprint::where('project_id', $uuid)->where('status', 'active')->first();
    }

    public function updated($key, $value) {
        if ($key == 'search') {
            $this->archivedSprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 1)->where('name', 'like', '%' . $value . '%')->with('cards')->orderBy('start_date')->get();
        }
    }

    /**
     * Create a new sprint
     * 
     * @return void
     */
    public function createSprint() {
        // Validate the date
        $data = $this->validate([
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $data['uuid'] = Str::uuid()->toString();
        $data['project_id'] = $this->uuid;

        // Create the sprint
        $sprint = Sprint::create($data);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 0)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $sprint->uuid,
            'action' => 'create',
            'data' => json_encode($data),
            'table' => 'sprints',
            'description' => 'Created sprint <b>' . $data['name'] . '</b>',
        ]);

        // Close the modal
        $this->createSprintModal = false;

        // Reset the variables
        $this->name = null;
    }

    /**
     * Set the sprint id to edit
     * 
     * @param string $id
     * 
     * @return void
     */
    public function editSprintSetId($id) {
        $this->id = $id;
        $this->editSprintModal = true;
        $this->sprint = Sprint::find($id);

        $this->name = $this->sprint->name;
        $this->start_date = $this->sprint->start_date;
        $this->end_date = $this->sprint->end_date;
        $this->status = $this->sprint->status;
    }

    /**
     * Update the sprint
     * 
     * @return void
     */
    public function updateSprint() {
        // Validate the data
        $data = $this->validate([
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'status' => ['required', 'string'],
        ]);

        // Find the sprint
        $sprint = Sprint::find($this->id);

        // Update the sprint
        $sprint = $sprint->update($data);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 0)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $this->id,
            'action' => 'update',
            'data' => json_encode($data),
            'table' => 'sprints',
            'description' => 'Updated sprint <b>' . $data['name'] . '</b>',
        ]);

        // Close the modal
        $this->editSprintModal = false;
    }

    /**
     * Set the sprint id to delete
     * 
     * @param string $id
     * 
     * @return void
     */
    public function deleteSprint($id) {
        $this->id = $id;
        $this->deleteSprintModal = true;
    }

    /**
     * Destroy the sprint
     * 
     * @return void
     */
    public function destroySprint() {
        // Find the sprint
        $sprint = Sprint::find($this->id);

        // Delete the sprint
        $sprint->delete();

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 0)->with('cards')->orderBy('start_date')->get();

        // Update the archived sprints
        $this->archivedSprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 1)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $this->id,
            'action' => 'delete',
            'data' => json_encode($sprint),
            'table' => 'sprints',
            'description' => 'Deleted sprint <b>' . $sprint->name . '</b>',
        ]);

        // Close the modal
        $this->deleteSprintModal = false;
    }

    /**
     * Start the sprint
     * 
     * @param string $id
     * 
     * @return void
     */
    public function startSprint($id) {
        // Find the sprint
        $sprint = Sprint::find($id);

        // Update the sprint
        $sprint->update([
            'status' => 'active',
        ]);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 0)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $id,
            'action' => 'update',
            'data' => json_encode($sprint),
            'table' => 'sprints',
            'description' => 'Started sprint <b>' . $sprint->name . '</b>',
        ]);
    }

    /**
     * Initiate the sprint completion
     * 
     * @param string $id
     * 
     * @return void
     */
    public function initiateSprintCompletion($id) {
        // Find the sprint
        $this->selectedSprint = Sprint::where('uuid', $id)->with('cards')->first();

        $this->numberOfIncompleteCards = 0;

        // Check if all cards have status 'done' or 'released'
        foreach ($this->selectedSprint->cards as $card) {
            if ($card->status != 'done' && $card->status != 'released') {
                $this->sprintNotDoneModal = true;

                $this->numberOfIncompleteCards++;
            }
        }

        // If all cards have status 'done' or 'released'
        if ($this->sprintNotDoneModal === false) {
            $this->sprintIsDone = true;
            $this->completeSprint($id);
        }
    }

    /**
     * Complete the sprint
     * 
     * @param string $id
     * 
     * @return void
     */
    public function completeSprint($id) {
        // Find the sprint
        $sprint = Sprint::where('uuid', $id)->with('cards')->first();

        // Check if the sprint is done
        if ($this->sprintIsDone === false) {
            // Get all incomplete cards
            $incompleteCards = Card::where('sprint_id', $sprint->uuid)->whereRaw('status != "done" AND status != "released"')->get();

            // Update the cards
            foreach ($incompleteCards as $card) {
                $card->update([
                    'sprint_id' => $this->selectedSprintOrBacklog->uuid,
                ]);
            }
        }

        // Update the sprint
        $sprint->update([
            'status' => 'done',
        ]);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 0)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $id,
            'action' => 'update',
            'data' => json_encode($sprint),
            'table' => 'sprints',
            'description' => 'Completed sprint <b>' . $sprint->name . '</b>',
        ]);

        // Close the modal
        $this->sprintNotDoneModal = false;

        // Reset the variables
        $this->numberOfIncompleteCards = 0;
        $this->sprintIsDone = false;
    }

    /**
     * Archive the sprint
     * 
     * @param string $id
     * 
     * @return void
     */
    public function archiveSprint($id) {
        // Find the sprint
        $sprint = Sprint::find($id);

        // Update the sprint
        $sprint->update([
            'is_archived' => 1,
            'archived_at' => now(),
        ]);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 0)->with('cards')->orderBy('start_date')->get();

        // Update the archived sprints
        $this->archivedSprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 1)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $id,
            'action' => 'update',
            'data' => json_encode($sprint),
            'table' => 'sprints',
            'description' => 'Archived sprint <b>' . $sprint->name . '</b>',
        ]);
    }

    /**
     * Restore the sprint
     * 
     * @param string $id
     * 
     * @return void
     */
    public function restoreSprint($id) {
        // Find the sprint
        $sprint = Sprint::find($id);

        // Update the sprint
        $sprint->update([
            'is_archived' => 0,
            'archived_at' => null,
        ]);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 0)->with('cards')->orderBy('start_date')->get();

        // Update the archived sprints
        $this->archivedSprints = Sprint::where('project_id', $this->uuid)->where('is_archived', 1)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $id,
            'action' => 'update',
            'data' => json_encode($sprint),
            'table' => 'sprints',
            'description' => 'Restored sprint <b>' . $sprint->name . '</b>',
        ]);
    }

    /**
     * Render the component
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.projects.sprints.overview');
    }
}
