<?php

namespace App\Filament\Resources\AiApiKeyResource\Pages;

use App\Filament\Resources\AiApiKeyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiApiKey extends EditRecord
{
    protected static string $resource = AiApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Never populate the key field with the actual decrypted value —
        // the form stays blank, and staying blank on submit means "keep
        // the existing key" (handled by dehydrated() on the field).
        unset($data['encrypted_key']);

        return $data;
    }
}
