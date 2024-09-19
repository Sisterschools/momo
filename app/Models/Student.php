<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
