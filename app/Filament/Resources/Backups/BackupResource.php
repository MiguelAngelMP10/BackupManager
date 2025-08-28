<?php

namespace App\Filament\Resources\Backups;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\Width;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Backups\Pages\ListBackups;
use App\Filament\Resources\BackupResource\Pages;
use App\Filament\Resources\BackupResource\RelationManagers;
use App\Models\Backup;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use MrPowerUp\FilamentSqlField\FilamentSqlField;

class BackupResource extends Resource
{
    protected static ?string $model = Backup::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cloud-arrow-up';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('connection_id')
                    ->relationship('connection', 'name')
                    ->required(),
                TextInput::make('file_name')
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->columns([
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('connection.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('file_name')
                    ->searchable(),
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
                Action::make('View Backup')
                    ->fillForm(function (Backup $backup) {
                        $content = Storage::disk('s3')->get($backup->file_name);
                        return [
                            'content' => $content
                        ];
                    })
                    ->schema([
                        Textarea::make('content')
                            ->disabled()
                            ->rows(50)
                            ->columnSpanFull()
                    ])
                    ->modalWidth(Width::SevenExtraLarge)
                    ->slideOver()
                    ->icon('heroicon-m-viewfinder-circle'),

                Action::make('Download Backup')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(function (Backup $backup) {
                        Notification::make()
                            ->title('Download successfully')
                            ->success()
                            ->send();
                        return Storage::disk('s3')->download($backup->file_name);
                    }),
                ViewAction::make()
                    ->modalWidth(Width::SevenExtraLarge)
                    ->slideOver(),
                DeleteAction::make()
                    ->after(function (Backup $record) {

                        $disk = Storage::disk('s3');

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
            'index' => ListBackups::route('/'),
        ];
    }
}
