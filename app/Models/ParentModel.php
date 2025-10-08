<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    protected $table = 'Parents';
    public $timestamps = false;

    protected $fillable = [
        'Parent_FirstName',
        'Parent_LastName',
        'Address',
        'City',
        'State',
        'Postal_Code',
        'Email',
        'Phone',
        'Church_Name',
        'Church_Attendance'
    ];

    public function players()
    {
        return $this->hasMany(Player::class, 'Parent_ID');
    }
}