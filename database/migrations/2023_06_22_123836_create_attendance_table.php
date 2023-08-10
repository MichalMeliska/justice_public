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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->boolean('volno')->nullable();
            $table->string('oddo', 14)->nullable();
            $table->string('trvanie', 5)->nullable();
            $table->string('prerusenie', 14)->nullable();
            $table->string('userSID', 50);
            $table->foreign('userSID')->references('SID')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
