<?php

namespace App\Livewire\Utils;

use App\Models\Card;
use App\Models\Sprint;
use Livewire\Component;
use App\Models\BugReport as BugReportModel;

class BugReport extends Component
{
    public $user;

    public $description;
    public $page;

    public $bugReportsSprintID = '3ca65bf7-a026-4c05-87bf-b7d7162b7d9d';

    public function mount()
    {
        $this->user = auth()->user();
    }

    /**
     * Submit the bug report
     * 
     * @return void
     */
    public function submit() {
        // Validate the data
        $this->validate([
            'description' => 'string|required',
            'page' => 'string|required',
        ]);

        // Create the bug report
        $bugReport = BugReportModel::create([
            'description' => $this->description,
            'page' => $this->page,
            'user_id' => $this->user->uuid,
        ]);

        // Check if the bug reports sprint exists
        $sprint = Sprint::where('uuid', $this->bugReportsSprintID)->with('cards')->first();

        // If the sprint exists, add the bug report to the sprint
        if ($sprint) {
            $numberOfCards = $sprint->cards->count();

            Card::create([
                'sprint_id' => $sprint->uuid,
                'name' => 'Bug Report #' . $bugReport->id,
                'description' => '<b>Description:</b> ' . $bugReport->description . '<br><b>Page:</b> ' . $bugReport->page,
                'status' => 'todo',
                'approval_status' => 'approved',
                'card_index' => $numberOfCards + 1,
            ]);
        }

        // Reset the form
        $this->description = '';
        $this->page = '';

        // Create a toast
        $this->dispatch('notify', ['message' => __('bugs.bug_report_submitted'), 'type' => 'success']);
    }

    /**
     * Render the component
     * 
     * @return Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.utils.bug-report');
    }
}
