<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SchoolPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'school']);
    }

    public function view(User $user, School $school): bool
    {
        return $user->role === 'admin' || ($user->role === 'school' && $user->profile->id === $school->id);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, School $school): bool
    {
        return $user->role === 'admin' || ($user->role === 'school' && $user->profile->id === $school->id);
    }

    public function delete(User $user, School $school): bool
    {
        return $user->role === 'admin';
    }
}