<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $fillable = ['type', 'title'];

    public function scopeTrainer($query)
    {
        return $query->where('type', 'trainer');
    }

    public function scopeAssessor($query)
    {
        return $query->where('type', 'assessor');
    }
}
