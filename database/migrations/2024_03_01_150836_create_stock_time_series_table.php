<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_time_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->dateTime('timestamp');
            $table->decimal('open', 19, 6);
            $table->decimal('high', 19, 6);
            $table->decimal('low', 19, 6);
            $table->decimal('close', 19, 6);
            $table->integer('volume');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_time_series');
    }
};
