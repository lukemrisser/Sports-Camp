<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampDiscount extends Model
{
    use HasFactory;

    protected $table = 'camp_discount';
    protected $primaryKey = 'Discount_ID';
    public $timestamps = false;

    protected $fillable = [
        'Camp_ID',
        'Discount_Date',
        'Discount_Amount',
        'Active'
    ];

    protected $casts = [
        'Discount_Date' => 'date',
        'Discount_Amount' => 'decimal:2',
        'Active' => 'boolean'
    ];

    /**
     * Get the camp that owns the discount
     */
    public function camp()
    {
        return $this->belongsTo(Camp::class, 'Camp_ID', 'Camp_ID');
    }

    /**
     * Check if discount is currently active
     */
    public function isActive()
    {
        return $this->Active && $this->Discount_Date >= now();
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAttribute()
    {
        return '$' . number_format((float) $this->Discount_Amount, 2);
    }

    /**
     * Apply discount to an amount
     * 
     * @param int $amount Amount in cents
     * @return int Discounted amount in cents
     */
    public function applyDiscount($amount)
    {
        if (!$this->isActive()) {
            return $amount;
        }

        // Dollar amount discount - convert to cents for calculation
        $discountInCents = $this->Discount_Amount * 100;
        return max(0, $amount - $discountInCents);
    }

    /**
     * Scope to get active discounts only
     */
    public function scopeActive($query)
    {
        return $query->where('Active', true)
                    ->where('Discount_Date', '>=', now());
    }

    /**
     * Scope to get discounts for a specific camp
     */
    public function scopeForCamp($query, $campId)
    {
        return $query->where('Camp_ID', $campId);
    }

    /**
     * Get the longest-lasting available discount for a camp
     */
    public static function getLongestLastingDiscountForCamp($campId)
    {
        return self::active()
                   ->forCamp($campId)
                   ->orderBy('Discount_Date', 'desc')
                   ->first();
    }

    /**
     * Get all active discounts for a camp
     */
    public static function getActiveDiscountsForCamp($campId)
    {
        return self::active()
                   ->forCamp($campId)
                   ->orderBy('Discount_Amount', 'desc')
                   ->get();
    }
}
