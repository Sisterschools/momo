<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    use HasFactory;

    protected $perPage = 10;
    protected $fillable = [
        'name',
        'photo',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    // Many-to-Many Relationship with School
    public function schools()
    {
        return $this->belongsToMany(School::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($student) {
            // Delete the associated user if exists
            $student->user->delete();

        });
    }

    // Scope a query to search for students by name
    public function scopeSearch(Builder $query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }


    public function projectPrograms()
    {
        return $this->belongsToMany(Program::class, 'project_program_student')
            ->withPivot('project_id');
    }

}
