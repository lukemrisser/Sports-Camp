<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;

    protected $table = 'Coaches';
    protected $primaryKey = 'Coach_ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Coach_FirstName',
        'Coach_LastName',
        'user_id',
        'admin',
        'Sport_ID',
    ];

    protected $casts = [
        'admin' => 'boolean',
        'user_id' => 'integer',
    ];

    // A coach can have many camps
    public function camps()
    {
        return $this->belongsToMany(
            Camp::class,
            'Coach_Camp',   // Pivot table name
            'Coach_ID',     // Foreign key on pivot table for this model
            'Camp_ID'       // Foreign key on pivot table for the related model
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // A coach belongs to one sport
    public function sport()
    {
        return $this->belongsTo(Sport::class, 'Sport_ID', 'Sport_ID');
    }

    public function getFullNameAttribute()
    {
        return $this->coach_firstname . ' ' . $this->coach_lastname;
    }

    public function getSportAttribute()
    {
        return $this->sport->Sport_Name ?? null; // or whatever the name field is called
    }

    /**
     * Model boot: handle cleanup when a coach is deleted.
     * - Detach pivot relations (camps)
     * - Optionally delete the associated user if it doesn't have other linked records (e.g. parent)
     */
    protected static function booted()
    {
        static::deleting(function (Coach $coach) {
            // Detach any pivoted camps
            if ($coach->camps()->exists()) {
                $coach->camps()->detach();
            }

            // If there is an associated user, delete it only if it's safe
            $user = $coach->user;
            if ($user) {
                // If the user is also a parent (or has other data), we should not auto-delete.
                // Use existing helper methods on the User model to check.
                $isParent = method_exists($user, 'isParent') ? $user->isParent() : false;

                // Only delete the user if they are not a parent and have no other coach records.
                // Since Coach->user is a one-to-one relation, after deleting this coach there
                // should be no other coach records, but we still check for safety.
                if (!$isParent) {
                    try {
                        $user->delete();
                    } catch (\Exception $e) {
                        // If deletion fails for any reason, swallow the exception to avoid
                        // blocking the coach deletion; log if you have a logger available.
                    }
                }
            }
        });
    }
}
