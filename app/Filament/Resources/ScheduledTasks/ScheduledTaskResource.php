<?php

namespace App\Filament\Resources\ScheduledTasks;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ScheduledTasks\Pages\ListScheduledTasks;
use App\Filament\Resources\ScheduledTasks\Pages\CreateScheduledTask;
use App\Filament\Resources\ScheduledTasks\Pages\EditScheduledTask;
use App\Filament\Resources\ScheduledTaskResource\Pages;
use App\Filament\Resources\ScheduledTaskResource\RelationManagers;
use App\Models\ScheduledTask;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduledTaskResource extends Resource
{
    protected static ?string $model = ScheduledTask::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
                Select::make('connection_id')
                    ->relationship('connection', 'name')
                    ->required(),
                Select::make('cron_expression')
                    ->options([
                        '* * * * *' => 'Cada minuto (* * * * *)',
                        '*/5 * * * *' => 'Cada 5 minutos (*/5 * * * *)',
                        '*/15 * * * *' => 'Cada 15 minutos (*/15 * * * *)',
                        '*/30 * * * *' => 'Cada 30 minutos (*/30 * * * *)',
                        '0 * * * *' => 'Cada hora (0 * * * *)',
                        '0 0 * * *' => 'Todos los días a la medianoche (0 0 * * *)',
                        '0 12 * * *' => 'Todos los días a las 12:00 PM (0 12 * * *)',
                        '0 8 * * 1' => 'Todos los lunes a las 8:00 AM (0 8 * * 1)',
                        '0 0 1 * *' => 'El primer día de cada mes a medianoche (0 0 1 * *)',
                        '0 */2 * * *' => 'Cada 2 horas (0 */2 * * *)',
                        '30 3 * * *' => 'A las 3:30 AM todos los días (30 3 * * *)',
                        '0 6 * * 1-5' => 'De lunes a viernes a las 6:00 AM (0 6 * * 1-5)',
                        '0 9 1-7 * 1' => 'El primer lunes de cada mes a las 9:00 AM (0 9 1-7 * 1)',
                        '*/5 9-17 * * *' => 'Cada 5 minutos, solo durante las horas de trabajo (9:00 AM - 5:00 PM) (*/5 9-17 * * *)',
                        '45 23 * * 0' => 'Cada domingo a las 11:45 PM (45 23 * * 0)',
                        '0 12 1 * *' => 'El primer día de cada mes a las 12:00 PM (0 12 1 * *)',
                        '30 14 * * 1-5' => 'De lunes a viernes a las 2:30 PM (30 14 * * 1-5)',
                        '0 9 * * 1-5' => 'Cada lunes a viernes a las 9:00 AM (0 9 * * 1-5)',
                        '15 5 * * *' => 'Cada día a las 5:15 AM (15 5 * * *)',
                        '0 0 * * 0' => 'Cada domingo a la medianoche (0 0 * * 0)',
                        '0 10 1-15 * *' => 'Del 1 al 15 de cada mes a las 10:00 AM (0 10 1-15 * *)',
                        '0 0 * * 6' => 'Cada sábado a medianoche (0 0 * * 6)',
                        '0 0 1-7 * 0' => 'El primer domingo de cada mes a medianoche (0 0 1-7 * 0)',
                        '0 9 1-7 * 0' => 'El primer domingo de cada mes a las 9:00 AM (0 9 1-7 * 0)',
                        '0 0 * 8,12 *' => 'Cada 1 de agosto y diciembre a medianoche (0 0 * 8,12 *)',
                        '0 6 1 * 1' => 'El primer día de cada mes a las 6:00 AM, solo si es lunes (0 6 1 * 1)',
                        '15 10 * * 6' => 'Cada sábado a las 10:15 AM (15 10 * * 6)',
                        '0 18 * * 0,6' => 'Cada sábado y domingo a las 6:00 PM (0 18 * * 0,6)',
                        '0 0 1 1 *' => 'El primer día del año a medianoche (1 de enero) (0 0 1 1 *)',
                        '0 0 25 12 *' => 'Cada 25 de diciembre a medianoche (0 0 25 12 *)',
                        '0 8-17/2 * * *' => 'Cada 2 horas desde las 8 AM hasta las 5 PM (0 8-17/2 * * *)',
                        '*/10 9-17 * * 1-5' => 'Cada 10 minutos durante las horas laborales (9:00 AM - 5:00 PM, de lunes a viernes) (*/10 9-17 * * 1-5)',
                        '0 0 1,15 * *' => 'El día 1 y 15 de cada mes a medianoche (0 0 1,15 * *)',
                    ])
                    ->required(),
                TextInput::make('name')
                    ->live(onBlur: true)
                    ->prefix('Task_')
                    ->suffix('_YYYY-MM-DD_HH-mm-ss')
                    ->required()
                    ->afterStateUpdated(function (Set $set, ?string $state, ?string $old) {
                        $set('name', str_replace(' ', '_', $state));
                    })
                    ->placeholder('name_other_test')
                    ->maxLength(255),
                DateTimePicker::make('last_executed_at')
                    ->hidden(fn(string $context): bool => $context === 'create')
                    ->disabled(fn(string $context): bool => $context === 'edit'),
                Toggle::make('enabled')
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false)
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('connection.name')
                    ->badge()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('cron_expression')
                    ->badge()
                    ->searchable(),
                IconColumn::make('enabled')
                    ->boolean(),
                TextColumn::make('last_executed_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                ->modalWidth(Width::SevenExtraLarge)
                ->slideOver(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScheduledTasks::route('/'),
            'create' => CreateScheduledTask::route('/create'),
            'edit' => EditScheduledTask::route('/{record}/edit'),
        ];
    }
}
