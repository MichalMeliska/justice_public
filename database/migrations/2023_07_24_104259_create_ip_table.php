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
        Schema::create('ip', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('attr', 12);
            $table->string('kstt', 47);
            $table->string('ostt', 20);
            $table->string('osds', 20);
            $table->string('osga', 20);
            $table->string('ospn', 20);
            $table->string('osse', 20);
            $table->string('ossi', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip');
    }
};
