<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Player;

class Camp extends Model
{
    protected $table = 'Camps';
    protected $primaryKey = 'Camp_ID';
    public $timestamps = false;

    protected $fillable = [
        'Camp_Name',
        'Start_Date',
        'End_Date'
    ];
    
    public function players() {
        return $this->belongsToMany(Player::class, 'Player_Camp', 'Camp_ID', 'Player_ID');
    }
}

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
	use HasFactory;

	// Set custom primary key
	protected $primaryKey = 'camp_id';

    // Specify the table name to match the database
    protected $table = 'Camps';

	// Allow mass assignment for these fields
	protected $fillable = ['camp_name', 'start_date', 'end_date'];

	// Cast start_date and end_date as dates
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
