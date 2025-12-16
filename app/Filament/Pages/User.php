<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ListUser;
use App\Filament\Widgets\UserOverview;
use Filament\Pages\Page;

class User extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.pages.user';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_User');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserOverview::class,
            ListUser::class,
        ];
    }
}
