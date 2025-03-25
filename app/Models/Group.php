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
    protected $fillable = ['name', 'codigo', 'creador', 'gymkhana_id', 'max_miembros'];

    // Relación many-to-many con los usuarios que pertenecen al grupo.
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users');
    }

    // Relación para acceder al usuario que creó el grupo.
    public function creator()
    {
        return $this->belongsTo(User::class, 'creador');
    }

    // Relación para acceder a la gimkhana asociada al grupo.
    public function gymkhana()
    {
        return $this->belongsTo(Gymkhana::class, 'gymkhana_id');
    }
}
