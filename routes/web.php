<?php

use App\Enum\WinnerPrizeEnum;
use App\Http\Controllers\DownloadPdfController;
use App\Http\Middleware\StaticAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

Route::get('/export-pdf-preview/{prize}', [DownloadPdfController::class, 'preview'])->name('export.preview');
Route::get('/export-pdf/{prize}', [DownloadPdfController::class, 'render'])->name('export.pdf');

// Route::get('/clean-data', function () {

//     $targetDate = '2026-02-17 16:59:59';

//     $startMarchTimestamp = Carbon::create(2026, 1, 1, 0, 0, 0)->timestamp;
//     $endMarchTimestamp = Carbon::create(2026, 2, 17, 16, 59, 59)->timestamp;

//     // dd(DB::table('submissions')
//     //     ->where('created_at', '>', $targetDate)->get());

//     DB::table('submissions')
//         ->where('created_at', '>', $targetDate)
//         ->orderBy('id')
//         ->chunk(200, function ($records) use ($startMarchTimestamp, $endMarchTimestamp) {
//             foreach ($records as $record) {

//                 $createdTimestamp = rand($startMarchTimestamp, $endMarchTimestamp);

//                 $updatedTimestamp = rand($createdTimestamp, $endMarchTimestamp);

//                 DB::table('submissions')
//                     ->where('id', $record->id)
//                     ->update([
//                         'created_at' => Carbon::createFromTimestamp($createdTimestamp),
//                         'updated_at' => Carbon::createFromTimestamp($updatedTimestamp),
//                     ]);
//             }
//         });

//     return 'Update selesai!';
// });

// require __DIR__.'/auth.php';
