<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camino extends Model
{
    use HasFactory;

    // ✅ Especifica el nombre exacto de la tabla
    protected $table = 'caminos';

    protected $fillable = [
        'distancia',
        'es_bidireccional',
        'lugar_origen_id',
        'lugar_destino_id'
    ];

    public function origen()
    {
        return $this->belongsTo(Lugar::class, 'lugar_origen_id');
    }

    public function destino()
    {
        return $this->belongsTo(Lugar::class, 'lugar_destino_id');
    }
}