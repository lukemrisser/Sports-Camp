<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
	// Specify the table name to match the database
	protected $table = 'Coaches';
	use HasFactory;
	protected $primaryKey = 'Coach_ID';
	protected $fillable = ['Coach_FirstName', 'Coach_LastName'];

    protected $casts = [
        'admin' => 'boolean',
        'user_id' => 'integer',
    ];

	// A coach can have many camps
	public function camps()
	{
		return $this->belongsToMany(
			Camp::class,
			'Coach_Camp',   // Pivot table name
			'Coach_ID',     // Foreign key on pivot table for this model
			'Camp_ID'       // Foreign key on pivot table for the related model
		);
	}
	
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFullNameAttribute()
    {
        return $this->coach_firstname . ' ' . $this->coach_lastname;
    }
}
