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
        Schema::create('user_donations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('amount_given');
            $table->uuid('donation_id');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('donation_id')->references('id')->on('donations')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_donations');
    }
};
