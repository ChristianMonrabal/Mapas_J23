<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'email', 'password', 'role_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
