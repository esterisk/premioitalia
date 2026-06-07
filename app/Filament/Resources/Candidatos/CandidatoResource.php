<?php

namespace App\Filament\Resources\Candidatos;

use App\Filament\Resources\Candidatos\Pages\CreateCandidato;
use App\Filament\Resources\Candidatos\Pages\EditCandidato;
use App\Filament\Resources\Candidatos\Pages\ListCandidatos;
use App\Filament\Resources\Candidatos\Schemas\CandidatoForm;
use App\Filament\Resources\Candidatos\Tables\CandidatosTable;
use App\Models\Candidato;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CandidatoResource extends Resource
{
    protected static ?string $model = Candidato::class;

    protected static ?string $pluralModelLabel = 'Candidati';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBoxArrowDown;

    protected static ?string $recordTitleAttribute = 'descrizione';

    protected static string | UnitEnum | null $navigationGroup = 'FASE UNO';

    public static function form(Schema $schema): Schema
    {
        return CandidatoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CandidatosTable::configure($table);
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
            'index' => ListCandidatos::route('/'),
            'create' => CreateCandidato::route('/create'),
            'edit' => EditCandidato::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::nuovo()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Nuovi candidati da esaminare';
    }

}
