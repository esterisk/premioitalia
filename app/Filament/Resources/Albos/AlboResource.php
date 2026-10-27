<?php

namespace App\Filament\Resources\Albos;

use App\Filament\Resources\Albos\Pages\CreateAlbo;
use App\Filament\Resources\Albos\Pages\EditAlbo;
use App\Filament\Resources\Albos\Pages\ListAlbos;
use App\Filament\Resources\Albos\Schemas\AlboForm;
use App\Filament\Resources\Albos\Tables\AlbosTable;
use App\Models\Albo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AlboResource extends Resource
{
    protected static ?string $model = Albo::class;

    protected static ?string $modelLabel = 'voce dell’albo';

    protected static ?string $pluralModelLabel = 'Albo d’oro';

    protected static ?string $navigationLabel = 'Albo d’oro';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $recordTitleAttribute = 'descrizione';

    public static function form(Schema $schema): Schema
    {
        return AlboForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlbosTable::configure($table);
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
            'index' => ListAlbos::route('/'),
            'create' => CreateAlbo::route('/create'),
            'edit' => EditAlbo::route('/{record}/edit'),
        ];
    }
}
