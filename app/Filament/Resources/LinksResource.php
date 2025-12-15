<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Links;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LinksResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LinksResource\RelationManagers;
use Filament\Tables\Columns\TextColumn;

class LinksResource extends Resource
{
    protected static ?string $model = Links::class;

    protected function beforeCreate()
    {
        $user = auth()->user();

        if (!$user->canCreateMoreLinks()) {
            Notification::make()
                ->title('Limit tercapai')
                ->body('Kamu telah mencapai limit link untuk plan ' . $user->plan->name)
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('original_url')
                    ->rules('required', 'url')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->helperText('Biarkan kosong untuk generate otomatis')
                    ->unique(ignoreRecord: true),
                Forms\Components\DatePicker::make('expired_at')
                    ->nullable()
                    ->format('d/m/Y'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->offColor('danger')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('original_url')
                    ->limit(30)
                    ->label('URL Asli'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('short_url')
                    ->label('URL Pendek')
                    ->getStateUsing(fn($record) => route('link.redirect', $record->slug))
                    ->copyable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('clicks_count')
                    ->numeric()
                    ->label('Klik')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('qr')
                    ->label('QR')
                    ->getStateUsing(fn($record) => route('link.qr', $record->slug))
                    ->square(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLinks::route('/create'),
            'edit' => Pages\EditLinks::route('/{record}/edit'),
        ];
    }
}
