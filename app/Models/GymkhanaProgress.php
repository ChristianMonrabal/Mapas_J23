<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymkhanaProgress extends Model
{
    /** @use HasFactory<\Database\Factories\GymkhanaProgressFactory> */
    use HasFactory;

    protected $fillable = [
        'group_users_id',
        'checkpoint_id',
        'completed'
    ];

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'group_users_id', 'id');
    }

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }
}
