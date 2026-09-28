<?php

namespace App\Livewire\Dashboard;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SalesLeaderboard extends Component
{
    public string $chartPeriod = 'daily';
    public bool $showFullLeaderboard = false;
    public array $chartLabels = [];
    public array $chartData = [];

    public function updatedChartPeriod(): void
    {
        if (!auth()->user()->can('view dashboard sales trend')) {
            return;
        }

        $now = Carbon::now();
        $chartResult = $this->buildChartData($now);
        $this->chartLabels = $chartResult['labels'];
        $this->chartData   = $chartResult['data'];

        // Kirim data baru langsung sebagai payload event
        // Alpine akan terima via $event.detail.labels & $event.detail.data
        $this->dispatch('chart-data-updated',
            labels: $chartResult['labels'],
            data:   $chartResult['data'],
        );
    }

    public function render()
    {
        $now          = Carbon::now();
        $today        = $now->toDateString();
        $currentMonth = $now->month;
        $currentYear  = $now->year;

        $user = auth()->user();

        // Omset & Transaksi Hari Ini (hanya jika punya izin)
        $dailyTurnover      = 0;
        $dailyTransactions  = 0;
        if ($user->can('view dashboard today sales')) {
            $dailyTurnover = Sale::where('status', 'completed')
                ->whereDate('date', $today)
                ->sum('grand_total');

            $dailyTransactions = Sale::where('status', 'completed')
                ->whereDate('date', $today)
                ->count();
        }

        // Papan Peringkat Kasir (hanya jika punya izin)
        $leaderboard = collect();
        if ($user->can('view dashboard staff leaderboard')) {
            $leaderboard = Sale::where('status', 'completed')
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->select('user_id', DB::raw('COUNT(id) as total_transactions'), DB::raw('SUM(grand_total) as total_sales'))
                ->groupBy('user_id')
                ->orderByDesc('total_sales')
                ->with('user')
                ->get();
        }

        // Tren Omset untuk Grafik (hanya jika punya izin)
        $chartResult = ['labels' => [], 'data' => [], 'title' => ''];
        if ($user->can('view dashboard sales trend')) {
            $chartResult = $this->buildChartData($now);
        }
        $this->chartLabels = $chartResult['labels'];
        $this->chartData   = $chartResult['data'];

        return view('livewire.dashboard.sales-leaderboard', [
            'dailyTurnover'       => $dailyTurnover,
            'dailyTransactions'   => $dailyTransactions,
            'leaderboard'         => $leaderboard,
            'chartTitle'          => $chartResult['title'],
            'chartLabels'         => $chartResult['labels'],
            'chartData'           => $chartResult['data'],
        ]);
    }

    private function buildChartData(Carbon $now): array
    {
        switch ($this->chartPeriod) {
            case 'weekly':
                return $this->buildWeeklyRange($now);
            case 'monthly':
                return $this->buildMonthlyRange($now);
            case 'daily':
            default:
                // Harian: tampilkan hari-hari dalam bulan berjalan s/d hari ini
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy(); // sampai hari ini saja
                return $this->buildDailyRange($start, $end, 'Harian — ' . $now->translatedFormat('F Y'));
        }
    }

    private function buildDailyRange(Carbon $start, Carbon $end, string $title): array
    {
        $salesTrend = Sale::where('status', 'completed')
            ->whereBetween('date', [$start->copy()->startOfDay()->toDateTimeString(), $end->copy()->endOfDay()->toDateTimeString()])
            ->select(DB::raw('DATE(date) as sale_date'), DB::raw('SUM(grand_total) as daily_total'))
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        $dailyTotals = $salesTrend->pluck('daily_total', 'sale_date')->toArray();
        $labels = [];
        $data   = [];

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dateString = $cursor->format('Y-m-d');
            $labels[]   = $cursor->format('d');
            $data[]     = (float)($dailyTotals[$dateString] ?? 0);
            $cursor->addDay();
        }

        return compact('labels', 'data', 'title');
    }

    private function buildWeeklyRange(Carbon $now): array
    {
        $weeks  = 12;
        $earliestMonday = $now->copy()->subWeeks($weeks - 1)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $endRange = $now->copy()->endOfDay();

        $dailySales = Sale::where('status', 'completed')
            ->whereBetween('date', [$earliestMonday->toDateTimeString(), $endRange->toDateTimeString()])
            ->select(DB::raw('DATE(date) as sale_date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('sale_date')
            ->pluck('total', 'sale_date')
            ->toArray();

        $labels = [];
        $data   = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start = $now->copy()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
            $end   = $now->copy()->subWeeks($i)->endOfWeek(Carbon::SUNDAY);
            if ($end->gt($now)) $end = $now->copy();

            $weekSum = 0;
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $weekSum += (float)($dailySales[$cursor->format('Y-m-d')] ?? 0);
                $cursor->addDay();
            }

            $labels[] = $start->format('d/m');
            $data[]   = $weekSum;
        }

        return ['labels' => $labels, 'data' => $data, 'title' => 'Mingguan — 12 Minggu Terakhir'];
    }

    private function buildMonthlyRange(Carbon $now): array
    {
        $months = 12;
        $startRange = $now->copy()->subMonths($months - 1)->startOfMonth()->startOfDay();
        $endRange   = $now->copy()->endOfDay();

        $monthlyTrend = Sale::where('status', 'completed')
            ->whereBetween('date', [$startRange->toDateTimeString(), $endRange->toDateTimeString()])
            ->select(DB::raw('DATE_FORMAT(date, "%Y-%m") as month_key'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('month_key')
            ->pluck('total', 'month_key')
            ->toArray();

        $labels = [];
        $data   = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $key = $month->format('Y-m');

            $labels[] = $month->translatedFormat('M');
            $data[]   = (float)($monthlyTrend[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data, 'title' => 'Bulanan — 12 Bulan Terakhir'];
    }
}
