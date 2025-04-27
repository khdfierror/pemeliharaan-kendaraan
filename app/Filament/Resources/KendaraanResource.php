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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KendaraanResource extends Resource
{
    protected static ?string $model = Kendaraan::class;

    protected static ?string $navigationIcon = 'carbon-car';

    protected static ?string $navigationLabel = 'Kendaraan';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'kendaraan';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->tahunAktif();

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor_plat')
                    ->label('Nomor Plat')
                    ->required(),
                Forms\Components\Select::make('jumlah_roda')
                    ->label('Jumlah Roda')
                    ->placeholder('Pilih Jumlah Roda')
                    ->options([
                        '2' => 'Roda 2',
                        '4' => 'Roda 4',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('tahun_produksi')
                    ->label('Tahun Produksi')
                    ->numeric(),
                Forms\Components\Select::make('merk_id')
                    ->label('Merk')
                    ->relationship('merk', 'nama')
                    ->placeholder('Pilih Merk Kendaraan')
                    ->native(false),
                Forms\Components\TextInput::make('nama')
                    ->required(),
            ])->columns(1)->inlineLabel();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('tahun_produksi')
                    ->label('Tahun Produksi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Kendaraan')
                    ->formatStateUsing(fn(Model $record, $state) => ucwords(strtolower($record->merk->nama)) . ' ' . $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_plat')
                    ->label('No. Plat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_roda')
                    ->label('Roda')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable()
                    ->wrap()
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jumlah_roda')
                    ->label('Jumlah Roda')
                    ->options([
                        '2' => 'Roda 2',
                        '4' => 'Roda 4',
                    ])->native(false)
                    ->placeholder('Pilih Jumlah Roda'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->iconButton(),
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
