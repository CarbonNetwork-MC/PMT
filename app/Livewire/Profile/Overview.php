<?php

namespace App\Livewire\Profile;
    
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class Overview extends Component
{
    use WithFileUploads;

    public $user;

    public $username;
    public $email;
    public $profileImage;
    public $currentProfileImage;

    public $sessions = [];

    public $currentPassword = '';
    public $newPassword = '';
    public $newPassword_confirmation = '';

    public $currentPasswordDelete = '';
    public $showAccountDeletionModal = false;

    public function mount() {
        $this->user = auth()->user();

        $this->username = $this->user->name;
        $this->email = $this->user->email;
        $this->currentProfileImage = $this->user->profile_photo_path
            ? asset('storage/' . $this->user->profile_photo_path)
            : null;

        $this->sessions = $this->user->sessions()->orderBy('last_activity', 'desc')->get();
    }

    public function updatedProfileImage() {
        $this->validate([
            'profileImage' => 'image|max:2048', // 2MB Max
        ]);

        $path = $this->profileImage->store(path: 'profile-images', options: 'public');

        // Delete old profile image if exists
        if ($this->user->profile_photo_path) {
            Storage::disk('public')->delete($this->user->profile_photo_path);
        }

        // Update user's profile image path
        $this->user->profile_photo_path = $path;
        $this->user->save();

        $this->currentProfileImage = asset('storage/' . $path);

        Toaster::success(__('profile.toasts.profile_image_updated'));
    }

    public function savePersonalInformation() {
        $this->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->uuid, 'uuid'),
            ],
        ]);

        $this->user->name = $this->username;
        $this->user->email = $this->email;
        $this->user->save();

        $this->dispatch('reloadUser');

        Toaster::success(__('profile.toasts.profile_updated'));
    }

    public function updatePassword() {
        $this->validate([
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($this->currentPassword, $this->user->password)) {
            Toaster::error(__('profile.toasts.current_password_incorrect'));
            return;
        }

        $this->user->password = Hash::make($this->newPassword);
        $this->user->save();

        // Clear password fields
        $this->reset(['currentPassword', 'newPassword', 'newPassword_confirmation']);

        Toaster::success(__('profile.toasts.password_updated'));
    }

    public function logoutOtherSessions() {
        $this->user->sessions()->where('id', '!=', session()->getId())->delete();

        Toaster::success(__('profile.toasts.sessions_logged_out'));
    }

    public function deleteAccount() {
        $ownedProjects = $this->user->ownedProjects()->count();
        if ($ownedProjects > 0) {
            Toaster::error(__('profile.messages.delete_account_owner'));
            $this->reset(['currentPasswordDelete', 'showAccountDeletionModal']);
            return;
        }

        $this->validate([
            'currentPasswordDelete' => ['required', 'string'],
        ]);

        if (!Hash::check($this->currentPasswordDelete, $this->user->password)) {
            Toaster::error(__('profile.toasts.current_password_incorrect'));
            return;
        }

        $this->user->syncRoles([]);
        $this->user->syncPermissions([]);

        $this->user->delete();

        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->success(__('profile.toasts.account_deleted'));
    }

    public function cancelAccountDeletion() {
        $this->reset(['currentPasswordDelete', 'showAccountDeletionModal']);
    }

    public function render()
    {
        return view('livewire.profile.overview');
    }
}
