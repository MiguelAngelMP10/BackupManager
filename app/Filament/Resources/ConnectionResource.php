<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConnectionResource\Pages;
use App\Filament\Resources\ConnectionResource\RelationManagers;
use App\Models\Connection;
use Filament\Actions\ReplicateAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;


class ConnectionResource extends Resource
{
    protected static ?string $model = Connection::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Select::make('driver')
                    ->options([
                        'mysql' => 'MySQL',
                        'mariadb' => 'MariaDB',
                        'pgsql' => 'PostgreSQL',
                        'sqlsrv' => 'SQL Server',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('host')
                    ->required(),
                Forms\Components\TextInput::make('port')
                    ->required()
                    ->numeric()
                    ->default(3306),
                Forms\Components\TextInput::make('database')
                    ->required(),
                Forms\Components\TextInput::make('username')
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->confirmed()
                    ->password()
                    ->revealable()
                    ->required(),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->required(),
            ])->columns(4);
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
                Tables\Columns\TextColumn::make('driver')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('host')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('port')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('database')
                    ->badge()
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
                Tables\Actions\ReplicateAction::make(),
                Tables\Actions\Action::make('Test connection')
                    ->icon('heroicon-o-globe-alt')
                    ->action(function (Connection $record) {
                        DB::purge($record->driver);
                        $config = [
                            'driver' => $record->driver,
                            'host' => $record->host,
                            'port' => $record->port,
                            'database' => $record->database,
                            'username' => $record->username,
                            'password' => $record->password,
                            'charset' => 'utf8mb4',
                            'collation' => 'utf8mb4_unicode_ci',
                            'prefix' => '',
                        ];

                        config(['database.connections.mysql_connect' => $config]);

                        try {
                            $connection = DB::connection($record->driver);
                            $connection->getPdo();
                            Notification::make()
                                ->title('Conexión exitosa')
                                ->body('La conexión a la base de datos fue establecida correctamente.')
                                ->success()
                                ->send();
                        } catch (PDOException $e) {
                            Notification::make()
                                ->title('Error en la conexión:')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
            'index' => Pages\ListConnections::route('/'),
            'create' => Pages\CreateConnection::route('/create'),
            'edit' => Pages\EditConnection::route('/{record}/edit'),
        ];
    }
}
