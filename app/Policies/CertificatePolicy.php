<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function view(User $user, Certificate $certificate): bool
    {
        return $user->hasRole('admin') || $user->id === $certificate->user_id;
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->hasRole('admin') || $user->id === $certificate->user_id;
    }
}
