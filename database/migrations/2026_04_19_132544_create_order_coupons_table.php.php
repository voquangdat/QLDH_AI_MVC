<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_coupon', function (Blueprint $table) {
            $table->id('order_coupon_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('coupon_id');
            $table->decimal('discount_amount', 12, 2);
            
            // Foreign Keys
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('coupon_id')
                ->references('coupon_id')->on('coupon')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('order_id');
            $table->index('coupon_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_coupon');
    }
};