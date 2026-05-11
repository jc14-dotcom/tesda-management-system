<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

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
