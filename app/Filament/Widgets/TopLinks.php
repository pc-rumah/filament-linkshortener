<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Links;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopLinks extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Links::orderByDesc('clicks')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('original_url')->limit(30),
                Tables\Columns\TextColumn::make('clicks')
                    ->label('Clicks')
                    ->getStateUsing(fn($record) => $record->clicks()->count()),
            ]);
    }

    protected static ?string $heading = 'Top Links';
}
