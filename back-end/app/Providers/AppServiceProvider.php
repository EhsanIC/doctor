<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Policies\AppointmentPolicy;
use App\Policies\DoctorProfilePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(DoctorProfile::class, DoctorProfilePolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
    }
}
