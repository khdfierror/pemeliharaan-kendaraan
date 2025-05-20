<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunResource\Pages;
use App\Filament\Resources\TahunResource\RelationManagers;
use App\Models\Tahun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TahunResource extends Resource
{
    protected static ?string $model = Tahun::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Tahun';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'pengaturan/tahun';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tahun')
                    ->label('Tahun')
                    ->required(),
                Forms\Components\Toggle::make('is_aktif')
                    ->label('Aktif')
                    ->columns(2),
                Forms\Components\Toggle::make('is_default')
                    ->label('Default'),
            ])->columns(1)->inlineLabel();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_aktif')
                    ->label('Aktif'),
                Tables\Columns\ToggleColumn::make('is_default')
                    ->label('Default')
                    ->afterStateUpdated(function ($state, $record) {
                        if ($state) {
                            $record->newQuery()
                                ->where('id', '!=', $record->id)
                                ->update(['is_default' => false]);
                        }
                    }),
            ])
            ->filters([
                //
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
            'index' => Pages\ManageTahun::route('/'),
        ];
    }
}
