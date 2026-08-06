<?php

namespace App\Livewire\Admin\InviteCodes;

use App\Models\InviteCode;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Overview extends Component
{
    public $inviteCodes;
    public string $code = '';
    public int $codeLength = 48;

    public bool $showCreateInviteCodeModal = false;
    public bool $showDeleteInviteCodeModal = false;
    public ?InviteCode $inviteCodeToDelete = null;

    public function mount() {
        $this->inviteCodes = InviteCode::orderBy('is_used', 'asc')->get();
    }

    public function generateInviteCode() {
        $this->code = $this->generateCode($this->codeLength);
    }

    public function createInviteCode() {
        InviteCode::create(['code' => $this->code]);
        $this->inviteCodes = InviteCode::orderBy('is_used', 'asc')->get();
        $this->code = '';
        $this->showCreateInviteCodeModal = false;

        Toaster::success(__('admin.toasts.invite_codes.created'));
    }

    public function cancelCreation() {
        $this->code = '';
        $this->showCreateInviteCodeModal = false;
    }

    public function deleteInviteCode(InviteCode $inviteCode) {
        $this->inviteCodeToDelete = $inviteCode;
        $this->showDeleteInviteCodeModal = true;
    }

    public function confirmDeleteInviteCode() {
        if ($this->inviteCodeToDelete) {
            $this->inviteCodeToDelete->delete();
            $this->inviteCodes = InviteCode::orderBy('is_used', 'asc')->get();
            $this->showDeleteInviteCodeModal = false;
            $this->inviteCodeToDelete = null;

            Toaster::success(__('admin.toasts.invite_codes.deleted'));
        }
    }

    private function generateCode($length = 10) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    public function render()
    {
        return view('livewire.admin.invite-codes.overview');
    }
}
