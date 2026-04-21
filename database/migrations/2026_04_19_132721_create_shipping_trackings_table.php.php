<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_tracking', function (Blueprint $table) {
            $table->id('tracking_id');
            $table->unsignedBigInteger('shipping_id');
            $table->string('location', 255);
            $table->string('status', 100);
            $table->dateTime('timestamp')->useCurrent();
            
            // Foreign Keys
            $table->foreign('shipping_id')
                ->references('shipping_id')->on('shipping')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('shipping_id');
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_tracking');
    }
};