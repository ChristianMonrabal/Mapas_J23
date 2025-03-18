<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_users');
    }

    public function gymkhanaProgress()
    {
        return $this->hasMany(GymkhanaProgress::class);
    }
}
