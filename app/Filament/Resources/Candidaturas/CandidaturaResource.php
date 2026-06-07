<?php

namespace App\Filament\Resources\Candidaturas;

use App\Filament\Resources\Candidaturas\Pages\CreateCandidatura;
use App\Filament\Resources\Candidaturas\Pages\EditCandidatura;
use App\Filament\Resources\Candidaturas\Pages\ListCandidaturas;
use App\Filament\Resources\Candidaturas\Schemas\CandidaturaForm;
use App\Filament\Resources\Candidaturas\Tables\CandidaturasTable;
use App\Models\Candidatura;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CandidaturaResource extends Resource
{
    protected static ?string $model = Candidatura::class;

    protected static ?string $pluralModelLabel = 'Candidature spontanee';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::InboxArrowDown;

    protected static ?string $recordTitleAttribute = 'descrizione';

    protected static string | UnitEnum | null $navigationGroup = 'FASE ZERO';

    public static function form(Schema $schema): Schema
    {
        return CandidaturaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CandidaturasTable::configure($table);
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
            'index' => ListCandidaturas::route('/'),
            'create' => CreateCandidatura::route('/create'),
            'edit' => EditCandidatura::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::nuove()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::nuove()->count();
        if ($count === 0) {
            return 'gray';
        }
        return $count > 10 ? 'danger' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Nuove candidature da esaminare';
    }

}
