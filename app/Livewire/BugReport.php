<?php

namespace App\Livewire;

use App\Models\BugReport as BugReportModel;
use App\Models\BugReportScreenshot;
use Livewire\Component;
use Livewire\WithFileUploads;

class BugReport extends Component
{
    use WithFileUploads;

    public $user;

    public $title;
    public $description;
    public $page;
    public $screenshots;

    public function mount() {
        $this->user = auth()->user();
    }

    public function submitBugReport() {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'page' => 'nullable|string|max:255',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'image|mimes:jpeg,png,jpg|max:2048', // Max size 2MB
        ]);

        $bugReport = BugReportModel::create([
            'title' => $this->title,
            'description' => $this->description,
            'page' => $this->page,
            'user_uuid' => $this->user->uuid,
        ]);

        // Save screenshots if any
        if ($this->screenshots) {
            foreach ($this->screenshots as $screenshot) {
                $path = $screenshot->store('bug_reports/screenshots', 'public');
                
                BugReportScreenshot::create([
                    'bug_report_id' => $bugReport->id,
                    'screenshot_path' => $path,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.bug-report');
    }
}
