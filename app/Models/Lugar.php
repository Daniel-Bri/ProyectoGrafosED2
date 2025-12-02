<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    use HasFactory;

    // ✅ Especifica el nombre exacto de la tabla
    protected $table = 'lugares';

    protected $fillable = [
        'nombre',
        'descripcion',
        'x',
        'y',
        'categoria_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaLugar::class);
    }

    public function caminosOrigen()
    {
        return $this->hasMany(Camino::class, 'lugar_origen_id');
    }

    public function caminosDestino()
    {
        return $this->hasMany(Camino::class, 'lugar_destino_id');
    }
}