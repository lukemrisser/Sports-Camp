<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
	use HasFactory;

    // Set custom primary key
    protected $primaryKey = 'player_id';

    // Specify the table name to match the database
    protected $table = 'Players';

    // Allow mass assignment for these fields
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
        'Church_Attendance'
    ];

	// A player can belong to many camps
	public function camps()
	{
		return $this->belongsToMany(Camp::class, 'Player_Camp');
	}
}
