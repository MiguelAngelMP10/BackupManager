<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BackupResource\Pages;
use App\Filament\Resources\BackupResource\RelationManagers;
use App\Models\Backup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupResource extends Resource
{
    protected static ?string $model = Backup::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('connection_id')
                    ->relationship('connection', 'name')
                    ->required(),
                Forms\Components\Select::make('storage_id')
                    ->relationship('storage', 'name')
                    ->required(),
                Forms\Components\TextInput::make('file_name')
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('connection.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('storage.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_name')
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
                Tables\Actions\ViewAction::make()
                    ->modalWidth(MaxWidth::SevenExtraLarge)
                    ->slideOver(),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        $storage = $record->storage;
                        $config = [
                            'driver' => 's3',
                            'key' => $storage->access_key_id,
                            'secret' => $storage->access_key_secret,
                            'region' => $storage->region,
                            'bucket' => $storage->bucket,
                        ];

                        $disk = Storage::build($config);

                        if ($disk->exists($record->file_name)) {
                            $disk->delete($record->file_name);
                            Notification::make()
                                ->title('Archivo eliminado correctamente de S3.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('El archivo no existe en S3.')
                                ->danger()
                                ->send();
                        }
                    })
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
            'index' => Pages\ListBackups::route('/'),
        ];
    }
}
