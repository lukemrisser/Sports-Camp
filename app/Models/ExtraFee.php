<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtraFee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'Extra_Fees';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'Fee_ID';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'Fee_Name',
        'Fee_Description',
        'Fee_Amount',
        'Camp_ID',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'Fee_Amount' => 'decimal:2',
    ];

    /**
     * Get the camp that offers this extra fee.
     */
    public function camp(): BelongsTo
    {
        return $this->belongsTo(Camp::class, 'Camp_ID', 'Camp_ID');
    }

    /**
     * Get order extra fee records for this fee.
     */
    public function orderExtraFees(): HasMany
    {
        return $this->hasMany(OrderExtraFee::class, 'Fee_ID', 'Fee_ID');
    }
}
