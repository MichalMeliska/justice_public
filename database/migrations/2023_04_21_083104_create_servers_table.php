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
        Schema::create('servers', function (Blueprint $table) {
            $table->string('SID', 50)->primary();
            $table->string('Name', 20);
            $table->dateTime('Created')->nullable();
            $table->string('OperatingSystem', 40)->nullable();
            $table->dateTime('InstallDate')->nullable();
            $table->dateTime('LastBootUpTime')->nullable();
            $table->string('Model', 40)->nullable();
            $table->enum('OSArchitecture', [32, 64])->nullable();
            $table->unsignedSmallInteger('TotalPhysicalMemory')->nullable();
            $table->string('RegisterVersion', 8)->nullable();
            $table->boolean('RegisterRunning')->nullable();
            $table->boolean('TaskSchedulerRunning')->nullable();
            $table->unsignedSmallInteger('RegisterLog')->nullable();
            $table->unsignedTinyInteger('Powershell')->nullable();
            $table->string('SN', 12)->nullable();
            $table->dateTime('specs_at')->nullable();
            $table->dateTime('online_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
