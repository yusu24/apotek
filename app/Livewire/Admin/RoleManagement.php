<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use App\Models\ActivityLog;

#[Layout('layouts.app')]
class RoleManagement extends Component
{
    public function mount()
    {
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Unauthorized');
        }
    }

    public function deleteRole($id)
    {
        if (!auth()->user()->can('manage users')) {
            session()->flash('error', 'Unauthorized');
            return;
        }

        $role = Role::findOrFail($id);

        if (in_array($role->name, ['super-admin', 'admin', 'kasir', 'gudang'])) {
            session()->flash('error', 'Peran sistem tidak dapat dihapus.');
            return;
        }

        if ($role->users()->count() > 0) {
            session()->flash('error', 'Peran ini masih digunakan oleh user lain.');
            return;
        }

        $oldData = $role->toArray();
        $role->delete();

        ActivityLog::log([
            'action' => 'deleted',
            'module' => 'roles',
            'description' => "Menghapus jabatan: {$role->name}",
            'old_values' => $oldData
        ]);

        session()->flash('message', 'Peran berhasil dihapus.');
    }

    public function render()
    {
        $roles = Role::withCount('users')->get();

        return view('livewire.admin.role-management', [
            'roles' => $roles,
        ]);
    }
}
