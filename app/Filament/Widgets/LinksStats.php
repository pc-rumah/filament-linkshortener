<?php

namespace App\Filament\Widgets;

use App\Models\Links;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class LinksStats extends BaseWidget
{
    protected static ?int $sort = 3;
    protected function getStats(): array
    {
        $user = auth()->user();
        return [
            Stat::make('Total Links', Links::where('user_id', $user->id)->count()),
            Stat::make('Total Clicks', Links::sum('clicks_count')),
            Stat::make('Clicks Today', Links::whereDate('created_at', today())->sum('clicks_count')),
            Stat::make('Plan', $user->plan ? $user->plan->name : "No Plan"),
        ];
    }
}
