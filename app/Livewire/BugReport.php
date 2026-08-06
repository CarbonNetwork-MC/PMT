<?php

namespace App\Livewire;

use App\Models\BugReport as BugReportModel;
use App\Models\BugReportScreenshot;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class BugReport extends Component
{
    use WithFileUploads;

    public $user;

    public $title;
    public $description;
    public $page;
    public $screenshots;

    public array $pages = [
        'home' => 'Home',
        'projects' => 'Projects',
        'profile' => 'Profile',
        'project_dashboard' => 'Project Dashboard',
        'board' => 'Board',
        'sprints' => 'Sprints',
        'backlog' => 'Backlog',
        'archive' => 'Archive',
        'settings' => 'Settings',
    ];

    public function mount() {
        $this->user = auth()->user();

        $this->page = $this->pages['home'];
    }

    public function submitBugReport() {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'page' => 'required|string|max:255',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'image|mimes:jpeg,png,jpg|max:2048',
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

        $this->reset(['title', 'description', 'page', 'screenshots']);

        Toaster::success(__('bug-report.toasts.success'));
    }

    public function render()
    {
        return view('livewire.bug-report');
    }
}
