<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'composition',
        'type',
        'image',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'datetime', // no format string here
        'notified_at' => 'datetime', // for optional notification
    ];

    public function targets()
    {
        return $this->hasMany(Target::class);
    }

    /** Product is expired or not */
    public function isExpired()
    {
        return $this->type === 'expiry'
            && $this->expiry_date
            && $this->expiry_date->isPast(); // ✅ FIXED
    }

    /** Check if all targets are completed */
    public function isTargetCompleted()
    {
        foreach ($this->targets as $target) {
            if (! $target->isComplete()) {
                return false;
            }
        }

        return $this->targets->isNotEmpty();
    }
}
