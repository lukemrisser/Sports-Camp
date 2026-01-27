<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderExtraFee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'Order_Extra_Fees';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'Order_Fee_ID';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'Fee_ID',
        'Order_ID',
    ];

    /**
     * Get the order this extra fee charge belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'Order_ID', 'Order_ID');
    }

    /**
     * Get the extra fee definition for this charge.
     */
    public function extraFee(): BelongsTo
    {
        return $this->belongsTo(ExtraFee::class, 'Fee_ID', 'Fee_ID');
    }
}
