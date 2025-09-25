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