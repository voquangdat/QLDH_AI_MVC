<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->id('color_id');
            $table->string('color_ten', 255);
            $table->string('color_anh', 500)->nullable();
            
            // Indexes
            $table->index('color_ten');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};