<?php

use App\Enum\WinnerPrizeEnum;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/{prize}', 'pages::raffle')
    ->whereIn('prize', array_column(WinnerPrizeEnum::cases(), 'value'))
    ->name('raffle.show');
