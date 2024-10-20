<?php

use App\Constants\DonationCategory;
use App\Constants\DonationStatus;
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
        Schema::create('donations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->mediumText('description');
            $table->double('estimated_amount');
            $table->enum('status', [DonationStatus::INCOMPLETE, DonationStatus::COMPLETE])->default(DonationStatus::INCOMPLETE);
            $table->timestamp('deadline');
            $table->enum('category', [DonationCategory::HUMANITARIAN, DonationCategory::REFUGEE, DonationCategory::EVANGELISM]);
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
