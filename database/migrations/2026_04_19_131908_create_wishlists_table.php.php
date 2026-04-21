<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlist', function (Blueprint $table) {
            $table->id('wishlist_id');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->timestamp('added_date')->useCurrent();
            
            // Foreign Keys
            $table->foreign('users_id')
                ->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('product_id')
                ->references('product_id')->on('product')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('variant_id')
                ->references('variant_id')->on('variant')
                ->onDelete('set null')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('users_id');
            $table->index('product_id');
            $table->index('variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist');
    }
};