<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Camp;

class CampDiscount extends Model
{
    use HasFactory;

    protected $primaryKey = 'Discount_ID';

    protected $table = 'Camp_Discounts';

    public $timestamps = false;

    // Allow mass assignment for these fields
    protected $fillable = [
        'Camp_ID',
        'Discount_Amount',
        'Discount_Date'
    ];

    // Cast Discount_Amount as decimal and Discount_Date as date
    protected $casts = [
        'Discount_Amount' => 'decimal:2',
        'Discount_Date' => 'date'
    ];

    // A discount belongs to a camp
    public function camp()
    {
        return $this->belongsTo(Camp::class, 'Camp_ID', 'Camp_ID');
    }
}