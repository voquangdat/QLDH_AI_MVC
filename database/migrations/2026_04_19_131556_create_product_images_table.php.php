<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id('product_anh_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_anh', 500);
            
            // Foreign Keys
            $table->foreign('product_id')
                ->references('product_id')->on('product')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};