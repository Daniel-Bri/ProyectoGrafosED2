<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaLugarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Facultad'],
            ['nombre' => 'Laboratorio'],
            ['nombre' => 'Auditorio'],
            ['nombre' => 'Biblioteca'],
            ['nombre' => 'Cafetería'],
            ['nombre' => 'Cancha Deportiva'],
            ['nombre' => 'Estacionamiento'],
            ['nombre' => 'Área Verde'],
            ['nombre' => 'Oficina Administrativa'],
            ['nombre' => 'Baño'],
        ];

        DB::table('categoria_lugar')->insert($categorias);
    }
}