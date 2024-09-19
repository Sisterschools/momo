<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    use HasFactory;

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

    // Scope a query to search for schools by title, address, or phone number.
    public function scopeSearch(Builder $query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
