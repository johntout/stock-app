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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('symbol')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        $stockSymbols = [
            'IBM' => 'IBM',
            'MSFT' => 'Microsoft',
            'AAPL' => 'Apple',
            'VZ' => 'Verizon',
            'WBD' => 'Warner Bros',
            'NFLX' => 'Netflix',
            'DIS' => 'Disney',
            'TBB' => 'AT&T',
            'AMZN' => 'Amazon',
            'SONY' => 'Sony',
            'SONO' => 'Sonos',
        ];

        foreach ($stockSymbols as $stockSymbol => $title) {
            \App\Models\Stock::query()->create([
                'title' => $title,
                'symbol' => $stockSymbol,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
