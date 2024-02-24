<?php

namespace App\Filament\Resources\SubscriptionplanResource\Pages;

use App\Filament\Resources\SubscriptionplanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionplans extends ListRecords
{
    protected static string $resource = SubscriptionplanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
