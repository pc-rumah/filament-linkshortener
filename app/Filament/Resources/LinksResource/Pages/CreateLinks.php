<?php

namespace App\Filament\Resources\LinksResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use App\Filament\Resources\LinksResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLinks extends CreateRecord
{
    protected static string $resource = LinksResource::class;

    // untuk membuat slug otomatis dan mengisi user_id
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        // Ambil plan
        $plan = $user->plan ?? \App\Models\Plans::where('slug', 'basic')->first();
        $maxLinks = $plan->features['max_links'];

        // Hitung link user
        $currentLinks = \App\Models\Links::where('user_id', $user->id)->count();

        if ($currentLinks >= $maxLinks) {
            Notification::make()
                ->title('Limit tercapai')
                ->danger()
                ->body("Kamu sudah mencapai limit link untuk plan {$plan->name}. Upgrade ke Pro.")
                ->send();

            $this->halt();
        }

        // Auto set user_id
        $data['user_id'] = $user->id;

        // Auto generate slug jika kosong
        if (empty($data['slug'])) {
            $data['slug'] = Str::random(6);
        }

        return $data;
    }
}
