<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'photo',
        'address',
        'description',
        'phone_number',
        'website',
        'founding_year',
        'student_capacity',
    ];

    protected $casts = [
        'founding_year' => 'integer',
        'student_capacity' => 'integer',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($school) {
            // Delete the associated user if exists
            $school->user->delete();

        });
    }
}