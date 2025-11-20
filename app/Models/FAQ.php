<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'FAQs';
    protected $primaryKey = 'FAQ_ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Question',
        'Answer',
        'Sport_ID',
    ];

    protected $casts = [
        'Sport_ID' => 'integer',
    ];

    // An FAQ belongs to one sport
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
