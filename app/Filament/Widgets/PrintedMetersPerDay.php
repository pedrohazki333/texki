<?php

namespace App\Filament\Widgets;

use App\Models\DTF;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class PrintedMetersPerDay extends ChartWidget
{
    protected ?string $heading = 'Printed Meters Per Day';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        [$labels, $values] = $this->buildSeries();

        // Dataset nunca vazio
        if (empty($labels)) {
            $labels = ['—'];
            $values = [0];
        }

        // paleta (repete se tiver mais dias)
        $palette = [
            'rgba(16,185,129,0.8)',  // emerald
            'rgba(59,130,246,0.8)',  // blue
            'rgba(245,158,11,0.8)',  // amber
            'rgba(239,68,68,0.8)',   // red
            'rgba(139,92,246,0.8)',  // violet
            'rgba(236,72,153,0.8)',  // pink
        ];
        $bg = [];
        foreach ($values as $i => $_) $bg[] = $palette[$i % count($palette)];

        return [
            'labels' => $labels, // ex.: ['01/08', '02/08', ...]
            'datasets' => [[
                'label'           => 'Metros por dia (m)',
                'data'            => $values,
                'backgroundColor' => $bg,
                'borderColor'     => 'rgba(255,255,255,0.12)',
                'borderWidth'     => 1,
            ]],
        ];
    }

    private function buildSeries(): array
    {
        $start = now()->startOfMonth()->toDateString(); // 'Y-m-d'
        $end   = now()->endOfMonth()->toDateString();

        // soma por dia (portável: DATE(print_date))
        $rows = DTF::query()
            ->whereBetween('print_date', [$start, $end])
            ->selectRaw('DATE(print_date) as day, SUM(meters) as meters_sum')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $byDay = $rows->pluck('meters_sum', 'day'); // ['2025-08-01' => 12.5, ...]

        $period = CarbonPeriod::create(Carbon::parse($start), Carbon::parse($end));

        $labels = [];
        $values = [];
        $total  = 0.0;

        foreach ($period as $date) {
            $key = $date->toDateString();
            $val = (float) ($byDay[$key] ?? 0);
            $labels[] = $date->format('d/m');
            $values[] = $val;
            $total   += $val;
        }

        return [$labels, $values, $total];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
