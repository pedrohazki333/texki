<?php

namespace App\Filament\Resources\DTFS;

use App\Filament\Resources\DTFS\Pages\CreateDTF;
use App\Filament\Resources\DTFS\Pages\EditDTF;
use App\Filament\Resources\DTFS\Pages\ListDTFS;
use App\Filament\Resources\DTFS\Schemas\DTFForm;
use App\Filament\Resources\DTFS\Tables\DTFSTable;
use App\Models\DTF;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DTFResource extends Resource
{
    protected static ?string $model = DTF::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-printer';
    protected static string | UnitEnum | null $navigationGroup = 'Sales';

    protected static ?string $navigationLabel = 'DTF';
    protected static ?string $modelLabel = 'DTF Print';

    public static function form(Schema $schema): Schema
    {
        return DTFForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DTFSTable::configure($table);
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
            'index' => ListDTFS::route('/'),
            'create' => CreateDTF::route('/create'),
            'edit' => EditDTF::route('/{record}/edit'),
        ];
    }
}
