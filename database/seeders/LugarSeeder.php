<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LugarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lugares = [
            // Facultades
            [
                'nombre' => 'Facultad de Ciencias Exactas y Tecnología',
                'descripcion' => 'Facultad de ingenierías y tecnología',
                'x' => 150,
                'y' => 100,
                'categoria_id' => 1
            ],
            [
                'nombre' => 'Facultad de Humanidades',
                'descripcion' => 'Facultad de letras y ciencias humanas',
                'x' => 300,
                'y' => 120,
                'categoria_id' => 1
            ],
            [
                'nombre' => 'Facultad de Ciencias Económicas',
                'descripcion' => 'Facultad de administración y contaduría',
                'x' => 200,
                'y' => 200,
                'categoria_id' => 1
            ],
            
            // Laboratorios
            [
                'nombre' => 'Laboratorio de Computación A',
                'descripcion' => 'Laboratorio de computación para estudiantes',
                'x' => 170,
                'y' => 80,
                'categoria_id' => 2
            ],
            [
                'nombre' => 'Laboratorio de Física',
                'descripcion' => 'Laboratorio equipado para prácticas de física',
                'x' => 130,
                'y' => 150,
                'categoria_id' => 2
            ],
            
            // Auditorios
            [
                'nombre' => 'Auditorio Central',
                'descripcion' => 'Auditorio principal de la universidad',
                'x' => 250,
                'y' => 80,
                'categoria_id' => 3
            ],
            
            // Biblioteca
            [
                'nombre' => 'Biblioteca Central',
                'descripcion' => 'Biblioteca principal con amplia colección',
                'x' => 350,
                'y' => 150,
                'categoria_id' => 4
            ],
            
            // Cafeterías
            [
                'nombre' => 'Cafetería Estudiantil',
                'descripcion' => 'Cafetería para estudiantes',
                'x' => 280,
                'y' => 250,
                'categoria_id' => 5
            ],
            
            // Canchas
            [
                'nombre' => 'Cancha de Fútbol',
                'descripcion' => 'Cancha principal de fútbol',
                'x' => 400,
                'y' => 300,
                'categoria_id' => 6
            ],
            [
                'nombre' => 'Cancha de Básquetbol',
                'descripcion' => 'Cancha techada de básquetbol',
                'x' => 450,
                'y' => 250,
                'categoria_id' => 6
            ],
            
            // Entradas
            [
                'nombre' => 'Entrada Principal',
                'descripcion' => 'Entrada principal de la universidad',
                'x' => 50,
                'y' => 200,
                'categoria_id' => 8
            ],
        ];

        DB::table('lugares')->insert($lugares);
    }
}