<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalNotification extends Model
{
    protected $fillable = [
        'recipient_type',
        'recipient_id',
        'title',
        'body',
        'url',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function scopeForRecipient($query, string $type, int $id)
    {
        return $query->where('recipient_type', $type)->where('recipient_id', $id);
    }
}
