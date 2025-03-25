<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{

    protected $table = 'group_users';
    protected $fillable = [
        'group_id',
        'user_id'
    ];

     public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Relación: El registro pivote pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un registro de group_user puede tener varios avances de gymkhana.
     */
    public function gymkhanaProgress()
    {
        return $this->hasMany(GymkhanaProgress::class, 'group_users_id');
    }
}
