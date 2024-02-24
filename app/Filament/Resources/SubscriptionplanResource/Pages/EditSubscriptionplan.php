<?php

namespace App\Filament\Resources\SubscriptionplanResource\Pages;

use App\Filament\Resources\SubscriptionplanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionplan extends EditRecord
{
    protected static string $resource = SubscriptionplanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
