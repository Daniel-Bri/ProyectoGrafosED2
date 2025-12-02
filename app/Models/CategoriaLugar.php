<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaLugar extends Model
{
    use HasFactory;

    // ✅ Especifica el nombre exacto de la tabla
    protected $table = 'categoria_lugar';

    protected $fillable = ['nombre'];
}