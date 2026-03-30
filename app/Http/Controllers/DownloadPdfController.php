<?php

namespace App\Http\Controllers;

use App\Enum\WinnerPrizeEnum;
use App\Services\RaffleService;

use function Spatie\LaravelPdf\Support\pdf;

class DownloadPdfController extends Controller
{
    public function preview(WinnerPrizeEnum|string $prize, RaffleService $raffleService)
    {
        if (! (in_array($prize, array_column(WinnerPrizeEnum::cases(), 'value')))) {
            $winners = $raffleService->getExistingWinners();

            if (empty($winners)) {
                return back();
            }

            $filename = 'raffle_winners_all_'.now()->format('Ymd_His').'.pdf';

        } else {
            $prize = WinnerPrizeEnum::from($prize);

            $winners = $raffleService->getExistingWinners($prize);

            if (empty($winners)) {
                return back();
            }

            $filename = "raffle_winners_{$prize->value}_".now()->format('Ymd_His').'.pdf';
            $prize = $prize->label();
        }

        return view('pdf.winners', [
            'winners' => $winners,
            'prizeName' => $prize,
        ]);
        // ->footerView('pdf.footer-view')
        // ->name($filename);

    }

    public function render(WinnerPrizeEnum|string $prize, RaffleService $raffleService)
    {
        if (! (in_array($prize, array_column(WinnerPrizeEnum::cases(), 'value')))) {
            $winners = $raffleService->getExistingWinners();

            if (empty($winners)) {
                return back();
            }

            $filename = 'raffle_winners_all_'.now()->format('Ymd_His').'.pdf';

        } else {
            $prize = WinnerPrizeEnum::from($prize);

            $winners = $raffleService->getExistingWinners($prize);

            if (empty($winners)) {
                return back();
            }

            $filename = "raffle_winners_{$prize->value}_".now()->format('Ymd_His').'.pdf';
            $prize = $prize->label();
        }

        return pdf('pdf.winners', [
            'winners' => $winners,
            'prizeName' => $prize,
        ])
            ->footerView('pdf.footer-view')
            ->name($filename);

    }
}
