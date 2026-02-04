<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $primaryKey = 'Promo_Code_ID';
    protected $table = 'Promo_Codes';
    public $timestamps = false;

    protected $fillable = [
        'Camp_ID',
        'Promo_Code',
        'Discount_Amount',
        'Expiration_Date'
    ];

    protected $casts = [
        'Discount_Amount' => 'decimal:2',
        'Expiration_Date' => 'date'
    ];

    /**
     * A promo code belongs to a camp
     */
    public function camp()
    {
        return $this->belongsTo(Camp::class, 'Camp_ID', 'Camp_ID');
    }

    /**
     * Check if promo code is currently valid
     */
    public function isValid()
    {
        return $this->Expiration_Date === null || $this->Expiration_Date >= now();
    }

    /**
     * Scope to get active promo codes only
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('Expiration_Date')
              ->orWhere('Expiration_Date', '>=', now());
        });
    }

    /**
     * Scope to get promo codes for a specific camp
     */
    public function scopeForCamp($query, $campId)
    {
        return $query->where('Camp_ID', $campId);
    }

    /**
     * Find a promo code by code string for a specific camp
     */
    public static function findValidPromoCodeForCamp($campId, $promoCode)
    {
        return self::forCamp($campId)
                   ->active()
                   ->where('Promo_Code', $promoCode)
                   ->first();
    }

    /**
     * Get all active promo codes for a camp
     */
    public static function getActivePromoCodesForCamp($campId)
    {
        return self::forCamp($campId)
                   ->active()
                   ->orderBy('Discount_Amount', 'desc')
                   ->get();
    }
}
