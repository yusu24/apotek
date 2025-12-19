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

    public function mount()
    {
        // Check permission
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Unauthorized');
        }
    }

    public function deleteUser($id)
    {
        if (!auth()->user()->can('manage users')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus user.');
            return;
        }

        $user = User::find($id);
        
        if ($user && $user->id !== auth()->id()) {
            $user->delete();
            session()->flash('message', 'User berhasil dihapus.');
        } else {
            session()->flash('error', 'Tidak dapat menghapus user sendiri.');
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
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10)
            ->onEachSide(2);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }
}
