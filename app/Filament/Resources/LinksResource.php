<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinksResource\Pages;
use App\Filament\Resources\LinksResource\RelationManagers;
use App\Models\Links;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LinksResource extends Resource
{
    protected static ?string $model = Links::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug')
                    ->helperText('Biarkan kosong untuk generate otomatis')
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('original_url')
                    ->rules('required', 'url')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Forms\Components\DateTimePicker::make('expired_at')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('original_url')
                    ->limit(30)
                    ->label('URL Asli'),
                Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qr_code_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('clicks_count')
                    ->numeric()
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
