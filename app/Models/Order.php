<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'Orders';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'Order_ID';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'Parent_ID',
        'Camp_ID',
        'Order_Date',
        'Item_Amount',
        'Item_Amount_Paid',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'Order_Date' => 'date',
        'Item_Amount' => 'decimal:2',
        'Item_Amount_Paid' => 'decimal:2',
    ];

    /**
     * Get the parent that owns the order.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'Parent_ID', 'Parent_ID');
    }

    /**
     * Get the camp that owns the order.
     */
    public function camp(): BelongsTo
    {
        return $this->belongsTo(Camp::class, 'Camp_ID', 'Camp_ID');
    }

    /**
     * Calculate the remaining amount to be paid.
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->Item_Amount - $this->Item_Amount_Paid;
    }

    /**
     * Check if the order is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->Item_Amount_Paid >= $this->Item_Amount;
    }

    /**
     * Check if the order is partially paid.
     */
    public function isPartiallyPaid(): bool
    {
        return $this->Item_Amount_Paid > 0 && $this->Item_Amount_Paid < $this->Item_Amount;
    }

    /**
     * Get the payment status of the order.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->isFullyPaid()) {
            return 'paid';
        } elseif ($this->isPartiallyPaid()) {
            return 'partial';
        } else {
            return 'pending';
        }
    }

    /**
     * Scope a query to only include paid orders.
     */
    public function scopePaid($query)
    {
        return $query->whereRaw('Item_Amount_Paid >= Item_Amount');
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('Item_Amount_Paid', '=', 0)
                     ->orWhereNull('Item_Amount_Paid');
    }

    /**
     * Scope a query to only include partially paid orders.
     */
    public function scopePartiallyPaid($query)
    {
        return $query->where('Item_Amount_Paid', '>', 0)
                     ->whereRaw('Item_Amount_Paid < Item_Amount');
    }
}
