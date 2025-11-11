<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampDiscount;
use App\Models\Player;
use App\Models\Coach;
use App\Models\Sport;

class Camp extends Model
{
    use HasFactory;

	protected $primaryKey = 'Camp_ID';

    protected $table = 'Camps';

	public $timestamps = false;

	protected $fillable = [
		'Sport_ID',
		'Camp_Name',
		'Description',
		'Start_Date',
		'End_Date',
		'Registration_Open',
		'Registration_Close',
		'Price',
		'Camp_Gender',
		'Age_Min',
		'Age_Max'
	];

	// Cast start_date and end_date as dates
	protected $casts = [
		'Start_Date' => 'date',
		'End_Date' => 'date',
		'Registration_Open' => 'date',
		'Registration_Close' => 'date',
	];

	// A camp belongs to one sport
	public function sport()
	{
		return $this->belongsTo(Sport::class, 'Sport_ID', 'Sport_ID');
	}

	// A camp can have many players
	public function players()
	{
		return $this->belongsToMany(Player::class, 'Player_Camp', 'Camp_ID', 'Player_ID');
	}

	public function coaches()
	{
		return $this->belongsToMany(
			Coach::class,
			'Coach_Camp',   // Pivot table name
			'Camp_ID',      // Foreign key on pivot table for this model
			'Coach_ID'      // Foreign key on pivot table for the related model
		);
	}
	public function discounts()
	{
		return $this->hasMany(CampDiscount::class, 'Camp_ID', 'Camp_ID');
	}

	// Get active discounts for this camp
	public function activeDiscounts()
	{
		return $this->discounts()
					->where('Discount_Date', '>=', now());
	}

	// Get the best available discount for this camp
	public function getBestDiscount()
	{
		return $this->activeDiscounts()
					->orderBy('Discount_Amount', 'desc')
					->first();
	}

	// Calculate discounted price for this camp
	public function getDiscountedPrice($originalPrice)
	{
		$discount = $this->getBestDiscount();

		if (!$discount) {
			return $originalPrice;
		}

		return $originalPrice - $discount->Discount_Amount;
	}

	// Scope to get camps that are currently accepting registrations
	public function scopeAcceptingRegistrations($query)
    {
        return $query->where('Registration_Open', '<=', now())
                    ->where('Registration_Close', '>=', now());
    }

	// Static method to get all camps currently accepting registrations
	public static function getAvailableForRegistration()
	{
		return static::acceptingRegistrations()->get();
	}

	// Check if this specific camp is currently accepting registrations
	public function isAcceptingRegistrations()
	{
		$today = now()->toDateString();

		return $this->Registration_Open <= $today && $this->Registration_Close >= $today;
	}
	public function getSportAttribute()
	{
		return $this->sport->Sport_Name ?? null; // or whatever the name field is called
	}
}
