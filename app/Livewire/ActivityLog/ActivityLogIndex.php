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
        $isDeveloper = (bool) (auth()->user()?->is_developer ?? false);
        $this->selectedLog = ActivityLog::with('user')
            ->when(!$isDeveloper, function ($q) {
                $q->whereDoesntHave('user', fn($u) => $u->where('is_developer', true));
            })
            ->find($logId);
        $this->showDetailModal = (bool) $this->selectedLog;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedLog = null;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    
    public function updatingPerPage()
    {
        $this->resetPage();
    }
public function render()
    {
        $isDeveloper = (bool) (auth()->user()?->is_developer ?? false);

        $baseQuery = ActivityLog::query()
            ->when(!$isDeveloper, function ($q) {
                $q->whereDoesntHave('user', fn($u) => $u->where('is_developer', true));
            });

        /** @var \Illuminate\Pagination\LengthAwarePaginator $query */
        $query = (clone $baseQuery)->with('user')
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

        $users = User::when(!$isDeveloper, fn($q) => $q->where('is_developer', false))
            ->orderBy('name')
            ->get();
        $modules = (clone $baseQuery)->select('module')->distinct()->orderBy('module')->pluck('module');
        $actions = (clone $baseQuery)->select('action')->distinct()->orderBy('action')->pluck('action');

        // Statistics
        $stats = [
            'total_today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'total_week' => (clone $baseQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_month' => (clone $baseQuery)->whereMonth('created_at', now()->month)->count(),
            'unique_users_today' => (clone $baseQuery)->whereDate('created_at', today())->distinct('user_id')->count('user_id'),
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
