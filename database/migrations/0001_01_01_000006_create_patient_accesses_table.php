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
        Schema::create('patient_accesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('type'); // Consulta, Cita, Emergencia, etc.
            $table->text('description');
            $table->dateTime('access_date');
            $table->string('doctor_in_charge')->nullable();
            $table->string('status')->default('completada'); // completada, programada, cancelada
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_accesses');
    }
};
