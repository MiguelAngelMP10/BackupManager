<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduledTaskResource\Pages;
use App\Filament\Resources\ScheduledTaskResource\RelationManagers;
use App\Models\ScheduledTask;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduledTaskResource extends Resource
{
    protected static ?string $model = ScheduledTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
                Forms\Components\Select::make('connection_id')
                    ->relationship('connection', 'name')
                    ->required(),
                Forms\Components\Select::make('cron_expression')
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
                Forms\Components\TextInput::make('name')
                    ->live(onBlur: true)
                    ->prefix('Task_')
                    ->suffix('_YYYY-MM-DD_HH-mm-ss')
                    ->required()
                    ->afterStateUpdated(function (Forms\Set $set, ?string $state, ?string $old) {
                        $set('name', str_replace(' ', '_', $state));
                    })
                    ->placeholder('name_other_test')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('last_executed_at')
                    ->hidden(fn(string $context): bool => $context === 'create')
                    ->disabled(fn(string $context): bool => $context === 'edit'),
                Forms\Components\Toggle::make('enabled')
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
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('connection.name')
                    ->badge()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cron_expression')
                    ->badge()
                    ->searchable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_executed_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->modalWidth(MaxWidth::SevenExtraLarge)
                ->slideOver(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListScheduledTasks::route('/'),
            'create' => Pages\CreateScheduledTask::route('/create'),
            'edit' => Pages\EditScheduledTask::route('/{record}/edit'),
        ];
    }
}
