<?php

namespace App\Filament\Resources\Motivaziones;

use App\Filament\Resources\Motivaziones\Pages\CreateMotivazione;
use App\Filament\Resources\Motivaziones\Pages\EditMotivazione;
use App\Filament\Resources\Motivaziones\Pages\ListMotivaziones;
use App\Filament\Resources\Motivaziones\Schemas\MotivazioneForm;
use App\Filament\Resources\Motivaziones\Tables\MotivazionesTable;
use App\Models\Motivazione;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MotivazioneResource extends Resource
{
    protected static ?string $model = Motivazione::class;
    protected static ?string $pluralModelLabel = 'Motivazioni';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationCircle;

    protected static ?string $recordTitleAttribute = 'motivazione';

    public static function form(Schema $schema): Schema
    {
        return MotivazioneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MotivazionesTable::configure($table);
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
            'index' => ListMotivaziones::route('/'),
            'create' => CreateMotivazione::route('/create'),
            'edit' => EditMotivazione::route('/{record}/edit'),
        ];
    }
}
