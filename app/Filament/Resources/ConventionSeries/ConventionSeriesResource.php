<?php

namespace App\Filament\Resources\ConventionSeries;

use App\Filament\Resources\ConventionSeries\Pages\CreateConventionSeries;
use App\Filament\Resources\ConventionSeries\Pages\EditConventionSeries;
use App\Filament\Resources\ConventionSeries\Pages\ListConventionSeries;
use App\Filament\Resources\ConventionSeries\Schemas\ConventionSeriesForm;
use App\Filament\Resources\ConventionSeries\Tables\ConventionSeriesTable;
use App\Models\ConventionSeries;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConventionSeriesResource extends Resource
{
    protected static ?string $model = ConventionSeries::class;
    protected static ?string $pluralModelLabel = 'Serie Convention';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'nome';

    public static function form(Schema $schema): Schema
    {
        return ConventionSeriesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConventionSeriesTable::configure($table);
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
            'index' => ListConventionSeries::route('/'),
            'create' => CreateConventionSeries::route('/create'),
            'edit' => EditConventionSeries::route('/{record}/edit'),
        ];
    }
}
