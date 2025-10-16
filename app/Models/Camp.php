<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampDiscount;
use App\Models\Player;
use App\Models\Coach;

class Camp extends Model
{
    use HasFactory;

	protected $primaryKey = 'Camp_ID';

    protected $table = 'Camps';

	public $timestamps = false;

	protected $fillable = [
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
}
