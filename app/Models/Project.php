<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    use HasFactory;

    protected $perPage = 10;

    protected $fillable = [
        'name',
        'description',
        'school_id_1',
        'school_id_2'
    ];

    public function school1(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id_1');
    }

    public function school2(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id_2');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class)
            ->withPivot('status', 'ready_at', 'archived_at')
            ->withTimestamps();
    }


    public function programStudents(Program $program)
    {
        return $this->belongsToMany(Student::class, 'project_program_student')
            ->withPivot('program_id')
            ->wherePivot('program_id', $program->id);
    }

    public function scopeSearch(Builder $query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
