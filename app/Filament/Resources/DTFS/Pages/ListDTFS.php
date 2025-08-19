<?php

namespace App\Filament\Resources\DTFS\Pages;

use App\Filament\Resources\DTFS\DTFResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDTFS extends ListRecords
{
    protected static string $resource = DTFResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
