<?php

namespace Tests\Feature\Livewire;

use App\Livewire\StocksTable;
use Livewire\Livewire;

test('component renders', function () {
    Livewire::test(StocksTable::class)
        ->assertOk();
});
