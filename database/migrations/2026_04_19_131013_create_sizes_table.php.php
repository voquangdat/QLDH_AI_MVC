<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_size', function (Blueprint $table) {
            $table->id('product_size_id');
            $table->string('product_size', 20)->unique();
            
            // Indexes
            $table->index('product_size');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_size');
    }
};