<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $table = 'Gallery_Images';
    protected $primaryKey = 'Image_ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Image_path',
        'Image_Title',
        'Image_Text',
        'Sport_ID',
    ];

    protected $casts = [
        'Image_ID' => 'integer',
        'Sport_ID' => 'integer',
    ];

    // A gallery image belongs to one sport
    public function sport()
    {
        return $this->belongsTo(Sport::class, 'Sport_ID', 'Sport_ID');
    }
}
