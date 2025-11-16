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
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            // Claves foráneas
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_libro');

            // Fechas y estado
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion')->nullable(); // por si no se ha devuelto aún
            $table->string('estado', 20); // pendiente, devuelto, vencido

            $table->timestamps();

            // Definición de las claves foráneas
            $table->foreign('id_usuario')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_libro')->references('id')->on('libros')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
