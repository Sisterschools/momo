<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'phone_number' => $this->phone_number,
            'bio' => $this->bio,
            'email' => $this->email,
            'school_ids' => $this->schools->pluck('id'), // Assuming `schools` is a relationship on `Teacher`
            // Include user data
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'created_at' => $this->user->created_at,
                'updated_at' => $this->user->updated_at,
            ] : null, // Return null if no user is associated
        ];
    }
}
