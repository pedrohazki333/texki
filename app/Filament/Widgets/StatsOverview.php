<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    // defina se quiser auto-refresh, ex.: '60s'
    //protected static ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();

        // Ajuste os status que contam como venda realizada
        $paidStatuses = ['pago', 'entregue'];

        $ordersThisMonth = Order::whereBetween('created_at', [$start, $end])->count();

        $salesThisMonth = Order::whereBetween('created_at', [$start, $end])
            ->whereIn('status', $paidStatuses)
            ->sum('total');

        return [
            Stat::make('Orders this month', number_format($ordersThisMonth, 0, ',', '.'))
                ->description("from {$start->format('m/d')} to {$end->format('m/d')}")
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Sales this month', 'R$ ' . number_format($salesThisMonth, 2, ',', '.'))
                ->description("from {$start->format('m/d')} to " . now()->format('m/d'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }

    /** Quantos cards por linha (2 = lado a lado) */
    protected function getColumns(): int|array
    {
        return 2;
    }
}
