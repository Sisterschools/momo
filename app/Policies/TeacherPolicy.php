<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeacherPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher']);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Teacher $teacher): bool
    {
        return $user->role === 'admin' || ($user->role === 'teacher' && $user->profile->id === $teacher->id);

    }

    /**
     * Determine whether the user can create models.
     */

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $user->role === 'admin' || ($user->role === 'teacher' && $user->profile->id === $teacher->id);
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->role === 'admin';
    }

}

