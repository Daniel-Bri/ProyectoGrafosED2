<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaminoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caminos = [
            // Caminos desde la entrada principal
            [
                'distancia' => 120.5,
                'es_bidireccional' => true,
                'lugar_origen_id' => 11, // Entrada Principal
                'lugar_destino_id' => 1,  // Facultad Exactas
            ],
            [
                'distancia' => 180.2,
                'es_bidireccional' => true,
                'lugar_origen_id' => 11, // Entrada Principal
                'lugar_destino_id' => 3,  // Facultad Económicas
            ],
            
            // Conexiones entre facultades
            [
                'distancia' => 85.7,
                'es_bidireccional' => true,
                'lugar_origen_id' => 1,  // Exactas
                'lugar_destino_id' => 2,  // Humanidades
            ],
            [
                'distancia' => 95.3,
                'es_bidireccional' => true,
                'lugar_origen_id' => 1,  // Exactas
                'lugar_destino_id' => 4,  // Lab Computación
            ],
            [
                'distancia' => 65.8,
                'es_bidireccional' => true,
                'lugar_origen_id' => 1,  // Exactas
                'lugar_destino_id' => 5,  // Lab Física
            ],
            
            // Conexiones hacia la biblioteca
            [
                'distancia' => 110.4,
                'es_bidireccional' => true,
                'lugar_origen_id' => 2,  // Humanidades
                'lugar_destino_id' => 7,  // Biblioteca
            ],
            [
                'distancia' => 135.6,
                'es_bidireccional' => true,
                'lugar_origen_id' => 3,  // Económicas
                'lugar_destino_id' => 7,  // Biblioteca
            ],
            
            // Conexiones hacia cafetería
            [
                'distancia' => 90.2,
                'es_bidireccional' => true,
                'lugar_origen_id' => 3,  // Económicas
                'lugar_destino_id' => 8,  // Cafetería
            ],
            [
                'distancia' => 75.8,
                'es_bidireccional' => true,
                'lugar_origen_id' => 7,  // Biblioteca
                'lugar_destino_id' => 8,  // Cafetería
            ],
            
            // Conexiones hacia canchas
            [
                'distancia' => 200.1,
                'es_bidireccional' => true,
                'lugar_origen_id' => 8,  // Cafetería
                'lugar_destino_id' => 9,  // Cancha Fútbol
            ],
            [
                'distancia' => 185.7,
                'es_bidireccional' => true,
                'lugar_origen_id' => 8,  // Cafetería
                'lugar_destino_id' => 10, // Cancha Básquet
            ],
        ];

        DB::table('caminos')->insert($caminos);
    }
}