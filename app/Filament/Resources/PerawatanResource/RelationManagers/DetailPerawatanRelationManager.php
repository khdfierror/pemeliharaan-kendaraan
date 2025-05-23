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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class DetailPerawatanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPerawatan';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->tahunAktif();

        return $query;
    }

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
                    ->preload()
                    ->placeholder('Pilih Jenis Perawatan'),
                Forms\Components\Textarea::make('uraian')
                    ->autosize(),
                Forms\Components\TextInput::make('volume')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($set, $get) => $set('total', (int) $get('volume') * (int) $get('harga_satuan'))),
                Forms\Components\TextInput::make('harga_satuan')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($set, $get) => $set('total', (int) $get('volume') * (int) $get('harga_satuan'))),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->disabled()
                    ->required()
                    ->dehydrated(true),
                Forms\Components\TextInput::make('masa_pakai')
                    ->label('Masa Pakai')
                    ->suffix('Bulan')
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
            ])->columns(1)->inlineLabel();
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription(null)
            // ->recordTitleAttribute('catatan')
            ->columns([
                Tables\Columns\TextColumn::make('jenisPerawatan.nama')
                    ->label('Jenis Perawatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uraian')
                    ->wrap(),
                Tables\Columns\TextColumn::make('volume'),
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
                Tables\Columns\TextColumn::make('habis_masa_pakai')
                    ->formatStateUsing(function (Model $record) {
                        $tanggal = $record->habis_masa_pakai?->locale('id')->translatedFormat('l, d F Y');

                        return new HtmlString(<<<HTML
                            <div class="text-center">
                                <div>$tanggal</div>
                            </div>
                        HTML);
                    })
                    ->searchable([
                        'tanggal',
                    ])
                    ->label('Habis Masa Pakai'),
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
                    ->modalHeading('Tambah Detail Perawatan')
                    ->hidden(fn() => Auth::user()?->hasRole('pimpinan')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->iconButton()
                    ->hidden(fn() => Auth::user()?->hasRole('pimpinan')),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->iconButton()
                    ->hidden(fn() => Auth::user()?->hasRole('pimpinan')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
