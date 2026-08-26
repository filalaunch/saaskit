<?php

namespace App\Filament\Resources\AiApiKeyResource\Pages;

use App\Filament\Resources\AiApiKeyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiApiKeys extends ListRecords
{
    protected static string $resource = AiApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
