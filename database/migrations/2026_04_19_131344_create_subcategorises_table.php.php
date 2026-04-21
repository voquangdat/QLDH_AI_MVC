<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id('subcategory_id');
            $table->unsignedBigInteger('category_id');
            $table->string('subcategory_name', 150);
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('category_id')
                ->references('category_id')->on('categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('category_id');
            $table->index('subcategory_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};