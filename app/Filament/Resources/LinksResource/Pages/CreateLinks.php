<?php

namespace App\Filament\Resources\LinksResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\LinksResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLinks extends CreateRecord
{
    protected static string $resource = LinksResource::class;

    // untuk membuat slug otomatis dan mengisi user_id
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (empty($data['slug'])) {
            $data['slug'] = Str::random(6);
        }

        return $data;
    }
}
