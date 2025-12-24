<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Add your form fields here
    protected $fillable = [
        'name',
        'composition',
        'type',
        'image',
        'expiry_date',
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
            && $this->expiry_date < now()->toDateString();
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
