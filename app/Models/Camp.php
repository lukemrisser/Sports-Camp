<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
	use HasFactory;

	// Set custom primary key
	protected $primaryKey = 'Camp_ID';

    // Specify the table name to match the database
    protected $table = 'Camps';

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
		return $this->belongsToMany(Player::class, 'Player_Camp');
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
}
