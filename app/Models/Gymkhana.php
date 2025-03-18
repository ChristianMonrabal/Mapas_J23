<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gymkhana extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }
}
