<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('users_id');
            $table->integer('rating')->unsigned();
            $table->string('title', 255)->nullable();
            $table->text('comment')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('product_id')
                ->references('product_id')->on('product')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('users_id')
                ->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('product_id');
            $table->index('users_id');
            $table->index('rating');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};