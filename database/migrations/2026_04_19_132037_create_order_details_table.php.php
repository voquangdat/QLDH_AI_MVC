<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('detail_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('quantity');
            $table->string('product_name', 255);
            $table->decimal('product_gia', 12, 2);
            $table->string('product_anh', 500)->nullable();
            $table->decimal('subtotal', 12, 2);
            
            // Foreign Keys
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('product_id')
                ->references('product_id')->on('product')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->foreign('variant_id')
                ->references('variant_id')->on('variant')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('order_id');
            $table->index('product_id');
            $table->index('variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};