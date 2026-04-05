<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizedCrime extends Model
{
    protected $table = 'organized_crimes';
    
    public $timestamps = true;
    
    protected $fillable = [
        'faction_id',
        'oc_id',
        'name',
        'difficulty',
        'status',
        'oc_created_at',
        'planning_started_at',
        'ready_at',
        'executed_at',
        'expires_at',
        'last_synced_at',
    ];

    protected $casts = [
        'difficulty' => 'integer',
        'oc_created_at' => 'integer',
        'planning_started_at' => 'integer',
        'ready_at' => 'integer',
        'executed_at' => 'integer',
        'expires_at' => 'integer',
        'last_synced_at' => 'datetime',
    ];

    public function slots(): HasMany
    {
        return $this->hasMany(OrganizedCrimeSlot::class, 'organized_crime_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planning', 'recruiting', 'ready']);
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty) {
            1 => 'Easy',
            2 => 'Medium',
            3 => 'Hard',
            4 => 'Very Hard',
            5 => 'Extreme',
            default => 'Unknown',
        };
    }
}
