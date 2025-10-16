<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'password',
        'token',
        'additional_data',
        'expires_at'
    ];

    protected $casts = [
        'additional_data' => 'array',
        'expires_at' => 'datetime'
    ];

    /**
     * Check if the registration has expired
     */
    public function hasExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Clean up expired registrations
     */
    public static function cleanupExpired(): int
    {
        return static::where('expires_at', '<', now())->delete();
    }
}
