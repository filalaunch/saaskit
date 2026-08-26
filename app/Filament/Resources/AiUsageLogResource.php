<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiUsageLogResource\Pages;
use App\Models\AiUsageLog;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AiUsageLogResource extends Resource
{
    protected static ?string $model = AiUsageLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'AI Management';

    protected static ?string $navigationLabel = 'Usage Logs';

    protected static ?int $navigationSort = 4;

    // Read-only by design: usage logs are a record of what happened, not
    // something an admin should hand-edit or delete.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('user.name')->searchable()->label('User'),
                TextColumn::make('model.name')->label('Model')->badge(),
                TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state) => $state === 'byok' ? 'success' : 'gray'),
                TextColumn::make('input_tokens')->label('In tokens')->numeric()->sortable(),
                TextColumn::make('output_tokens')->label('Out tokens')->numeric()->sortable(),
                TextColumn::make('computed_cost')
                    ->label('Cost')
                    ->formatStateUsing(fn (?int $state) => $state === null ? '—' : '$' . number_format($state / 100, 4))
                    ->sortable(),
                TextColumn::make('feature_context')->label('Feature')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->options([
                        'platform_key' => 'Platform key',
                        'byok' => 'BYOK',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiUsageLogs::route('/'),
        ];
    }
}
