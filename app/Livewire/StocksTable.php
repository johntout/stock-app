<?php

namespace App\Livewire;

use App\Services\StocksService;
use Livewire\Component;
use Livewire\WithPagination;

class StocksTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.stocks-table', [
            'stocks' => (new StocksService)->getStocks(),
        ]);
    }
}
