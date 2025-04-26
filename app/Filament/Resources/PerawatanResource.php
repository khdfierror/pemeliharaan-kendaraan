<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerawatanResource\Pages;
use App\Filament\Resources\PerawatanResource\RelationManagers;
use App\Models\Perawatan;
use Filament\Forms;
use Filament\Forms\Form;
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
                                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->nama} ({$record->nomor_plat})")
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('tahun')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('nomor_nota')
                                ->label('Nomor Nota')
                                ->required()
                                ->numeric(),
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

                        Forms\Components\Section::make([
                            Forms\Components\Placeholder::make('created_at')
                                ->label('Dibuat pada')
                                ->content(fn(Model $record): string => $record->created_at->diffForHumans())
                                ->hiddenOn(['create']),
                            Forms\Components\Placeholder::make('updated_at')
                                ->label('Diubah pada')
                                ->content(fn(Model $record): string => $record->created_at->diffForHumans())
                                ->hiddenOn(['create']),
                        ])->columnSpan(2)
                            ->hiddenOn(['create']),
                    ])
            ]);
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun'),
                Tables\Columns\TextColumn::make('nomor_nota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_nota')
                    ->formatStateUsing(function (Model $record) {
                        $tanggal = $record->tanggal_nota?->locale('id')->translatedFormat('l, d M Y');

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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerawatan::route('/'),
            'create' => Pages\CreatePerawatan::route('/create'),
            'edit' => Pages\EditPerawatan::route('/{record}/edit'),
        ];
    }
}
