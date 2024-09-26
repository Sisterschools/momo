<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Student;

use App\Observers\SchoolObserver;
use App\Observers\TeacherObserver;
use App\Observers\StudentObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        School::observe(SchoolObserver::class);
        Teacher::observe(TeacherObserver::class);
        Student::observe(StudentObserver::class);



    }
}
