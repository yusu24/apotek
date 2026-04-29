<?php

namespace App\Livewire\ActivityLog;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class ActivityLogIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterUser = '';
    public $perPage = 10;

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
        'page' => ['except' => 1],
        'search' => ['except' => ''],
        'filterUser' => ['except' => ''],
        'filterModule' => ['except' => ''],
        'filterAction' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'perPage' => ['except' => 10],
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
        /** @var \Illuminate\Pagination\LengthAwarePaginator $query */
        $query = ActivityLog::with('user')
            ->when($this->search, function ($q) {
                $q->where(function($q2) {
                    $q2->where('description', 'like', '%' . $this->search . '%')
                       ->orWhere('module', 'like', '%' . $this->search . '%');
                });
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
            ->paginate($this->perPage);
        $query->onEachSide(1);

        Log::info("ActivityLogIndex Rendering", [
            'page' => $this->getPage(),
            'search' => $this->search,
            'user' => $this->filterUser,
            'total' => $query->total(),
            'count' => $query->count()
        ]);

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
