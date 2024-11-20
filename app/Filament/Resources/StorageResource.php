<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageResource\Pages;
use App\Filament\Resources\StorageResource\RelationManagers;
use App\Models\Storage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StorageResource extends Resource
{
    protected static ?string $model = Storage::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(['local' => 'Local', 's3' => 'S3'])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn(Forms\Components\Select $component) => $component
                        ->getContainer()
                        ->getComponent('dynamicTypeFields')
                        ->getChildComponentContainer()
                        ->fill()),
                Forms\Components\Grid::make(1)
                    ->schema(fn(Forms\Get $get): array => match ($get('type')) {
                        'local' => [
                            Forms\Components\TextInput::make('path'),
                        ],
                        's3' => [
                            Forms\Components\TextInput::make('access_key_id'),
                            Forms\Components\TextInput::make('secret_access_key'),
                            Forms\Components\TextInput::make('region'),
                            Forms\Components\TextInput::make('bucket'),
                        ],
                        default => [],
                    })
                    ->key('dynamicTypeFields')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('host')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('port')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bucket')
                    ->searchable(),
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListStorages::route('/'),
            'create' => Pages\CreateStorage::route('/create'),
            'edit' => Pages\EditStorage::route('/{record}/edit'),
        ];
    }
}
