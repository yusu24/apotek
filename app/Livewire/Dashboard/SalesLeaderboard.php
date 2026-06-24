<?php

namespace App\Livewire\Dashboard;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SalesLeaderboard extends Component
{
    public string $chartPeriod = 'current_month';

    public function render()
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Omset Bulan Ini
        $monthlyTurnover = Sale::where('status', 'completed')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('grand_total');

        // Total Transaksi Bulan Ini
        $monthlyTransactions = Sale::where('status', 'completed')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        // Papan Peringkat Penjualan Kasir
        $leaderboard = Sale::where('status', 'completed')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->select('user_id', DB::raw('COUNT(id) as total_transactions'), DB::raw('SUM(grand_total) as total_sales'))
            ->groupBy('user_id')
            ->orderByDesc('total_sales')
            ->with('user')
            ->get();

        // Tren Omset untuk Grafik (berdasarkan filter)
        $chartResult = $this->buildChartData($now);

        $this->dispatch('chart-updated', labels: $chartResult['labels'], data: $chartResult['data']);

        return view('livewire.dashboard.sales-leaderboard', [
            'monthlyTurnover' => $monthlyTurnover,
            'monthlyTransactions' => $monthlyTransactions,
            'leaderboard' => $leaderboard,
            'chartLabels' => json_encode($chartResult['labels']),
            'chartData' => json_encode($chartResult['data']),
            'chartTitle' => $chartResult['title'],
        ]);
    }

    private function buildChartData(Carbon $now): array
    {
        switch ($this->chartPeriod) {
            case '7_days':
                return $this->buildDailyRange($now->copy()->subDays(6), $now, '7 Hari Terakhir');
            case '30_days':
                return $this->buildDailyRange($now->copy()->subDays(29), $now, '30 Hari Terakhir');
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                return $this->buildDailyRange($start, $end, 'Bulan Lalu (' . $start->translatedFormat('F Y') . ')');
            case 'current_month':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                return $this->buildDailyRange($start, $end, 'Bulan Ini (' . $now->translatedFormat('F Y') . ')');
        }
    }

    private function buildDailyRange(Carbon $start, Carbon $end, string $title): array
    {
        $salesTrend = Sale::where('status', 'completed')
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->select(DB::raw('DATE(date) as sale_date'), DB::raw('SUM(grand_total) as daily_total'))
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        $dailyTotals = $salesTrend->pluck('daily_total', 'sale_date')->toArray();
        $labels = [];
        $data = [];

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dateString = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('d');
            $data[] = (float)($dailyTotals[$dateString] ?? 0);
            $cursor->addDay();
        }

        return compact('labels', 'data', 'title');
    }
}
