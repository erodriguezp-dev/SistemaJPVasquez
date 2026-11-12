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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->enum('tipo', ['mueble', 'ferreteria', 'reparacion']);
            $table->string('cliente_nombre', 150);
            $table->string('cliente_telefono', 20)->nullable();
            $table->string('cliente_direccion', 255)->nullable();
            $table->enum('estado', ['recibido', 'en_proceso', 'terminado', 'entregado', 'confirmado'])
                  ->default('recibido')
                  ->index();
            $table->dateTime('fecha_pedido')->index();
            $table->date('fecha_entrega')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
