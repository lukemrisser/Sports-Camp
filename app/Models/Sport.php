<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Camp;

class Sport extends Model
{
    use HasFactory;

    protected $table = 'Sports';
    protected $primaryKey = 'Sport_ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Sport_Name',
        'Sport_Description',
    ];

    // A sport can have many coaches
    public function coaches()
    {
        return $this->hasMany(Coach::class, 'Sport_ID', 'Sport_ID');
    }

    // A sport can have many camps
    public function camps()
    {
        return $this->hasMany(Camp::class, 'Sport_ID', 'Sport_ID');
    }
}