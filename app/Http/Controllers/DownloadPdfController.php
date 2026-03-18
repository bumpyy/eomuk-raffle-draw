<?php

namespace App\Http\Controllers;

use App\Enum\WinnerPrizeEnum;
use App\Services\RaffleService;
use Spatie\LaravelPdf\Facades\Pdf as FacadesPdf;

class DownloadPdfController extends Controller
{
    public function __invoke(WinnerPrizeEnum $prize, RaffleService $raffleService)
    {
        $winners = $raffleService->getExistingWinners($prize);

        if (empty($winners)) {
            return back();
        }

        $prizeName = $prize->label();
        $filename = "raffle_winners_{$prize->value}_".now()->format('Ymd_His').'.pdf';

        return FacadesPdf::view('pdf.winners', [
            'winners' => $winners,
            'prizeName' => $prizeName,
        ])
            ->name($filename);

    }
}
