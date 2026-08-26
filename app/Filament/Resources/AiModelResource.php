<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiModelResource\Pages;
use App\Models\AiModel;
use App\Models\AiProvider;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiModelResource extends Resource
{
    protected static ?string $model = AiModel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'AI Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Model details')
                ->schema([
                    Select::make('ai_provider_id')
                        ->label('Provider')
                        ->relationship('provider', 'name')
                        ->required()
                        ->searchable(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Display name shown in the admin panel, e.g. "GPT-4.1".'),
                    TextInput::make('model_key')
                        ->required()
                        ->maxLength(255)
                        ->helperText('The exact identifier sent to the provider\'s API, e.g. "gpt-4.1" or "claude-sonnet-5".'),
                    TextInput::make('context_window')
                        ->numeric()
                        ->suffix('tokens'),
                    CheckboxList::make('capabilities')
                        ->options([
                            'text' => 'Text',
                            'vision' => 'Vision',
                            'tools' => 'Tool use',
                        ])
                        ->columns(3),
                    Toggle::make('is_active')->default(true),
                ])
                ->columns(2),
            Section::make('Pricing (per 1,000 tokens)')
                ->description('Used to compute the cost logged for platform-key usage. Verify against the provider\'s current pricing page — these change frequently.')
                ->schema([
                    TextInput::make('input_price_per_1k')
                        ->label('Input price')
                        ->numeric()
                        ->prefix('$')
                        ->step(0.000001)
                        ->required(),
                    TextInput::make('output_price_per_1k')
                        ->label('Output price')
                        ->numeric()
                        ->prefix('$')
                        ->step(0.000001)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider.name')->badge()->sortable(),
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('model_key')->fontFamily('mono')->copyable(),
                TextColumn::make('input_price_per_1k')
                    ->label('In $/1k')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 6)),
                TextColumn::make('output_price_per_1k')
                    ->label('Out $/1k')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 6)),
                ToggleColumn::make('is_active'),
            ])
            ->filters([
                SelectFilter::make('ai_provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name'),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiModels::route('/'),
            'create' => Pages\CreateAiModel::route('/create'),
            'edit' => Pages\EditAiModel::route('/{record}/edit'),
        ];
    }
}
