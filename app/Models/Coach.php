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
	protected $primaryKey = 'coach_id';

	// Allow mass assignment for these fields
	protected $fillable = ['coach_firstname', 'coach_lastname'];

	// A coach can have many camps
	public function camps()
	{
		return $this->belongsToMany(Camp::class, 'Coach_Camp');
	}
}
