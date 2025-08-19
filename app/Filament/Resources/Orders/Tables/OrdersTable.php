<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Exports\OrderExporter;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('first_art_url')
                    ->label('Art')
                    // devolve a URL (ou null) de forma null-safe
                    ->getStateUsing(fn(?Order $record) => $record?->first_art_url)
                    ->square()
                // opcional: um placeholder quando não houver imagem
                // ->defaultImageUrl(asset('images/no-art.png'))
                ,
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('customer.first_name')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'recebido' => 'gray',
                        'pago' => 'danger',
                        'arte pronta' => 'warning',
                        'impressão pronta' => 'warning',
                        'estampado' => 'warning',
                        'entregue' => 'success',
                    }),
                TextColumn::make('total')->money('BRL', true)->sortable(),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(OrderExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
