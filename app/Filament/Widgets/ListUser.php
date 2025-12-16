<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ListUser extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_ListUser');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->state(
                        fn($record, $livewire) => ($livewire->getTablePage() - 1) * $livewire->getTableRecordsPerPage()
                            + $livewire->getTableRecords()->search($record) + 1
                    ),
                TextColumn::make('name')->label('Name')->sortable()->searchable(),
                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->label('Email')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('created_at')->label('Sign Up Date')->dateTime('d M Y')->sortable(),
                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'basic' => 'primary',
                        'pro' => 'success',
                        default => 'secondary',
                    })
            ]);
    }
}
