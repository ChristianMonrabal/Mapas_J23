<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'direccion', 'latitud', 'longitud', 'descripcion'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'place_tags');
    }
}
