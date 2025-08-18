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
        Schema::create('reviews', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');     // review is tied to one loan
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // owner
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade'); // borrower
            $table->unsignedTinyInteger('rating');   // 1..5
            $table->text('body')->nullable();
            $table->timestamps();
        });

        // (optional but useful) enforce at most one review per loan
        Schema::table('reviews', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->unique('loan_id');
        });
    }

};
