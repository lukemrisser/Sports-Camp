<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Camp;

class Player extends Model
{
    protected $table = 'Players';
    protected $primaryKey = 'Player_ID';
    public $timestamps = false;

    protected $fillable = [
        'Division_Name',
        'Parent_FirstName',
        'Parent_LastName',
        'Camper_FirstName',
        'Camper_LastName',
        'Gender',
        'Birth_Date',
        'Address',
        'City',
        'State',
        'Postal_Code',
        'Email',
        'Phone',
        'Age',
        'Shirt_Size',
        'Allergies',
        'Asthma',
        'Medication_Status',
        'Injuries',
        'Church_Name',
        'Church_Attendance',
    ];

    public function camps() {
        return $this->belongsToMany(Camp::class, 'Player_Camp', 'Player_ID', 'Camp_ID');
    }
}
