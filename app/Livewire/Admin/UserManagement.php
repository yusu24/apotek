<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WithPagination;
    
    public $search = '';

    protected $queryString = [
        'page' => ['except' => 1],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        // Check permission
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Unauthorized');
        }
    }

    public function toggleUserStatus($id)
    {
        if (!auth()->user()->can('manage users')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengelola status user.');
            return;
        }

        $user = User::find($id);
        
        if (!$user) {
            session()->flash('error', 'User tidak ditemukan.');
            return;
        }

        if ($user->id === auth()->id()) {
            session()->flash('error', 'Tidak dapat menonaktifkan diri sendiri.');
            return;
        }

        try {
            $user->is_active = !$user->is_active;
            $user->save();
            
            $statusText = $user->is_active ? 'mengaktifkan' : 'menonaktifkan';
            
            \App\Models\ActivityLog::log([
                'action' => 'updated',
                'module' => 'users',
                'description' => "Berhasil {$statusText} user: {$user->name}",
                'old_values' => ['is_active' => !$user->is_active],
                'new_values' => ['is_active' => $user->is_active]
            ]);

            session()->flash('message', "User {$user->name} berhasil " . ($user->is_active ? 'diaktifkan.' : 'dinonaktifkan.'));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status user.');
        }
    }

    public function impersonate($id)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            session()->flash('error', 'Hanya Super Admin yang dapat melakukan impersonate.');
            return;
        }

        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak bisa impersonate diri sendiri.');
            return;
        }

        // Store original user ID in session
        session(['impersonator_id' => auth()->id()]);
        
        // Log in as the user
        auth()->login($user);
        
        session()->flash('message', "Sesi dialihkan ke: {$user->name}");
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        $users = User::with('roles')
            ->where('is_developer', false)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10)
            ->onEachSide(2);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }
}
