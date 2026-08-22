<?php

namespace App\Services;

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class DoctorProfileService
{
    /**
     * List all doctor users with their profiles eager loaded.
     */
    public function listDoctors(): Collection
    {
        return User::role('doctor')->with('doctorProfile')->get();
    }

    /**
     * Paginate all doctor profiles.
     */
    public function listProfiles(): LengthAwarePaginator
    {
        return DoctorProfile::paginate();
    }

    /**
     * List all active (approved) doctor profiles, paginated and eager loaded.
     */
    public function listActive(): LengthAwarePaginator
    {
        return DoctorProfile::with(['user', 'specialty'])
            ->where('status', 'active')
            ->paginate();
    }

    /**
     * Show a doctor profile with its user relation loaded.
     */
    public function show(DoctorProfile $profile): DoctorProfile
    {
        return $profile->load('user');
    }

    /**
     * Update a doctor profile (admin edit).
     */
    public function update(DoctorProfile $profile, array $data): DoctorProfile
    {
        $profile->update($data);

        return $profile;
    }

    /**
     * Approve / disable a doctor profile (admin).
     */
    public function updateStatus(DoctorProfile $profile, string $status): DoctorProfile
    {
        $profile->update(['status' => $status]);

        return $profile;
    }

    /**
     * Update a doctor's own profile, handling image upload/removal.
     */
    public function updateOwn(DoctorProfile $profile, array $data, ?UploadedFile $image = null): DoctorProfile
    {
        if ($image) {
            if ($profile->image) {
                Storage::disk('local')->delete($profile->image);
            }

            $data['image'] = $image->store('doctors', 'local');
        }

        $profile->update($data);

        return $profile;
    }
}
