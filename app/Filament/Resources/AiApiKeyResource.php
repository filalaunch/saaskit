<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiApiKeyResource\Pages;
use App\Models\AiApiKey;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AiApiKeyResource extends Resource
{
    protected static ?string $model = AiApiKey::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'AI Management';

    protected static ?string $navigationLabel = 'Platform API Keys';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Platform key')
                ->description('These keys serve AI requests for customers who haven\'t added their own BYOK key. Never shown again after saving — leave blank on edit to keep the existing key unchanged.')
                ->schema([
                    Select::make('ai_provider_id')
                        ->label('Provider')
                        ->relationship('provider', 'name')
                        ->required()
                        ->searchable(),
                    TextInput::make('label')
                        ->maxLength(255)
                        ->helperText('A friendly name for your own reference, e.g. "Production OpenAI key".'),
                    TextInput::make('encrypted_key')
                        ->label('API key')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->helperText(fn (string $operation) => $operation === 'edit'
                            ? 'Leave blank to keep the current key unchanged.'
                            : null),
                    Toggle::make('is_active')->default(true),
                    Toggle::make('is_default')
                        ->helperText('Used first if this provider has more than one active platform key.'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider.name')->badge()->sortable(),
                TextColumn::make('label')->searchable(),
                TextColumn::make('masked_key')->label('Key')->fontFamily('mono'),
                ToggleColumn::make('is_active'),
                ToggleColumn::make('is_default'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiApiKeys::route('/'),
            'create' => Pages\CreateAiApiKey::route('/create'),
            'edit' => Pages\EditAiApiKey::route('/{record}/edit'),
        ];
    }
}
