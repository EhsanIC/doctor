<?php

namespace App\Http\Middleware;

use App\Enums\DoctorStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoctorActive
{
    /**
     * Block doctors whose profile is not active (pending / disabled).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->user()?->doctorProfile;

        if (! $profile || $profile->status !== DoctorStatus::ACTIVE) {
            abort(403, 'Your account is pending approval or disabled.');
        }

        return $next($request);
    }
}
