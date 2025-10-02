<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeammateRequest extends Model
{
    protected $table = 'Teammate_Request';

    
    protected $primaryKey = 'Teammate_Request_ID';

    protected $fillable = [
        'Player_ID',
        'Requested_FirstName',
        'Requested_LastName',
    ];

    public $timestamps = false;
}
