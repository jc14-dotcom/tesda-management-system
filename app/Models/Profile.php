<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'middle_name',
        'suffix',
        'date_of_birth',
        'gender',
        'contact_number',
        'address',
        'profile_photo_path',
        'company_id',
        'position_title',
        'employment_status',
        'status',
        'date_hired',
        'region',
        'branch',
        'tesda_registry_number',
        'qualification_title',
        'remarks',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_hired' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path ? Storage::url($this->profile_photo_path) : null;
    }
}
