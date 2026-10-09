<?php

namespace App\Policies;

use App\Models\ProgramVerification;
use App\Models\User;

/**
 * Doğrulama kayıtları: panele girebilen herkes görebilir; yalnız tam yetkili admin (isFullAdmin) yazabilir.
 * Editör rolü içerik moderasyonu içindir, resmî kaynak doğrulaması yapamaz.
 */
class ProgramVerificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin === true || $user->is_editor === true;
    }

    public function view(User $user, ProgramVerification $v): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isFullAdmin();
    }

    public function update(User $user, ProgramVerification $v): bool
    {
        return $user->isFullAdmin();
    }

    public function delete(User $user, ProgramVerification $v): bool
    {
        return $user->isFullAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isFullAdmin();
    }
}
