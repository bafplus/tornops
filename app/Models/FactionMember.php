<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactionMember extends Model
{
    protected $fillable = [
        'faction_id',
        'player_id',
        'name',
        'level',
        'rank',
        'days_in_faction',
        'data',
        'last_synced_at',
    ];

    protected $casts = [
        'data' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(PlayerProfile::class, 'player_id', 'player_id');
    }
}
