<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_id',
        'executive_id',
        'status',
        'accepted_value',
    ];

    public function target()
    {
        return $this->belongsTo(Target::class);
    }

    public function executive()
    {
        return $this->belongsTo(User::class, 'executive_id');
    }
}
