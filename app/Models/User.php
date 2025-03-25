<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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

    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class, 'user_id');
    }

    public function groups(){
        return $this->belongsToMany(Group::class, 'group_users', 'user_id', 'group_id');}

    public function creador()
    {
        return $this->hasMany(Group::class, 'creador');
    }
}