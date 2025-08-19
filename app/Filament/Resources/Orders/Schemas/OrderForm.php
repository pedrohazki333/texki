<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Customer')
                    ->options(
                        Customer::all()->mapWithKeys(function ($customer) {
                            return [$customer->id => "{$customer->first_name} {$customer->last_name}"];
                        })
                    )
                    ->searchable()
                    ->required(),
                Select::make('employee_id')
                    ->label('Employee')
                    ->options(
                        Employee::all()->mapWithKeys(function ($employee) {
                            return [$employee->id => "{$employee->first_name} {$employee->last_name}"];
                        })
                    )
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('Employee')
                    ->options([
                        'recebido' => 'Recebido',
                        'pago' => 'Pago',
                        'arte pronta' => 'Arte pronta',
                        'impressão pronta' => 'Impressão pronta',
                        'estampado' => 'Estampado',
                        'entregue' => 'Entregue',
                    ])
                    ->default('recebido')
                    ->searchable()
                    ->required(),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->dehydrated()
                    ->default(0),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Repeater::make('items')
                    ->schema([
                        // Code here for OrderItems repeater
                        Select::make('product_id')
                            ->label('Product')
                            ->options(
                                Product::all()->mapWithKeys(function ($product) {
                                    return [$product->id => "{$product->product_name} "];
                                })
                            )
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state && ($p = Product::find($state))) {
                                    $set('unit_price', (string) $p->price);
                                }
                            })
                            ->searchable(),
                        TextInput::make('quantity')
                            ->numeric()
                            ->default(0)
                            ->live(debounce: 300)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $set('line_total', (float) $get('quantity') * (float) $get('unit_price'));
                            }),
                        TextInput::make('unit_price')
                            ->numeric()
                            ->prefix('R$')
                            ->step('0.01')
                            ->default(0)
                            ->live(debounce: 300)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $set('line_total', (float) $get('quantity') * (float) $get('unit_price'));
                            }),
                        TextInput::make('line_total')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('R$'),
                    ])
                    ->live(debounce: 300)
                    ->columns(4)
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->afterStateUpdated(function (array $state, Set $set) {
                        $sum = collect($state)->sum(function ($row) {
                            $q = (float) ($row['quantity'] ?? 0);
                            $u = (float) ($row['unit_price'] ?? 0);
                            return $q * $u;
                        });
                        $set('total', number_format($sum, 2, '.', ''));
                    }),
                Repeater::make('arts')
                    ->label('Arts')
                    ->relationship()          // usa Order::arts()
                    ->columns(12)
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->live(debounce: 300)
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Artwork')
                            ->image()
                            ->imageEditor()
                            ->directory('orders/arts')
                            ->visibility('public')
                            ->preserveFilenames(false)
                            // nome único para evitar colisão
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string =>
                                (string) Str::uuid() . '.' . $file->getClientOriginalExtension()
                            )
                            ->openable()
                            ->downloadable()
                            ->previewable(true)
                            ->imageResizeMode('cover')
                            ->required()
                            ->columnSpan(6),

                        TextInput::make('quantity')
                            ->label('Qty')
                            ->numeric()->minValue(1)->default(1)
                            ->live(debounce: 300)
                            ->columnSpan(2),

                        TextInput::make('width')
                            ->label('Width')
                            ->numeric()->step('0.01')
                            ->suffix('cm')      // ou mm/pol
                            ->live(debounce: 300)
                            ->columnSpan(2),

                        TextInput::make('height')
                            ->label('Height')
                            ->numeric()->step('0.01')
                            ->suffix('cm')
                            ->live(debounce: 300)
                            ->columnSpan(2),

                        Textarea::make('notes')
                            ->label('Obs')
                            ->rows(2)
                            ->columnSpan(12)
                            ->nullable(),
                    ])
                    // se quiser atualizar total por área, você pode somar aqui
                    ->afterStateUpdated(function (array $state, Set $set, Get $get) {
                        // Exemplo: se quiser compor o total do pedido com base nas artes também
                        // some somente se fizer sentido no seu negócio
                        $itemsSum = collect($get('items') ?? [])->sum(
                            fn($r) => (float) ($r['quantity'] ?? 0) * (float) ($r['unit_price'] ?? 0)
                        );
                        $artsSum = 0; // calcule se tiver preço por área; senão, deixe 0
                        $set('total', number_format($itemsSum + $artsSum, 2, '.', ''));
                    }),
            ]);
    }
}
