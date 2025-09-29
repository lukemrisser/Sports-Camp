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

	protected $casts = [
		'Start_Date' => 'date',
		'End_Date' => 'date'
	];
    
	// Many-to-many relationship with players
    public function players() {
        return $this->belongsToMany(Player::class, 'Player_Camp', 'Camp_ID', 'Player_ID');
    }

	// Many-to-many relationship with coaches
	public function coaches() {
		return $this->belongsToMany(Coach::class, 'Coach_Camp', 'Camp_ID', 'Coach_ID');
	}
}