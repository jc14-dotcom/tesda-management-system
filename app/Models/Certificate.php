<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    public const TYPE_LABELS = [
        'nc_i' => 'NC I',
        'nc_ii' => 'NC II',
        'nc_iii' => 'NC III',
        'nc_iv' => 'NC IV',
        'nttc' => 'NTTC',
        'trainer' => 'Trainer',
        'assessor' => 'Assessor',
        'other' => 'Other',
    ];

    protected $fillable = [
        'user_id',
        'certificate_name',
        'certificate_type',
        'qualification_title',
        'certificate_number',
        'issued_by',
        'issue_date',
        'expiration_date',
        'status',
        'verification_status',
        'verified_by',
        'verified_at',
        'last_notified_at',
        'notified_days',
        'notification_count',
        'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
        'verified_at' => 'datetime',
        'last_notified_at' => 'datetime',
        'notified_days' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getCertificateTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->certificate_type] ?? Str::headline($this->certificate_type ?? '');
    }
}
