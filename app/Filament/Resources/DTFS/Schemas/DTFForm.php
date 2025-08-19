<?php

namespace App\Filament\Resources\DTFS\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DTFForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('print_date')
                    ->required(),
                TextInput::make('meters')
                    ->numeric()
                    ->default(0)
                    ->prefix('M')
                    ->step('0.01')
                    ->required(),
                Select::make('status')
                    ->label('Employee')
                    ->options([
                        'pendente' => 'Pendente',
                        'em produção' => 'Em produção',
                        'impresso' => 'Impresso',
                        'finalizado' => 'Finalizado',
                    ])
                    ->default('pendente')
                    ->searchable()
                    ->required()
            ]);
    }
}
