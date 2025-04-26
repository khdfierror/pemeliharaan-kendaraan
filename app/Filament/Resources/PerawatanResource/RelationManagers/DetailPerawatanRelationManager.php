<?php

namespace App\Filament\Resources\PerawatanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class DetailPerawatanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPerawatan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_perawatan_id')
                    ->label('Jenis Perawatan')
                    ->relationship('jenisPerawatan', 'nama')
                    ->native(false)
                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->nama} ({$record->kode})")
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('jumlah')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($set, $get) => $set('total', (int) $get('jumlah') * (int) $get('harga_satuan'))),
                Forms\Components\TextInput::make('harga_satuan')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($set, $get) => $set('total', (int) $get('jumlah') * (int) $get('harga_satuan'))),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->disabled()
                    ->required()
                    ->dehydrated(true),
                Forms\Components\TextInput::make('masa_pakai')
                    ->label('Masa Pakai')
                    ->helperText('*Dalam Bulan')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('km_awal')
                    ->label('KM Awal')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('km_akhir')
                    ->label('KM Akhir')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('catatan')
                    ->required()
                    ->maxLength(255),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription(null)
            // ->recordTitleAttribute('catatan')
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('jenisPerawatan.nama')
                    ->label('Jenis Perawatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah'),
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('total')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('masa_pakai')
                    ->label('Masa Pakai')
                    ->formatStateUsing(function (Model $record) {
                        return new HtmlString(
                            <<<HTML
                                <div class="text-center">
                                    <div>{$record->masa_pakai} Bulan</div>
                                </div>
                            HTML
                        );
                    }),
                Tables\Columns\TextColumn::make('km_awal')
                    ->label('Kilometer')
                    ->formatStateUsing(function (Model $record, ?string $state) {
                        return new HtmlString(Blade::render(<<<'BLADE'
                        <div class="text-sm text-gray-500">
                                    <div>Kilometer Awal : {{ number_format($record->km_awal, 0, ',', '.') }} Km</div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <div>Kilometer Akhir : {{ number_format($record->km_akhir, 0, ',', '.') }} Km</div>
                                </div>
                        BLADE, ['state' => $state, 'record' => $record]));
                    })->html(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Detail')
                    ->modalHeading('Tambah Detail Perawatan'),
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
}
