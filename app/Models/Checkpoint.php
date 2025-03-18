<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    /** @use HasFactory<\Database\Factories\CheckpointFactory> */
    use HasFactory;

    protected $fillable = [
        'gymkhana_id',
        'place_id',
        'pista'
    ];

    public function gymkhana()
    {
        return $this->belongsTo(Gymkhana::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function progress()
    {
        return $this->hasMany(GymkhanaProgress::class);
    }
}
