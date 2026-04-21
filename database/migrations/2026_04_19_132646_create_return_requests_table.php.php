<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_request', function (Blueprint $table) {
            $table->id('return_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_detail_id');
            $table->text('reason');
            $table->integer('quantity');
            $table->enum('status', ['requested', 'approved', 'rejected', 'refunded'])->default('requested');
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->foreign('order_detail_id')
                ->references('detail_id')->on('order_details')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('order_id');
            $table->index('order_detail_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_request');
    }
};