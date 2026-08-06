<?php

namespace App\Livewire\Projects\Backlog;

use App\Helpers\CheckIfUserIsAdmin;
use App\Helpers\CheckProjectPermissions;
use App\Models\Backlog as BacklogModel;
use App\Models\BacklogCard;
use App\Models\BacklogCardAssignee;
use App\Models\BacklogTask;
use App\Models\BacklogTaskAssignee;
use App\Models\Card;
use App\Models\Log;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use Throwable;

class Overview extends Component
{
    public $project;
    public $backlogs;

    public $entities;
    public $projects;
    public $users;

    public $approvalStatuses = ['Approved', 'Needs Work', 'Rejected', 'None'];

    public $selectedBacklog;
    public $selectedCard = null;

    public $selectedProject;
    public $selectedProjectUuid;
    public $selectedEntityUuid;

    public $bucketName = '';
    public $showBucketCreationModal = false;

    public $bucketToEdit = null;
    public $showBucketEditModal = false;

    public $bucketToDelete = null;
    public $showDeleteBucketModal = false;

    public $cardTitle = '';
    public $showCardCreationModal = false;

    public $cardToModify = null;
    public $showDeleteCardModal = false;

    public $taskToModify = null;
    public $showDeleteTaskModal = false;

    public $sprintOrBacklog = 'sprint';
    public $column;
    public $position = 'top';

    public $refreshKey = 0;

    public $isProjectAdminOrOwner = false;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->backlogs = $this->project->backlogs()->with(['cards.assignees', 'cards.tasks.assignees'])->orderBy('created_at', 'desc')->get();
        $this->selectedBacklog = $this->backlogs->first();

        // Load all users assigned to the project + the owner
        $this->users = $this->project->members()->with('user')->get()->pluck('user');
        $this->users->push($this->project->owner);
        $this->users = $this->users->unique('uuid');

        $user = Auth::user();
        $ownedProjects = $user->ownedProjects()->get();
        $projectsWhereAdmin = $user->projectsWhereAdmin()->get();

        $this->projects = $ownedProjects->merge($projectsWhereAdmin)->unique('uuid');
        $this->selectedProject = $this->projects->first();
        $this->selectedProjectUuid = $this->selectedProject ? $this->selectedProject->uuid : null;

