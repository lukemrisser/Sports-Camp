<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $table = 'Sponsors';
    protected $primaryKey = 'Sponsor_ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Sponsor_Name',
        'Image_Path',
        'Sponsor_Link',
        'Sport_ID',
    ];

    protected $casts = [
        'Sport_ID' => 'integer',
    ];

    // A sponsor belongs to one sport
    public function sport()
    {
        return $this->belongsTo(Sport::class, 'Sport_ID', 'Sport_ID');
    }

    // Get the sport name attribute
    public function getSportAttribute()
    {
        return $this->sport->Sport_Name ?? null;
    }
}
