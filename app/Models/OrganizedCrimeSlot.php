<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizedCrimeSlot extends Model
{
    protected $table = 'organized_crime_slots';
    
    public $timestamps = true;
    
    protected $fillable = [
        'organized_crime_id',
        'oc_id',
        'position',
        'position_number',
        'user_id',
        'checkpoint_pass_rate',
        'user_joined_at',
        'item_required_id',
        'item_available',
        'last_synced_at',
    ];

    protected $casts = [
        'position_number' => 'integer',
        'checkpoint_pass_rate' => 'decimal:2',
        'user_joined_at' => 'integer',
        'item_required_id' => 'integer',
        'item_available' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function organizedCrime(): BelongsTo
    {
        return $this->belongsTo(OrganizedCrime::class, 'organized_crime_id');
    }

    public function getUser(): ?FactionMember
    {
        if (!$this->user_id) {
            return null;
        }
        return FactionMember::where('player_id', $this->user_id)->first();
    }
}
