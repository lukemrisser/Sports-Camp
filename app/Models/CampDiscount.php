<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Camp;
use App\Models\PromoCode;

class CampDiscount extends Model
{
    use HasFactory;

    protected $primaryKey = 'Discount_ID';

    protected $table = 'Camp_Discount';

    public $timestamps = false;

    // Allow mass assignment for these fields
    protected $fillable = [
        'Camp_ID',
        'Discount_Amount',
        'Discount_Date'
    ];

    // Cast Discount_Amount as decimal and Discount_Date as date
    protected $casts = [
        'Discount_Amount' => 'decimal:2',
        'Discount_Date' => 'date'
    ];

    // A discount belongs to a camp
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

    /**
     * Find a promo code by code string for a specific camp
     */
    public static function findPromoCodeForCamp($campId, $promoCode)
    {
        return PromoCode::findValidPromoCodeForCamp($campId, $promoCode);
    }

    public static function isPromoCodeValid($discount)
    {
        if($discount->Expiration_Date === null) {
            return true;
        }
        return $discount->Expiration_Date >= now();
    }
}
