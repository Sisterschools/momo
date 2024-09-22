<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Program extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('is_completed', 'completed_at')
            ->withTimestamps();
    }

    public function projectStudents(Project $project)
    {
        return $this->belongsToMany(Student::class, 'project_program_student')
            ->withPivot('project_id')
            ->wherePivot('project_id', $project->id);
    }

    // Scope a query to search for programs by name.
    public function scopeSearch(Builder $query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

}