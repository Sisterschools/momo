<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'photo' => $this->photo,
            'address' => $this->address,
            'description' => $this->description,
            'phone_number' => $this->phone_number,
            'website' => $this->website,
            'founding_year' => $this->founding_year,
            'student_capacity' => $this->student_capacity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}