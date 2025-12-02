<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('caminos', function (Blueprint $table) {
            $table->id();
            $table->double('distancia');
            $table->boolean('es_bidireccional')->default(true);
            $table->foreignId('lugar_origen_id')->constrained('lugares')->onDelete('cascade');
            $table->foreignId('lugar_destino_id')->constrained('lugares')->onDelete('cascade');
            $table->timestamps();
            
            // Índices únicos para evitar caminos duplicados
            $table->unique(['lugar_origen_id', 'lugar_destino_id']);
            $table->index(['lugar_origen_id']);
            $table->index(['lugar_destino_id']);
            $table->index(['es_bidireccional']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caminos');
    }
};