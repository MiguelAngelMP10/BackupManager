<?php

namespace App\Filament\Resources\Connections;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Connections\Pages\ListConnections;
use App\Filament\Resources\Connections\Pages\CreateConnection;
use App\Filament\Resources\Connections\Pages\EditConnection;
use App\Filament\Resources\ConnectionResource\Pages;
use App\Filament\Resources\ConnectionResource\RelationManagers;
use App\Models\Connection;
use Filament\Actions\ReplicateAction;
use Filament\Forms;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('driver')
                    ->options([
                        'mysql' => 'MySQL',
                        'mariadb' => 'MariaDB',
                        'pgsql' => 'PostgreSQL',
                        'sqlsrv' => 'SQL Server',
                    ])
                    ->required(),
                TextInput::make('host')
                    ->required(),
                TextInput::make('port')
                    ->required()
                    ->numeric()
                    ->default(3306),
                TextInput::make('database')
                    ->required(),
                TextInput::make('username')
                    ->required(),
                TextInput::make('password')
                    ->confirmed()
                    ->password()
                    ->revealable()
                    ->required(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->required(),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('driver')
                    ->badge()
                    ->searchable(),
                TextColumn::make('host')
                    ->badge()
                    ->searchable(),
                TextColumn::make('port')
                    ->badge()
                    ->sortable(),
                TextColumn::make('database')
                    ->badge()
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
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ReplicateAction::make(),
                Action::make('Test connection')
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
            'index' => ListConnections::route('/'),
            'create' => CreateConnection::route('/create'),
            'edit' => EditConnection::route('/{record}/edit'),
        ];
    }
}
