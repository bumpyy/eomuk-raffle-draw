<?php

namespace App\Http\Controllers;

use App\Enum\WinnerPrizeEnum;
use App\Services\RaffleService;
use Spatie\LaravelPdf\Facades\Pdf as FacadesPdf;

class DownloadPdfController extends Controller
{
    public function __invoke(WinnerPrizeEnum $prize, RaffleService $raffleService)
    {
        // Fetch the winners directly from the database
        $winners = $raffleService->getExistingWinners($prize);

        // If no winners exist, safely redirect back
        if (empty($winners)) {
            return back();
        }

        $prizeName = $prize->label();
        $filename = "raffle_winners_{$prize->value}_".now()->format('Ymd_His').'.pdf';

        // Load the view and download
        return FacadesPdf::view('pdf.winners', [
            'winners' => $winners,
            'prizeName' => $prizeName,
        ])
            ->name($filename);

    }
}
