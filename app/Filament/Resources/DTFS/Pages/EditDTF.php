<?php

namespace App\Filament\Resources\DTFS\Pages;

use App\Filament\Resources\DTFS\DTFResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDTF extends EditRecord
{
    protected static string $resource = DTFResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
