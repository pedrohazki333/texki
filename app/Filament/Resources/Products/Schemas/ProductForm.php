<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_name')->required(),
                TextInput::make('description')->required(),
                TextInput::make('price')
                    ->numeric()
                    ->inputMode('decimal')
                    ->required(),
                TextInput::make('quantity')
                    ->numeric()
                    ->inputMode('decimal')
                    ->required(),
            ]);
    }
}
