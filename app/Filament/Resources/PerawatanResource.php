<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerawatanResource\Pages;
use App\Filament\Resources\PerawatanResource\RelationManagers;
use App\Models\Perawatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class PerawatanResource extends Resource
{
    protected static ?string $model = Perawatan::class;

    protected static ?string $navigationIcon = 'carbon-vehicle-services';

    protected static ?string $navigationLabel = 'Perawatan';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'perawatan';

    protected static ?string $pluralLabel = 'Perawatan';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->tahunAktif()
            ->with('kendaraan');

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(6)
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\Select::make('kendaraan_id')
                                ->label('Kendaraan')
                                ->relationship('kendaraan', 'nama')
                                ->required()
                                ->native(false)
                                ->placeholder('Pilih Kendaraan')
                                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->merk->nama} {$record->nama} ({$record->nomor_plat}) (Roda {$record->jumlah_roda})")
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('nomor_nota')
                                ->label('Nomor Nota')
                                ->required(),
                            Forms\Components\DatePicker::make('tanggal_nota')
                                ->label('Tanggal Nota')
                                ->required()
                                ->format('Y-m-d')
                                ->displayFormat('d/m/Y')
                                ->native(false)
                                ->suffixIcon('carbon-event-schedule'),
                            Forms\Components\Textarea::make('keterangan')
                                ->autosize(),
                        ])->columnSpan(4),
                    ])
            ])->inlineLabel();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('kendaraan.nama')
                    ->label('Kendaraan')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        $merk = $record->kendaraan?->merk?->nama ?? '-';
                        $nama = $record->kendaraan?->nama ?? '-';
                        $plat = $record->kendaraan?->nomor_plat ?? '-';
                        $roda = $record->kendaraan?->jumlah_roda ?? '-';

                        return "{$merk} {$nama} ({$plat}) (Roda {$roda})";
                    }),
                Tables\Columns\TextColumn::make('tahun'),
                Tables\Columns\TextColumn::make('nomor_nota')
                    ->searchable()
                    ->label('Nomor Nota'),
                Tables\Columns\TextColumn::make('tanggal_nota')
                    ->formatStateUsing(function (Model $record) {
                        $tanggal = $record->tanggal_nota?->locale('id')->translatedFormat('l, d F Y');

                        return new HtmlString(<<<HTML
                            <div class="text-center">
                                <div>$tanggal</div>
                            </div>
                        HTML);
                    })
                    ->searchable([
                        'tanggal',
                    ])
                    ->label('Tanggal Nota'),
                Tables\Columns\TextColumn::make('keterangan')
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jumlah_roda')
                    ->label('Jumlah Roda')
                    ->options([
                        null => 'Semua',
                        2 => 'Roda 2',
                        4 => 'Roda 4',
                    ])
                    ->query(function ($query, array $data) {
                        if (filled($data['value'])) {
                            return $query->whereHas('kendaraan', function ($q) use ($data) {
                                $q->where('jumlah_roda', (int) $data['value']);
                            });
                        }

                        return $query;
                    })->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-list-bullet')
                    ->color('black')
                    ->iconButton(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\Group::make([
                                        Components\TextEntry::make('kendaraan.nama')
                                            ->label('Kendaraan')
                                            ->formatStateUsing(function ($state, $record) {
                                                $merk = $record->kendaraan?->merk?->nama ?? '-';
                                                $nama = $record->kendaraan?->nama ?? '-';
                                                $plat = $record->kendaraan?->nomor_plat ?? '-';
                                                $roda = $record->kendaraan?->jumlah_roda ?? '-';

                                                return "{$merk} {$nama} ({$plat}) (Roda {$roda})";
                                            }),
                                        Components\TextEntry::make('tahun'),
                                        Components\TextEntry::make('nomor_nota')
                                            ->label('Nomor Nota'),
                                        Components\TextEntry::make('tanggal_nota')
                                            ->label('Tanggal Nota')
                                            ->formatStateUsing(function (Model $record) {
                                                $tanggal = $record->tanggal_nota?->locale('id')->translatedFormat('l, d F Y');

                                                return new HtmlString(<<<HTML
                                                    <div class="text-center">
                                                        <div>$tanggal</div>
                                                    </div>
                                                HTML);
                                            })
                                            ->badge()
                                            ->color('success'),
                                        Components\TextEntry::make('keterangan'),
                                    ]),
                                ])->inlineLabel(),
                        ])->from('lg'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailPerawatanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerawatan::route('/'),
            'create' => Pages\CreatePerawatan::route('/create'),
            'edit' => Pages\EditPerawatan::route('/{record}/edit'),
            'view' => Pages\ViewPerawatan::route('/{record}'),
        ];
    }
}
