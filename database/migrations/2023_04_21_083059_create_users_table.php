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
        Schema::create('users', function (Blueprint $table) {
            $table->string('SID', 50)->primary();
            $table->string('Name', 50);
            $table->string('SamAccountName', 30)->nullable();
            $table->string('EmailAddress', 45)->nullable();
            $table->boolean('Enabled')->nullable();
            $table->unsignedSmallInteger('ipPhone')->nullable();
            $table->dateTime('Created')->nullable();
            $table->string('Description', 100)->nullable();
            $table->boolean('LockedOut')->nullable();
            $table->dateTime('msExchWhenMailboxCreated')->nullable();
            $table->dateTime('PasswordLastSet')->nullable();
            $table->dateTime('PasswordExpires')->nullable();
            $table->char('sud', 4);
            $table->unsignedSmallInteger('kanc')->nullable();
            $table->string('humanet_pass', 15)->nullable();
            $table->string('computerSID', 50)->nullable();
            $table->foreign('computerSID')->references('SID')->on('computers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
