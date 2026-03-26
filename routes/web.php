<?php

use App\Enum\WinnerPrizeEnum;
use App\Http\Controllers\DownloadPdfController;
use App\Http\Middleware\StaticAuth;
use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'login')->name('login');

Route::middleware([StaticAuth::class])->group(function () {

    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    // Route::get('/{prize}', function () {})
    //     ->whereIn('prize', array_column(WinnerPrizeEnum::cases(), 'value'))
    //     ->name('raffle.index');

    Route::livewire('/{prize}', 'pages::raffle')
        ->whereIn('prize', array_merge(['all'], array_column(WinnerPrizeEnum::cases(), 'value')))
        ->name('raffle.show');
});

Route::get('/export-pdf/{prize}', DownloadPdfController::class)->name('export.pdf');

// require __DIR__.'/auth.php';
