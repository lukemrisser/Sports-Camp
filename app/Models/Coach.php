<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
	use HasFactory;

	// Set custom primary key
	protected $primaryKey = 'coach_id';

	// Allow mass assignment for these fields
	protected $fillable = ['coach_firstname', 'coach_lastname'];

	// A coach can have many camps
	public function camps()
	{
		return $this->belongsToMany(Camp::class, 'coach_camp');
	}
}
