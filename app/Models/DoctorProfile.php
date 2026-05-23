<?php

namespace App\Models;

use App\Enums\DoctorStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorProfile extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty_id',
        'status',
        'image',
        'bio',
        'mobile',
        'medical_code',
        'address',
        'working_hours',
    ];  

    protected $casts = [
        'status' => DoctorStatus::class,
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialty() : BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
