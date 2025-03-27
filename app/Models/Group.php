<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Gymkhana;
use App\Models\User;


class Group extends Model
{
    use HasFactory;

    // Campos asignables masivamente.
    protected $fillable = ['name', 'codigo', 'creador', 'max_miembros'];

    // Relación many-to-many con los usuarios que pertenecen al grupo.
    public function creator()
    {
        return $this->belongsTo(User::class, 'creador');
    }

    /**
     * Relación: Un grupo tiene muchos registros en la tabla pivote group_users.
     */
    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class, 'group_id');
    }

    /**
     * Relación: Un grupo tiene muchos usuarios (muchos a muchos a través de group_users).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id');
    }


    public function gymkhana()
    {
        return $this->belongsTo(Gymkhana::class, 'gymkhana_id');
    }
}
