<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
	use HasFactory;

    protected $primaryKey = 'Player_ID';
    protected $table = 'Players';
    public $timestamps = false;

    // Allow mass assignment for these fields
    protected $fillable = [
        'Parent_ID',
        'Division_Name',
        'Camper_FirstName',
        'Camper_LastName',
        'Gender',
        'Birth_Date',
        'Shirt_Size',
        'Allergies',
        'Asthma',
        'Medication_Status',
        'Injuries'
    ];

    // Relationship with Parent model
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'Parent_ID', 'id');
    }

    // A player can belong to many camps
    public function camps()
    {
        return $this->belongsToMany(Camp::class, 'Player_Camp', 'Player_ID', 'Camp_ID');
    }

    // Relationship with teammate requests
    public function teammateRequests()
    {
        return $this->hasMany(TeammateRequest::class, 'player_id', 'Player_ID');
    }
}
