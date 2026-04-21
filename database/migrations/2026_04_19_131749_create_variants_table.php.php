<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant', function (Blueprint $table) {
            $table->id('variant_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('product_size_id');
            $table->integer('quantity')->default(0);
            $table->string('variant_code', 100)->nullable();
            
            // Foreign Keys
            $table->foreign('product_id')
                ->references('product_id')->on('product')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->foreign('color_id')
                ->references('color_id')->on('colors')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->foreign('product_size_id')
                ->references('product_size_id')->on('product_size')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            // Unique constraint
            $table->unique(['product_id', 'color_id', 'product_size_id'], 'unique_product_color_size');
            
            // Indexes
            $table->index('product_id');
            $table->index('color_id');
            $table->index('product_size_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant');
    }
};