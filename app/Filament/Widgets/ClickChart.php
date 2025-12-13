<?php

namespace App\Filament\Widgets;

use App\Models\Links;
use Filament\Widgets\ChartWidget;

class ClickChart extends ChartWidget
{
    protected static ?string $heading = '7 Hari terakhir Klik';
    protected static ?int $sort = 6;
    protected function getData(): array
    {
        $dates = collect(range(6, 0))->map(function ($i) {
            return now()->subDays($i)->format('Y-m-d');
        });

        $clicks = $dates->map(function ($date) {
            return Links::whereDate('created_at', $date)->sum('clicks_count');
        });

        return [
            'datasets' => [
                [
                    'label' => 'Klik',
                    'data' => $clicks,
                ],
            ],
            'labels' => $dates->map(fn($d) => date('d M', strtotime($d))),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
