<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Player;

class Camp extends Model
{
	use HasFactory;

	// Set custom primary key
	protected $primaryKey = 'camp_id';

    // Specify the table name to match the database
    protected $table = 'Camps';

	// Allow mass assignment for these fields
	protected $fillable = ['camp_name', 'start_date', 'end_date'];

	protected $casts = [
		'start_date' => 'date',
		'end_date' => 'date',
	];

	// A camp can have many players
	public function players()
	{
		return $this->belongsToMany(Player::class, 'Player_Camp');
	}
    
	public function coaches()
	{
		return $this->belongsToMany(Coach::class, 'Coach_Camp');
	}
}