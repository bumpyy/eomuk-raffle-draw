<?php

namespace App\Http\Controllers;

use App\Enum\WinnerPrizeEnum;
use App\Services\RaffleService;

use function Spatie\LaravelPdf\Support\pdf;

class DownloadPdfController extends Controller
{
    public function __invoke(WinnerPrizeEnum|string $prize, RaffleService $raffleService)
    {

        if (! ($prize instanceof WinnerPrizeEnum)) {
            $winners = $raffleService->getExistingWinners();

            if (empty($winners)) {
                return back();
            }

            $filename = 'raffle_winners_all_'.now()->format('Ymd_His').'.pdf';

        } else {
            $winners = $raffleService->getExistingWinners($prize);

            if (empty($winners)) {
                return back();
            }

            $filename = "raffle_winners_{$prize->value}_".now()->format('Ymd_His').'.pdf';
        }

        return pdf('pdf.winners', [
            'winners' => $winners,
        ])
            ->footerView('pdf.footer-view')
            ->name($filename);

    }
}
