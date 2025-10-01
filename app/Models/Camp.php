<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Player;
use App\Models\Coach;

class Camp extends Model
{
    use HasFactory;

    protected $primaryKey = 'Camp_id';
    protected $table = 'Camps';
    protected $fillable = ['camp_name', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // A camp can have many players
    public function players()
    {
         return $this->belongsToMany(
			Player::class,
			'Player_Camp',
			'Camp_ID',    // foreign key for Camp in Player_Camp
			'Player_ID'   // foreign key for Player in Player_Camp
    	);
    }

    // A camp can have many coaches
    public function coaches()
    {
        return $this->belongsToMany(
            Coach::class,
            'Coach_Camp',
            'camp_id',      // foreign key for Camp
            'coach_id'      // foreign key for Coach
        );
    }
}