<?php

namespace App\Filament\Resources\DTFS\Pages;

use App\Filament\Resources\DTFS\DTFResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDTF extends CreateRecord
{
    protected static string $resource = DTFResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
