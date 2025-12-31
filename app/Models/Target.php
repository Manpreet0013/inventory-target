<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    // Allow mass assignment for these fields
    protected $fillable = [
        'product_id',
        'executive_id',
        'target_type',
        'target_value',
        'start_date',
        'end_date',
        'status',
        'parent_id',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignToExecutive($newExecutiveId)
    {
        $this->executive_id = $newExecutiveId;
        $this->save();
    }


    // Relationship: Target belongs to a Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Optional: Relationship: Target belongs to an Executive
    public function executive()
    {
        return $this->belongsTo(User::class, 'executive_id'); 
        // Assuming executives are stored in users table
    }

    public function sales()
    {
        // Only approved sales count
        return $this->hasMany(Sale::class);
    }

    public function allSales()
    {
        // All sales, for table/details
        return $this->hasMany(Sale::class);
    }

    public function achievedValue()
    {
        $ownSales = $this->sales()->sum($this->target_type === 'box' ? 'boxes_sold' : 'amount');

        $childSales = $this->children()->get()->sum(fn($c) => $c->achievedValue());

        return $ownSales + $childSales;
    }

    public function remainingValue()
    {
        return max($this->target_value - $this->achievedValue(), 0);
    }


    /** Target completed or not */
    public function isComplete()
    {
        return $this->achievedValue() >= $this->target_value;
    }
    public function assignments()
    {
        return $this->hasMany(TargetAssignment::class);
    }

    public function parent()
    {
        return $this->belongsTo(Target::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Target::class, 'parent_id');
    }
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function reject()
    {
        // Reject this target
        $this->update(['status' => 'rejected']);

        // If parent target → reject all children
        if ($this->parent_id === null) {
            $this->children()->update(['status' => 'rejected']);
        }
    }
    public function assignedByAdmin()
    {
        return $this->parent_id === null
            ? $this->target_value
            : $this->parent->target_value;
    }
    public function splitValue()
    {
        return $this->children()
            ->where('status', 'accepted')
            ->sum('target_value');
    }
    public function canSplit()
    {
        return $this->parent_id === null && $this->remainingValue() > 0;
    }

    public function allSalesRecursive()
    {
        // Include own sales
        $sales = $this->sales()->get();

        // Include sales from children targets
        foreach ($this->children()->with('sales')->get() as $child) {
            $sales = $sales->merge($child->allSalesRecursive());
        }

        return $sales;
    }


}   
