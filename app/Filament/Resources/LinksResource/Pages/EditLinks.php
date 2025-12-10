<?php

namespace App\Filament\Resources\LinksResource\Pages;

use App\Filament\Resources\LinksResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLinks extends EditRecord
{
    protected static string $resource = LinksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // agar user_id tetap
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function authorizeAccess(): void
    {
        $record = $this->getRecord();

        if (!auth()->user()->hasRole('superadmin') && $record->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
