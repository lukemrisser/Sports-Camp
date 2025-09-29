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
	public function camps()
	{
		return $this->belongsToMany(Camp::class, 'Coach_Camp', 'Coach_ID', 'Camp_ID');
	}
}
