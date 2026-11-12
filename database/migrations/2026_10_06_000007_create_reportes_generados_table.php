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
        Schema::create('reportes_generados', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['inventario', 'pedidos']);
            $table->json('filtros')->nullable();
            $table->string('formato', 10)->nullable();
            $table->dateTime('hora_inicio', 3);
            $table->dateTime('hora_fin', 3)->nullable();
            $table->decimal('duracion_segundos', 8, 2)->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_generados');
    }
};
