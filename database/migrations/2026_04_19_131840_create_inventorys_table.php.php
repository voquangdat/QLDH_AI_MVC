<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->unsignedBigInteger('variant_id')->unique();
            $table->integer('soluong_ton')->default(0);
            $table->integer('soluong_dat')->default(0);
            $table->integer('soluong_co_the_ban')->default(0);
            $table->integer('muc_canh_bao')->default(10);
            
            // Foreign Keys
            $table->foreign('variant_id')
                ->references('variant_id')->on('variant')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            // Indexes
            $table->index('variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};