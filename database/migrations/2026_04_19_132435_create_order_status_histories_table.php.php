<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id('status_history_id');
            $table->unsignedBigInteger('order_id');
            $table->enum('old_status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->nullable();
            $table->enum('new_status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']);
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Foreign Keys
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('changed_by')
                ->references('user_id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('order_id');
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
    }
};