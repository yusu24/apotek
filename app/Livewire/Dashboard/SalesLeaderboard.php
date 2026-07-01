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
        // 12 minggu terakhir, label = "Mg DD/MM"
        $weeks  = 12;
        $labels = [];
        $data   = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start = $now->copy()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
            $end   = $now->copy()->subWeeks($i)->endOfWeek(Carbon::SUNDAY);

            // Jangan melebihi hari ini
            if ($end->gt($now)) $end = $now->copy();

            $total = Sale::where('status', 'completed')
                ->whereBetween('date', [$start->copy()->startOfDay()->toDateTimeString(), $end->copy()->endOfDay()->toDateTimeString()])
                ->sum('grand_total');

            $labels[] = $start->format('d/m');
            $data[]   = (float)$total;
        }

        return ['labels' => $labels, 'data' => $data, 'title' => 'Mingguan — 12 Minggu Terakhir'];
    }

    private function buildMonthlyRange(Carbon $now): array
    {
        // 12 bulan terakhir, label = "Jan", "Feb", dst
        $months = 12;
        $labels = [];
        $data   = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);

            $total = Sale::where('status', 'completed')
                ->whereYear('date',  $month->year)
                ->whereMonth('date', $month->month)
                ->sum('grand_total');

            $labels[] = $month->translatedFormat('M');
            $data[]   = (float)$total;
        }

        return ['labels' => $labels, 'data' => $data, 'title' => 'Bulanan — 12 Bulan Terakhir'];
    }
}
