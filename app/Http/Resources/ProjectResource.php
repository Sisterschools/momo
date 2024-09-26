<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'school_1' => new SchoolResource($this->whenLoaded('school1')),
            'school_2' => new SchoolResource($this->whenLoaded('school2')),
            'teachers' => TeacherResource::collection($this->whenLoaded('teachers')),
            'students' => StudentResource::collection($this->whenLoaded('students')),
            'programs' => ProgramResource::collection($this->whenLoaded('programs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}