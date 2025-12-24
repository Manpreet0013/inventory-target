<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'target_id',
        'boxes_sold',
        'amount',       
        'party_name',
        'sale_date',
        'status',
        'executive_id'  
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
