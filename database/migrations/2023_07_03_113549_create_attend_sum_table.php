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
        Schema::create('attend_sum', function (Blueprint $table) {
            $table->id();
            $table->char('month', 7);
            $table->boolean('completed')->nullable();
            $table->timestamp('updated_at');
            $table->string('Celkový fond', 6);
            $table->string('Aktuálny fond', 6);
            $table->string('Odpracovaný čas', 6);
            $table->string('Započítaný čas', 6);
            $table->string('Saldo', 6);
            $table->string('Lekár - zamestnanec - mesiac', 6);
            $table->string('Lekár - zamestnanec - sumárne', 6);
            $table->string('Lekár - sprevádzanie RP - mesiac', 6);
            $table->string('Lekár - sprevádzanie RP - sumárne', 6);
            $table->double('Dovolenka - čerpanie - mesiac', 3, 1);
            $table->double('Dovolenka - zostatok', 3, 1);
            $table->double('Voľno z KZ - aktuálny mesiac', 2, 1);
            $table->double('Voľno z KZ - zostatok', 2, 1);
            $table->string('Práceneschopnosť - mesiac', 6);
            $table->string('Ošetrovanie člena rodiny - mesiac', 6);
            $table->string('Práceneschopnosť - karanténa', 6);
            $table->string('Ošetrovanie člena rodiny - pandemické', 6);
            $table->string('Iné osobné prekážky v práci s náhradou mzdy', 6);
            $table->unsignedTinyInteger('Home office');
            $table->unsignedTinyInteger('Zoznam neprítomností pre stravné lístky');
            $table->unsignedTinyInteger('Nárok SL');
            $table->unsignedTinyInteger('SL -  Počet neprítomnosti');
            $table->string('userSID', 50);
            $table->foreign('userSID')->references('SID')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attend_sum');
    }
};
