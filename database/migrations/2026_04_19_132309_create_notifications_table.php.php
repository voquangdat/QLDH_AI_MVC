<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('users_id');
            $table->string('title', 255);
            $table->text('message');
            $table->enum('type', ['order', 'payment', 'delivery', 'return', 'promo', 'system'])->default('system');
            $table->unsignedBigInteger('related_order_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('users_id')
                ->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('related_order_id')
                ->references('id')->on('orders')
                ->onDelete('set null')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('users_id');
            $table->index('related_order_id');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};