        $this->entities = $this->selectedProject ? $this->selectedProject->sprints->where('is_archived', false)->whereIn('status', ['planned', 'active']) : collect();
        $this->selectedEntityUuid = $this->entities->first() ? $this->entities->first()->uuid : null;
        $this->column = $this->selectedProject ? $this->selectedProject->columns()->first()->id : null;

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner($user, $this->project);
    }

    public function updated($key, $value) {
        if ($key === 'selectedProjectUuid') {
            $this->selectedProject = $this->projects->firstWhere('uuid', $value);

            if ($this->selectedProject) {
                $this->entities = $this->sprintOrBacklog === 'sprint'
                    ? $this->selectedProject->sprints->where('is_archived', false)
                    : $this->selectedProject->backlogs;
                $this->selectedEntityUuid = optional($this->entities->first())->uuid;
                $this->column = optional($this->selectedProject->columns()->first())->id;
            }
        } elseif ($key === 'sprintOrBacklog') {
            if ($this->selectedProject) {
                $this->entities = $value === 'sprint'
                    ? $this->selectedProject->sprints->where('is_archived', false)
                    : $this->selectedProject->backlogs;
                $this->selectedEntityUuid = optional($this->entities->first())->uuid;
            }
        }
    }

    public function openBacklog($backlogUuid) {
        $this->selectedBacklog = BacklogModel::where('uuid', $backlogUuid)->with(['cards.assignees', 'cards.tasks.assignees'])->first();
        $this->selectedCard = null;
    }

    public function selectCard($cardId) {
        $this->selectedCard = $this->selectedBacklog->cards()->where('id', $cardId)->with(['assignees', 'tasks.assignees'])->first();
    }

    public function reloadBacklog(): void {
        $this->selectedBacklog->refresh();
        $this->selectedBacklog->load('cards');
    }

    #[On('refreshBacklog')]
    public function handleRefreshBacklog() {
        $this->selectedBacklog = BacklogModel::query()
            ->with([
                'cards.tasks.assignees',
                'cards.assignees',
            ])
            ->findOrFail($this->selectedBacklog->getKey());
    }

    private function reloadBacklogState(): void {
        $selectedBacklogKey = $this->selectedBacklog?->getKey();

        $this->backlogs = $this->project
            ->backlogs()
            ->with([
                'cards.assignees',
                'cards.tasks.assignees',
            ])
            ->orderByDesc('created_at')
            ->get();

        $this->selectedBacklog = $this->backlogs
            ->firstWhere(fn ($backlog) => $backlog->getKey() === $selectedBacklogKey);

        if (!$this->selectedBacklog) {
            $this->selectedBacklog = $this->backlogs->first();
        }
    }

    public function updateApprovalStatus($id, $status) {
        $card = $this->selectedBacklog->cards()->where('id', $id)->first();

        $card->approval_status = $status;
        $card->save();
    }

    #[On('closeBacklogCardModal')]
    public function handleCloseBacklogCardModal() {
        $this->selectedCard = null;
    }

    public function createBucket() {
        if (empty($this->bucketName)) {
            Toaster::error(__('backlog.toasts.bucket_name_required'));
            return;
        }

        $newBacklog = BacklogModel::create([
            'uuid' => \Str::uuid(),
            'project_uuid' => $this->project->uuid,
            'name' => $this->bucketName,
        ]);

        if ($this->backlogs->isEmpty()) {
            $this->selectedBacklog = $newBacklog;
        }

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $newBacklog->uuid,
            'action' => 'create',
            'table' => 'backlogs',
            'data' => json_encode([
                'name' => $newBacklog->name,
            ]),
            'description' => __('logs.backlog.bucket_created', [
                'bucket' => $newBacklog->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        Toaster::success(__('backlog.toasts.bucket_created', ['bucket' => $newBacklog->name]));

        $this->bucketName = '';
        $this->showBucketCreationModal = false;

        $this->reloadBacklogState();
    }

    public function cancelBucketCreation() {
        $this->bucketName = '';
        $this->showBucketCreationModal = false;
    }

    public function editBucket($backlogId) {
        $this->bucketToEdit = BacklogModel::where('uuid', $backlogId)->first();
        $this->bucketName = $this->bucketToEdit->name;
        $this->showBucketEditModal = true;
    }

    public function updateBucket() {
        if (!$this->bucketToEdit) {
            Toaster::error(__('backlog.toasts.bucket_not_found'));
            $this->showBucketEditModal = false;
            return;
        }

        $this->bucketToEdit->name = $this->bucketName;
        $this->bucketToEdit->save();

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->bucketToEdit->uuid,
            'action' => 'update',
            'table' => 'backlogs',
            'data' => json_encode([
                'name' => $this->bucketToEdit->name,
            ]),
            'description' => __('logs.backlog.bucket_updated', [
                'bucket' => $this->bucketToEdit->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        Toaster::success(__('backlog.toasts.bucket_updated', ['bucket' => $this->bucketToEdit->name]));

        $this->bucketName = '';
        $this->showBucketEditModal = false;

        $this->reloadBacklogState();
    }

    public function removeBucket($backlogId) {
        $this->bucketToDelete = BacklogModel::where('uuid', $backlogId)->first();
        $this->showDeleteBucketModal = true;
    }

    public function destroyBucket() {
        if (!$this->bucketToDelete) {
            Toaster::error(__('backlog.toasts.bucket_not_found'));
            $this->showDeleteBucketModal = false;
            return;
        }

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->bucketToDelete->uuid,
            'action' => 'delete',
            'table' => 'backlogs',
            'data' => json_encode([
                'name' => $this->bucketToDelete->name,
            ]),
            'description' => __('logs.backlog.bucket_deleted', [
                'bucket' => $this->bucketToDelete->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        Toaster::success(__('backlog.toasts.bucket_deleted', ['bucket' => $this->bucketToDelete->name]));

        $this->bucketToDelete->delete();
        $this->reset(['bucketToDelete', 'showDeleteBucketModal']);

        $this->reloadBacklogState();
    }

    public function createCard() {
        if (empty($this->cardTitle)) {
            Toaster::error(__('backlog.toasts.card_title_required'));
            return;
        }

        $newCardIndex = BacklogCard::where('backlog_uuid', $this->selectedBacklog->uuid)
            ->max('card_index') + 1;

        $newCard = BacklogCard::create([
            'backlog_uuid' => $this->selectedBacklog->uuid,
            'title' => $this->cardTitle,
            'description' => null,
            'approval_status' => 'None',
            'card_index' => $newCardIndex,
        ]);

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->selectedBacklog->uuid,
            'backlog_card_id' => $newCard->id,
            'action' => 'create',
            'table' => 'backlog_cards',
            'data' => json_encode([
                'title' => $newCard->title,
                'description' => $newCard->description,
                'approval_status' => $newCard->approval_status,
                'deadline' => $newCard->deadline,
            ]),
            'description' => __('logs.backlog.card_created', [
                'card' => $newCard->title, 
                'backlog' => $this->selectedBacklog->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        Toaster::success(__('backlog.toasts.card_created', ['card' => $newCard->title]));

        $this->cardTitle = '';
        $this->showCardCreationModal = false;

        $this->reloadBacklogState();
    }

    #[On('backlogCardDeleteInitiated')]
    public function handleBacklogCardDeleteInitiated($cardId) {
        $this->cardToModify = BacklogCard::where('id', $cardId)->first();
        $this->showDeleteCardModal = true;
    }

    public function deleteCard($cardId) {
        $this->cardToModify = BacklogCard::where('id', $cardId)->first();
        $this->showDeleteCardModal = true;
    }

    public function confirmDeleteCard() {
        if (!$this->cardToModify) {
            Toaster::error(__('backlog.toasts.card_not_found'));
            $this->showDeleteCardModal = false;
            return;
        }

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->selectedBacklog->uuid,
            'backlog_card_id' => $this->cardToModify->id,
            'action' => 'delete',
            'table' => 'backlog_cards',
            'data' => json_encode([
                'title' => $this->cardToModify->title,
                'description' => $this->cardToModify->description,
                'approval_status' => $this->cardToModify->approval_status,
                'deadline' => $this->cardToModify->deadline,
            ]),
            'description' => __('logs.backlog.card_deleted', [
                'card' => $this->cardToModify->title, 
                'backlog' => $this->selectedBacklog->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        Toaster::success(__('backlog.toasts.card_deleted', ['card' => $this->cardToModify->title]));

        $this->cardToModify->delete();
        $this->reset(['cardToModify', 'showDeleteCardModal']);

        $this->reloadBacklogState();
    }

    public function moveCard($cardId) {
        $this->moveBacklogCardRequested(
            $cardId,
            $this->sprintOrBacklog,
            $this->selectedEntityUuid,
            $this->column,
            $this->position
        );
    }

    #[On('moveBacklogCardRequested')]
    public function moveBacklogCardRequested(
        int $cardId,
        string $sprintOrBacklog,
        string $selectedEntityUuid,
        ?int $column,
        string $position
    ): void {
        $this->selectedCard = null;

        try {
            DB::transaction(function () use (
                $cardId,
                $sprintOrBacklog,
                $selectedEntityUuid,
                $column,
                $position
            ) {
                $backlogCard = BacklogCard::query()
                    ->with([
                        'tasks.assignees',
                        'assignees',
                    ])
                    ->findOrFail($cardId);

                if ($sprintOrBacklog === 'backlog') {
                    $index = $position === 'top'
                        ? 0
                        : (BacklogCard::where('backlog_uuid', $selectedEntityUuid)
                            ->max('card_index') ?? -1) + 1;

                    BacklogCard::where('backlog_uuid', $selectedEntityUuid)
                        ->where('card_index', '>=', $index)
                        ->increment('card_index');

                    $backlogCard->update([
                        'backlog_uuid' => $selectedEntityUuid,
                        'card_index' => $index,
                    ]);

                    Log::create([
                        'user_uuid' => Auth::user()->uuid,
                        'project_uuid' => $this->project->uuid,
                        'backlog_uuid' => $this->selectedBacklog->uuid,
                        'backlog_card_id' => $backlogCard->id,
                        'action' => 'update',
                        'table' => 'backlog_cards',
                        'data' => json_encode([
                            'title' => $backlogCard->title,
                            'description' => $backlogCard->description,
                            'approval_status' => $backlogCard->approval_status,
                            'deadline' => $backlogCard->deadline,
                            'moved_to_backlog_uuid' => $selectedEntityUuid,
                        ]),
                        'description' => __('logs.backlog.card_moved_backlog', [
                            'card' => $backlogCard->title, 
                            'fromBacklog' => optional($backlogCard->backlog)->name, 
                            'toBacklog' => optional(BacklogModel::where('uuid', $selectedEntityUuid)->first())->name, 
                        ]),
                        'environment' => app()->environment(),
                        'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
                    ]);

                    return;
                }

                $index = $position === 'top'
                    ? 0
                    : (Card::where('sprint_uuid', $selectedEntityUuid)
                        ->max('card_index') ?? -1) + 1;

                Card::where('sprint_uuid', $selectedEntityUuid)
                    ->where('card_index', '>=', $index)
                    ->increment('card_index');

                $sprintCard = Card::create([
                    'sprint_uuid' => $selectedEntityUuid,
                    'title' => $backlogCard->title,
                    'description' => $backlogCard->description,
                    'column_id' => $column,
                    'approval_status' => $backlogCard->approval_status,
                    'card_index' => $index,
                ]);

                foreach ($backlogCard->tasks as $task) {
                    $sprintTask = $sprintCard->tasks()->create([
                        'description' => $task->description,
                        'status' => $task->status,
                        'task_index' => $task->task_index,
                    ]);

                    foreach ($task->assignees as $assignee) {
                        $sprintTask->assignees()->create([
                            'user_uuid' => $assignee->user_uuid,
                        ]);
                    }
                }

                foreach ($backlogCard->assignees as $assignee) {
                    $sprintCard->assignees()->create([
                        'user_uuid' => $assignee->user_uuid,
                    ]);
                }

                Log::create([
                    'user_uuid' => Auth::user()->uuid,
                    'project_uuid' => $this->project->uuid,
                    'backlog_uuid' => $this->selectedBacklog->uuid,
                    'backlog_card_id' => $backlogCard->id,
                    'action' => 'update',
                    'table' => 'backlog_cards',
                    'data' => json_encode([
                        'title' => $backlogCard->title,
                        'description' => $backlogCard->description,
                        'approval_status' => $backlogCard->approval_status,
                        'deadline' => $backlogCard->deadline,
                        'moved_to_sprint_card_id' => $sprintCard->id,
                    ]),
                    'description' => __('logs.backlog.card_moved_sprints', [
                        'card' => $backlogCard->title, 
                        'fromBacklog' => $this->selectedBacklog->name, 
                        'toSprint' => optional($sprintCard->sprint)->name,
                        'toColumn' => optional($sprintCard->column)->name
                    ]),
                    'environment' => app()->environment(),
                    'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
                ]);

                $backlogCard->delete();
            });
        } catch (Throwable $exception) {
            report($exception);

            Toaster::error(__('board.toast.card_move_failed'));
            return;
        }

        $this->reloadBacklogState();
    }

    public function makeACopy($cardId) {
        $this->handleBacklogCardCopy($cardId);
    }

    #[On('backlogCardCopyInitiated')]
    public function handleBacklogCardCopy($cardId) {
        $card = BacklogCard::where('id', $cardId)->firstOrFail();
        if (!$card) {
            Toaster::error(__('backlog.toasts.card_not_found'));
            return;
        }
        
        $newCardIndex = BacklogCard::where('backlog_uuid', $card->backlog_uuid)
            ->max('card_index') + 1;

        $newCard = BacklogCard::create([
            'backlog_uuid' => $card->backlog_uuid,
            'title' => $card->title . ' (Copy)',
            'description' => $card->description,
            'approval_status' => $card->approval_status,
            'deadline' => $card->deadline,
            'card_index' => $newCardIndex,
        ]);

        // Copy assignees
        foreach ($card->assignees as $assignee) {
            BacklogCardAssignee::create([
                'backlog_card_id' => $newCard->id,
                'user_uuid' => $assignee->user_uuid,
            ]);
        }

        // Copy tasks
        $newTaskIndex = $card->tasks()->max('task_index') + 1;

        foreach ($card->tasks as $task) {
            $newTask = BacklogTask::create([
                'backlog_card_id' => $newCard->id,
                'description' => $task->description,
                'task_index' => $newTaskIndex,
            ]);

            // Copy task assignees
            foreach ($task->assignees as $assignee) {
                BacklogTaskAssignee::create([
                    'backlog_task_id' => $newTask->id,
                    'user_uuid' => $assignee->user_uuid,
                ]);
            }
        }

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->selectedBacklog->uuid,
            'backlog_card_id' => $card->id,
            'action' => 'create',
            'table' => 'backlog_cards',
            'data' => json_encode([
                'original_card_id' => $card->id,
                'new_card_id' => $newCard->id,
                'title' => $newCard->title,
                'description' => $newCard->description,
                'approval_status' => $newCard->approval_status,
                'deadline' => $newCard->deadline,
            ]),
            'description' => __('logs.backlog.card_copied', [
                'card' => $card->title, 
                'backlog' => $this->selectedBacklog->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        $this->reloadBacklog();
    }

    #[On('taskConvertToBacklogCardInitiated')]
    public function handleTaskConvertToBacklogCard($taskId) {
        $task = BacklogTask::where('id', $taskId)->firstOrFail();
        if (!$task) {
            Toaster::error(__('backlog.toasts.task_not_found'));
            return;
        }

        // Create a new BacklogCard from the task
        $newCardIndex = BacklogCard::where('backlog_uuid', $task->card->backlog_uuid)
            ->max('card_index') + 1;

        $newCard = BacklogCard::create([
            'backlog_uuid' => $task->card->backlog_uuid,
            'title' => $task->description,
            'description' => '',
            'approval_status' => 'None',
            'card_index' => $newCardIndex,
        ]);

        // Copy assignees from the task to the new card
        foreach ($task->assignees as $assignee) {
            BacklogCardAssignee::create([
                'backlog_card_id' => $newCard->id,
                'user_uuid' => $assignee->user_uuid,
            ]);
        }

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->selectedBacklog->uuid,
            'backlog_card_id' => $newCard->id,
            'action' => 'create',
            'table' => 'backlog_cards',
            'data' => json_encode([
                'original_task_id' => $task->id,
                'new_card_id' => $newCard->id,
                'title' => $newCard->title,
                'description' => $newCard->description,
                'approval_status' => $newCard->approval_status,
                'deadline' => $newCard->deadline,
            ]),
            'description' => __('logs.backlog.card_created_from_task', [
                'task' => $task->id,
                'backlog' => $this->selectedBacklog->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        // Delete the original task
        $task->delete();

        $this->reloadBacklog();
    }

    #[On('backlogTaskDeleteInitiated')]
    public function handleBacklogTaskDelete($taskId) {
        $this->taskToModify = BacklogTask::where('id', $taskId)->first();
        $this->showDeleteTaskModal = true;
    }

    public function confirmDeleteTask() {
        if (!$this->taskToModify) {
            Toaster::error(__('backlog.toasts.task_not_found'));
            $this->showDeleteTaskModal = false;
            return;
        }

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->selectedBacklog->uuid,
            'backlog_card_id' => $this->taskToModify->backlog_card_id,
            'action' => 'delete',
            'table' => 'backlog_tasks',
            'data' => json_encode([
                'description' => $this->taskToModify->description,
                'status' => $this->taskToModify->status,
                'task_index' => $this->taskToModify->task_index,
            ]),
            'description' => __('logs.backlog.task_deleted', [
                'task' => $this->taskToModify->description, 
                'card' => optional($this->taskToModify->card)->title,
                'backlog' => optional($this->taskToModify->card->backlog)->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        $this->taskToModify->delete();
        $this->reset(['taskToModify', 'showDeleteTaskModal']);

        $this->reloadBacklog();
    }

    public function render()
    {
        return view('livewire.projects.backlog.overview');
    }
}
