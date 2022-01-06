<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static active()
 * @method static expired()
 */
class Heartbeat extends Model
{
    protected $fillable = [
        'code',
        'error_message',
        'expires_at'
    ];

    public function scopeExpired($query)
    {
        return $query->where('expires_at', "<", now());
    }
}
