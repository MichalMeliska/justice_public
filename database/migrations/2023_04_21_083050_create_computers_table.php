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
        Schema::create('computers', function (Blueprint $table) {
            $table->string('SID', 50)->primary();
            $table->string('Name', 20);
            $table->dateTime('Created')->nullable();
            $table->string('OperatingSystem', 40)->nullable();
            $table->dateTime('InstallDate')->nullable();
            $table->dateTime('LastBootUpTime')->nullable();
            $table->string('Model', 40)->nullable();
            $table->enum('OSArchitecture', [32, 64])->nullable();
            $table->macAddress('Mac')->nullable();
            $table->string('LoggedUser', 30)->nullable();
            $table->unsignedTinyInteger('TotalPhysicalMemory')->nullable();
            $table->string('RegisterVersion', 8)->nullable();
            $table->char('sud', 4);
            $table->ipAddress('IP')->nullable();
            $table->char('SN', 10)->nullable();
            $table->unsignedTinyInteger('Powershell')->nullable();
            $table->boolean('IT');
            $table->dateTime('specs_at')->nullable();
            $table->dateTime('online_at')->nullable();
            $table->unsignedTinyInteger('printerID')->nullable();
            $table->foreign('printerID')->references('ID')->on('printers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computers');
    }
};
