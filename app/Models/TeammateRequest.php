<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeammateRequest extends Model
{
    protected $table = 'Teammate_Request';

    protected $fillable = [
        'Player_ID',
        'Requested_FirstName',
        'Requested_LastName',
    ];

    public $timestamps = true;
}
