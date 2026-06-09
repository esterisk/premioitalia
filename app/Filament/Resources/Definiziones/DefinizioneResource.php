<?php

namespace App\Filament\Resources\Definiziones;

use App\Filament\Resources\Definiziones\Pages\CreateDefinizione;
use App\Filament\Resources\Definiziones\Pages\EditDefinizione;
use App\Filament\Resources\Definiziones\Pages\ListDefiniziones;
use App\Filament\Resources\Definiziones\Schemas\DefinizioneForm;
use App\Filament\Resources\Definiziones\Tables\DefinizionesTable;
use App\Models\Definizione;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DefinizioneResource extends Resource
{
    protected static ?string $model = Definizione::class;
    protected static ?string $pluralModelLabel = 'Definizioni';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $recordTitleAttribute = 'titolo';

    public static function form(Schema $schema): Schema
    {
        return DefinizioneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DefinizionesTable::configure($table);
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
            'index' => ListDefiniziones::route('/'),
            'create' => CreateDefinizione::route('/create'),
            'edit' => EditDefinizione::route('/{record}/edit'),
        ];
    }
}
