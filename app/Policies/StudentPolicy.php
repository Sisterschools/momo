<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Student $student): bool
    {
        return $user->role === 'admin' || ($user->role === 'student' && $user->profile->id === $student->id);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->role === 'admin';
    }

}
