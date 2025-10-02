<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;

    protected $table = 'Coaches';
    protected $primaryKey = 'Coach_ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Coach_FirstName',
        'Coach_LastName',
        'user_id',
        'admin',
        'sport'
    ];

    protected $casts = [
        'admin' => 'boolean',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFullNameAttribute()
    {
        return $this->coach_firstname . ' ' . $this->coach_lastname;
    }
}
