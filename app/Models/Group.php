<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    // Se definen los campos asignables masivamente.
    protected $fillable = ['name', 'codigo', 'creador', 'miembros'];

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
}
