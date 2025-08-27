<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('condition')->nullable();
            $table->string('status')->default('available');
            $table->string('location')->nullable();
            $table->string('price_per_day')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });

    }

 
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
 