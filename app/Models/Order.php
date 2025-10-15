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
        'Player_ID',
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
     * Get the player that this order is for.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'Player_ID', 'Player_ID');
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

    /**
     * Add payment to the order
     */
    public function addPayment(float $amount): bool
    {
        $currentPaid = $this->Item_Amount_Paid ?? 0;
        $newTotal = $currentPaid + $amount;
        
        // Don't allow overpayment
        if ($newTotal > $this->Item_Amount) {
            $newTotal = $this->Item_Amount;
        }
        
        return $this->update(['Item_Amount_Paid' => $newTotal]);
    }

    /**
     * Get payment history for this order (if you implement a payments table later)
     */
    public function getPaymentHistoryAttribute(): array
    {
        // For now, return basic info. Later you could relate to a payments table
        return [
            'order_id' => $this->Order_ID,
            'player_id' => $this->Player_ID,
            'player_name' => $this->player ? $this->player->Camper_FirstName . ' ' . $this->player->Camper_LastName : null,
            'parent_name' => $this->parent ? $this->parent->Parent_FirstName . ' ' . $this->parent->Parent_LastName : null,
            'camp_name' => $this->camp ? $this->camp->Camp_Name : null,
            'total_amount' => $this->Item_Amount,
            'amount_paid' => $this->Item_Amount_Paid,
            'remaining_amount' => $this->remaining_amount,
            'status' => $this->payment_status,
            'order_date' => $this->Order_Date,
        ];
    }

    /**
     * Get a formatted description of this order
     */
    public function getDescriptionAttribute(): string
    {
        $playerName = $this->player ? $this->player->Camper_FirstName . ' ' . $this->player->Camper_LastName : 'Unknown Player';
        $campName = $this->camp ? $this->camp->Camp_Name : 'Unknown Camp';
        
        return "Order #{$this->Order_ID} - {$playerName} for {$campName}";
    }

    /**
     * Scope to find orders for a specific player
     */
    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('Player_ID', $playerId);
    }

    /**
     * Scope to find orders for a specific camp
     */
    public function scopeForCamp($query, $campId)
    {
        return $query->where('Camp_ID', $campId);
    }
}