<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Profile extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'first_name', 'last_name', 'middle_name', 'suffix',
                'date_of_birth', 'gender', 'contact_number', 'address',
                'employment_status', 'position_title', 'region', 'branch',
                'tesda_registry_number', 'qualification_title',
                'trainer_qualification_titles', 'assessor_qualification_titles', 'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Profile created',
                'updated' => 'Profile updated',
                'deleted' => 'Profile deleted',
                default => $eventName,
            });
    }

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'gender',
        'contact_number',
        'address',
        'profile_photo_path',
        'company_id',
        'position_title',
        'position_roles',
        'employment_status',
        'status',
        'date_hired',
        'region',
        'branch',
        'tesda_registry_number',
        'qualification_title',
        'trainer_qualification_titles',
        'assessor_qualification_titles',
        'remarks',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'date_hired'     => 'date',
        'position_roles'               => 'array',
        'trainer_qualification_titles'  => 'array',
        'assessor_qualification_titles' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        return route('profile.photo', ['user' => $this->user_id]);
    }
}
