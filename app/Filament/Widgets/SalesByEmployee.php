<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class SalesByEmployee extends ChartWidget
{
    protected ?string $heading = 'Sales By Employee';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();
        $paid  = ['pago', 'entregue', 'arte pronta', 'impressão pronta', 'estampado'];

        // Query portátil (SQLite/MySQL/Postgres): nomes separados, concatena no PHP
        $rows = Order::query()
            ->join('employees', 'employees.id', '=', 'orders.employee_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereIn('orders.status', $paid)
            ->groupBy('orders.employee_id', 'employees.first_name', 'employees.last_name')
            ->selectRaw('employees.first_name, employees.last_name, SUM(orders.total) AS total_sum')
            ->orderByDesc('total_sum')
            ->get();

        $labels = $rows->map(fn($r) => trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? '')) ?: '—')->values()->all();
        $values = $rows->pluck('total_sum')->map(fn($v) => (float) $v)->values()->all();

        if (empty($labels)) {
            $labels = ['—'];
            $values = [0.0];
        }

        // Paleta de cores (quantas quiser)
        $colors = [
            'rgba(59,130,246,0.8)',   // azul
            'rgba(16,185,129,0.8)',   // verde
            'rgba(239,68,68,0.8)',    // vermelho
            'rgba(245,158,11,0.8)',   // amarelo
            'rgba(139,92,246,0.8)',   // roxo
            'rgba(236,72,153,0.8)',   // rosa
        ];

        // Se tiver mais vendedores do que cores, repete o array
        $bgColors = [];
        foreach ($values as $i => $v) {
            $bgColors[] = $colors[$i % count($colors)];
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label'           => 'Sales this month (R$)',
                'data'            => $values,
                'backgroundColor' => $bgColors,
                'borderColor'     => 'rgba(255,255,255,0.12)',
                'borderWidth'     => 1,
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
