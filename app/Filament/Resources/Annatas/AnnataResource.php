<?php

namespace App\Filament\Resources\Annatas;

use App\Filament\Resources\Annatas\Pages\CreateAnnata;
use App\Filament\Resources\Annatas\Pages\EditAnnata;
use App\Filament\Resources\Annatas\Pages\ListAnnatas;
use App\Filament\Resources\Annatas\Schemas\AnnataForm;
use App\Filament\Resources\Annatas\Tables\AnnatasTable;
use App\Models\Annata;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnnataResource extends Resource
{
    protected static ?string $model = Annata::class;
    protected static ?string $pluralModelLabel = 'Annate';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'anno';

    public static function form(Schema $schema): Schema
    {
        return AnnataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnatasTable::configure($table);
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
            'index' => ListAnnatas::route('/'),
            'create' => CreateAnnata::route('/create'),
            'edit' => EditAnnata::route('/{record}/edit'),
        ];
    }
}
