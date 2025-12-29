<?php

namespace App\Livewire\ActivityLog;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterUser = '';

    public function mount()
    {
        if (!auth()->user()->can('view activity logs')) {
            abort(403, 'Unauthorized action.');
        }
    }
    public $filterModule = '';
    public $filterAction = '';
    public $lastUpdated = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    
    public $selectedLog = null;
    public $showDetailModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterUser' => ['except' => ''],
        'filterModule' => ['except' => ''],
        'filterAction' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterUser()
    {
        $this->resetPage();
    }

    public function updatingFilterModule()
    {
        $this->resetPage();
    }

    public function updatingFilterAction()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterUser', 'filterModule', 'filterAction', 'filterDateFrom', 'filterDateTo']);
        $this->resetPage();
    }

    public function viewDetails($logId)
    {
        $this->selectedLog = ActivityLog::with('user')->find($logId);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedLog = null;
    }

    public function render()
    {
        $query = ActivityLog::with('user')
            ->when($this->search, function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('module', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterUser, function ($q) {
                $q->where('user_id', $this->filterUser);
            })
            ->when($this->filterModule, function ($q) {
                $q->where('module', $this->filterModule);
            })
            ->when($this->filterAction, function ($q) {
                $q->where('action', $this->filterAction);
            })
            ->when($this->filterDateFrom, function ($q) {
                $q->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($q) {
                $q->whereDate('created_at', '<=', $this->filterDateTo);
            })
            ->latest()
            ->paginate(20);

        $users = User::orderBy('name')->get();
        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        // Statistics
        $stats = [
            'total_today' => ActivityLog::whereDate('created_at', today())->count(),
            'total_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_month' => ActivityLog::whereMonth('created_at', now()->month)->count(),
            'unique_users_today' => ActivityLog::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
        ];

        $this->lastUpdated = now()->format('H:i:s');

        return view('livewire.activity-log.activity-log-index', [
            'logs' => $query,
            'users' => $users,
            'modules' => $modules,
            'actions' => $actions,
            'stats' => $stats,
        ]);
    }
}
