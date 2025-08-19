<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class PendingOrders extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;
    /** Status considerados “pendentes” */
    private array $pending = [
        'pago',
        'recebido',
        'arte pronta',
        'impressão pronta',
        'estampado',
        // remova/adicione conforme seu fluxo
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Order::query()->with(['arts', 'customer', 'employee'])
                ->whereIn('status', $this->pending))
            ->columns([
                ImageColumn::make('first_art_url')
                    ->label('Art')
                    ->getStateUsing(fn(?Order $record) => $record?->first_art_url)
                    ->square(),

                TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('total')
                    ->label('Value')
                    ->money('BRL', true)
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'recebido'                             => 'warning',
                        'arte pronta', 'impressão pronta'      => 'info',
                        'estampado'                            => 'primary',
                        'entregue'                             => 'success',
                        'pago'                                 => 'danger',
                        default                                => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
