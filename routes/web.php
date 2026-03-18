<?php

use App\Enum\WinnerPrizeEnum;
use App\Http\Controllers\DownloadPdfController;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/{prize}', 'pages::raffle')
    ->whereIn('prize', array_column(WinnerPrizeEnum::cases(), 'value'))
    ->name('raffle.show');

Route::get('/export-pdf/{prize}', DownloadPdfController::class)->name('export.pdf');

// Route::get('/pdf/{prize}', function (WinnerPrizeEnum $prize) {
//     Pdf::view('pages::raffle', ['prize' => $prize])->save('/some/directory/invoice.pdf');
// })
//     ->whereIn('prize', array_column(WinnerPrizeEnum::cases(), 'value'))
//     ->name('pdf.export');
