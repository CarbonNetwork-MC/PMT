<?php

namespace App\Livewire\Admin\BugReports;

use App\Models\AppSetting;
use App\Models\BugReport;
use App\Models\BugReportScreenshot;
use App\Models\Card;
use App\Models\Sprint;
use App\Models\Task;
use Livewire\Component;

class ReviewReport extends Component
{
    public string $sprintUuid;

    public BugReport $report;

    public string $cardTitle = '';
    public string $cardDescription = '';
    public array $tasks = [];

    public bool $showRemoveModal = false;

    public function mount($reportId) {
        $this->sprintUuid = AppSetting::where('key', 'sprint_id')->first()->value;
        $this->report = BugReport::where('id', $reportId)->with(['screenshots'])->firstOrFail();
    }

    public function addTask() {
        $this->tasks[] = [
            'id' => (string) \Str::uuid(),
            'description' => '',
        ];
    }

    public function removeTask($taskId) {
        $this->tasks = array_filter($this->tasks, function ($task) use ($taskId) {
            return $task['id'] !== $taskId;
        });

        // Re-index the tasks array
        $this->tasks = array_values($this->tasks);
    }

    public function resolveReport() {
        $columnId = Sprint::where('uuid', $this->sprintUuid)->first()->project->columns->first()->id;

        $card = Card::create([
            'sprint_uuid' => $this->sprintUuid,
            'title' => $this->cardTitle,
            'description' => $this->cardDescription,
            'column_id' => $columnId,
            'approval_status' => 'None',
            'card_index' => Card::where('sprint_uuid', $this->sprintUuid)->max('card_index') + 1,
        ]);

        foreach ($this->tasks as $task) {
            Task::create([
                'card_id' => $card->id,
                'description' => $task['description'],
                'status' => 'todo',
                'task_index' => Task::where('card_id', $card->id)->max('task_index') + 1,
            ]);
        }

        $this->report->update([
            'is_resolved' => true,
        ]);

        return redirect()->route('admin.bug-reports.render')->success(__('admin.toasts.bug-reports.report_resolved', ['reportId' => $this->report->id]));
    }

    public function removeReport() {
        $this->showRemoveModal = true;
    }

    public function confirmRemoveReport() {
        foreach ($this->report->screenshots as $screenshot) {
            $screenshotPath = public_path('storage/' . $screenshot->path);
            if (file_exists($screenshotPath)) {
                unlink($screenshotPath);
            }
            $screenshot->delete();
        }

        $this->report->delete();

        return redirect()->route('admin.bug-reports.render')->success(__('admin.toasts.bug-reports.report_deleted', ['reportId' => $this->report->id]));
    }

    public function render()
    {
        return view('livewire.admin.bug-reports.review-report');
    }
}
