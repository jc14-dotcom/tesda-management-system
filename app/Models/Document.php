<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['document_name', 'original_name', 'type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Document uploaded',
                'updated' => 'Document updated',
                'deleted' => 'Document deleted',
                default => $eventName,
            });
    }

    protected $fillable = [
        'user_id',
        'certificate_id',
        'document_name',
        'certificate_no',
        'issued_on',
        'valid_until',
        'type',
        'path',
        'original_name',
        'mime_type',
        'size',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'issued_on' => 'date',
        'valid_until' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
