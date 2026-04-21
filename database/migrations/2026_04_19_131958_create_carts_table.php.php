<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->id('cart_id');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('quantity')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign Keys
            $table->foreign('users_id')
                ->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('variant_id')
                ->references('variant_id')->on('variant')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('users_id');
            $table->index('variant_id');
            $table->index(['users_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};