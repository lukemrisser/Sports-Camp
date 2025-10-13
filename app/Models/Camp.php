<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Player;
use App\Models\Coach;

class Camp extends Model
{
    use HasFactory;

	protected $primaryKey = 'Camp_ID';

    protected $table = 'Camps';

	public $timestamps = false;

	// Allow mass assignment for these fields
	protected $fillable = ['Camp_Name', 'Start_Date', 'End_Date'];

	// Cast start_date and end_date as dates
	protected $casts = [
		'Start_Date' => 'date',
		'End_Date' => 'date',
	];

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

	// A camp can have many discounts
	public function discounts()
	{
		return $this->hasMany(CampDiscount::class, 'Camp_ID', 'Camp_ID');
	}

	// Get active discounts for this camp
	public function activeDiscounts()
	{
		return $this->discounts()->active();
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

		return $discount->applyDiscount($originalPrice);
	}
}
