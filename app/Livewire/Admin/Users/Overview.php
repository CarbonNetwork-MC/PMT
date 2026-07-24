<?php

namespace App\Livewire\Admin\Users;

use App\Models\DeletedUser;
use App\Models\Log;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class Overview extends Component
{
    use WithPagination;
    public $search = '';
    public $perPage = 10;

    public $errorMessage = null;

    public $userToDelete;
    public bool $showDeleteUserModal = false;

    public array $projectsRequiringAction = [];
    public array $projectActions = [];
    public array $projectTransferUsers = [];
    public array $eligibleTransferUsers = [];

    protected function findAutomaticNewOwner($project): ?ProjectMember {
        $admin = ProjectMember::query()
            ->where('project_uuid', $project->uuid)
            ->where('user_uuid', '!=', $this->userToDelete->uuid)
            ->whereHas('role', function ($query) {
                $query->where('slug', 'admin');
            })
            ->first();

        if ($admin) return $admin;

        return ProjectMember::query()
            ->where('project_uuid', $project->uuid)
            ->where('user_uuid', '!=', $this->userToDelete->uuid)
            ->whereHas('role', function ($query) {
                $query->where('slug', 'member');
            })
            ->first();
    }

    public function deleteUser(string $uuid): void {
        $this->resetDeleteUserState();

        $this->userToDelete = User::where('uuid', $uuid)->firstOrFail();

        if ($this->userToDelete->hasRole('Superadmin')) {
            Toaster::error(__('admin.toasts.users.user_is_superadmin', [
                'user' => $this->userToDelete->name,
            ]));

            $this->resetDeleteUserState();
            return;
        }

        $ownedProjects = $this->userToDelete->ownedProjects()->get();

        foreach ($ownedProjects as $project) {
            if ($this->findAutomaticNewOwner($project)) {
                continue;
            }

            $this->projectsRequiringAction[] = [
                'uuid' => $project->uuid,
                'name' => $project->name,
            ];

            $this->projectActions[$project->uuid] = '';
            $this->projectTransferUsers[$project->uuid] = '';
        }

        $this->eligibleTransferUsers = User::query()
            ->where('uuid', '!=', $this->userToDelete->uuid)
            ->where('uuid', '!=', Auth::user()->uuid)
            ->orderBy('name')
            ->get(['uuid', 'name', 'email'])
            ->map(fn (User $user) => [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->all();

        $this->showDeleteUserModal = true;
    }

    public function confirmDeleteUser(): void {
        if (!$this->userToDelete) return;

        if ($this->userToDelete->hasRole('Superadmin')) {
            Toaster::error(__('admin.toasts.users.user_is_superadmin', [
                'user' => $this->userToDelete->name,
            ]));

            $this->resetDeleteUserState();
            return;
        }

        $this->validateProjectActions();

        $profileImagePath = $this->userToDelete->profile_image_path;
        $deletedUserName = $this->userToDelete->name;

        DB::transaction(function (): void {
            $ownedProjects = $this->userToDelete
                ->ownedProjects()
                ->lockForUpdate()
                ->get();

            foreach ($ownedProjects as $project) {
                $automaticNewOwner = $this->findAutomaticNewOwner($project);

                if ($automaticNewOwner) {
                    $this->transferProject(
                        project: $project,
                        newOwnerUuid: $automaticNewOwner->user_uuid
                    );

                    continue;
                }

                $this->handleSelectedProjectAction($project);
            }

            DeletedUser::create([
                'uuid' => $this->userToDelete->uuid,
                'name' => $this->userToDelete->name,
            ]);

            $this->userToDelete->syncRoles([]);
            $this->userToDelete->syncPermissions([]);

            $this->userToDelete->delete();
        });

        if ($profileImagePath) {
            Storage::disk('public')->delete($profileImagePath);
        }

        Toaster::success(__('admin.toasts.users.user_deleted', [
            'user' => $deletedUserName,
        ]));

        $this->resetDeleteUserState();
    }

    protected function validateProjectActions(): void {
        $errors = [];

        foreach ($this->projectsRequiringAction as $project) {
            $projectUuid = $project['uuid'];
            $action = $this->projectActions[$projectUuid] ?? null;

            if (!in_array(
                $action,
                ['transfer_user', 'transfer_me', 'archive', 'delete'],
                true
            )) {
                $errors["projectActions.$projectUuid"] = __('admin.validation.users.project_action_required');
                continue;
            }

            if ($action === 'transfer_user') {
                $newOwnerUuid = $this->projectTransferUsers[$projectUuid] ?? null;

                $validUserExists = User::query()
                    ->where('uuid', $newOwnerUuid)
                    ->where('uuid', '!=', $this->userToDelete->uuid)
                    ->exists();

                if (!$validUserExists) {
                    $errors["projectTransferUsers.$projectUuid"] = __('admin.validation.users.project_owner_required');
                }
            }

            if (
                $action === 'transfer_me'
                && Auth::user()->uuid === $this->userToDelete->uuid
            ) {
                $errors["projectActions.$projectUuid"] = __('admin.validation.users.cannot_transfer_to_deleted_user');
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function handleSelectedProjectAction(Project $project): void {
        $action = $this->projectActions[$project->uuid];

        match ($action) {
            'transfer_user' => $this->transferProject(
                project: $project,
                newOwnerUuid: $this->projectTransferUsers[$project->uuid],
            ),

            'transfer_me' => $this->transferProject(
                project: $project,
                newOwnerUuid: Auth::user()->uuid,
            ),

            'archive' => $this->archiveProject($project),

            'delete' => $this->deleteProject($project),

            default => throw ValidationException::withMessages([
                "projectActions.{$project->uuid}" =>
                    __('admin.validation.users.project_action.required')
            ]),
        };
    }

    protected function transferProject($project, string $newOwnerUuid): void {
        $project->update([
            'owner_uuid' => $newOwnerUuid,
        ]);

        $projectMember = ProjectMember::where('user_uuid', $newOwnerUuid)->first();
        if ($projectMember) $projectMember->delete();

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $project->uuid,
            'action' => 'update',
            'table' => 'projects',
            'data' => json_encode([
                'owner_uuid' => $newOwnerUuid,
            ]),
            'description' => __('logs.project.owner_changed', [
                'oldOwner' => $this->userToDelete->name,
                'newOwner' => User::where('uuid', $newOwnerUuid)->first()->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => true,
        ]);
    }

    protected function archiveProject($project): void {
        $project->update([
            'is_archived' => true,
            'archived_by' => Auth::user()->uuid,
            'archived_at' => now(),
        ]);

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $project->uuid,
            'action' => 'update',
            'table' => 'projects',
            'data' => json_encode([
                'is_archived' => true,
                'archived_by' => Auth::user()->uuid,
                'archived_at' => now(),
            ]),
            'description' => __('logs.project.archived', [
                'project' => $project->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => true,
        ]);
    }

    protected function deleteProject($project): void {
        $project->delete();
    }

    public function closeDeleteUserModal(): void {
        $this->resetDeleteUserState();
    }

    protected function resetDeleteUserState(): void {
        $this->reset([
            'userToDelete',
            'showDeleteUserModal',
            'errorMessage',
            'projectsRequiringAction',
            'projectActions',
            'projectTransferUsers',
            'eligibleTransferUsers',
        ]);

        $this->resetValidation();
    }

    public function getAllProjectsConfiguredProperty(): bool {
        foreach ($this->projectsRequiringAction as $project) {
            $projectUuid = $project['uuid'];
            $action = $this->projectActions[$projectUuid] ?? '';

            if (!in_array(
                $action,
                ['transfer_user', 'transfer_me', 'archive', 'delete'],
                true
            )) {
                return false;
            }

            if (
                $action === 'transfer_user'
                && empty($this->projectTransferUsers[$projectUuid] ?? null)
            ) {
                return false;
            }
        }

        return true;
    }

    public function render()
    {
        $users = User::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);

        return view('livewire.admin.users.overview', [
            'users' => $users,
        ]);
    }
}
