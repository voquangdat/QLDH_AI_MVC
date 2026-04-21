<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name', 255);
            $table->text('description')->nullable();
            $table->text('care_note')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->decimal('product_gia', 12, 2);
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('category_id')
                ->references('category_id')->on('categories')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->foreign('subcategory_id')
                ->references('subcategory_id')->on('subcategories')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('product_name');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};