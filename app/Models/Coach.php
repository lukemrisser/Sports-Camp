<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
	// Specify the table name to match the database
	protected $table = 'Coaches';
	use HasFactory;

	// Set custom primary key
	protected $primaryKey = 'Coach_ID';

	// Allow mass assignment for these fields
	protected $fillable = ['Coach_FirstName', 'Coach_LastName'];

	// A coach can have many camps
	public function camps() {
		return $this->belongsToMany(
			Camp::class,    // related model
			'Coach_Camp',   // pivot table
			'coach_id',     // foreign key for Coach (this model)
			'camp_id'       // foreign key for Camp (related model)
		);
	}
}