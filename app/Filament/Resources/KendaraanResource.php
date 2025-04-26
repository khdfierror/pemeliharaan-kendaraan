<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KendaraanResource\Pages;
use App\Filament\Resources\KendaraanResource\RelationManagers;
use App\Models\Kendaraan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KendaraanResource extends Resource
{
    protected static ?string $model = Kendaraan::class;

    protected static ?string $navigationIcon = 'carbon-car';

    protected static ?string $navigationLabel = 'Kendaraan';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'kendaraan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor_plat')
                    ->label('Nomor Plat')
                    ->required(),
                Forms\Components\TextInput::make('jumlah_roda')
                    ->label('Jumlah Roda')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tahun')
                    ->numeric(),
                Forms\Components\Select::make('merek')
                    ->options([
                        'toyota' => 'Toyota',
                        'daihatsu' => 'Daihatsu',
                        'honda' => 'Honda',
                        'mitsubishi' => 'Mitsubishi',
                        'suzuki' => 'Suzuki',
                    ])->native(false),
                Forms\Components\TextInput::make('nama')
                    ->required()
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('nomor_plat')
                    ->label('Nomor Plat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_roda')
                    ->label('Jumlah Roda')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merek')
                    ->searchable()
                    ->formatStateUsing(fn($state) => ucwords(strtolower($state))),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageKendaraan::route('/'),
        ];
    }
}
