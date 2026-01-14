<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

#[Layout('layouts.app')]
class UserForm extends Component
{
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role_name;
    // Granular Menu Access
    public $menu_permissions = [
        'view dashboard' => false,
        'view products' => false,
        'manage categories' => false,
        'manage product units' => false,
        'view stock' => false,
        'adjust stock' => false,
        'view purchase orders' => false,
        'view goods receipts' => false,
        'access pos' => false,
        'view sales reports' => false,
        'view profit loss' => false,
        'view balance sheet' => false,
        'view income statement' => false,
        'view ppn report' => false,
        'view ap aging report' => false,
        'view general ledger' => false,
        'view journals' => false,
        'create journal' => false,
        'view accounts' => false,
        'manage accounts' => false,
        'view expenses' => false,
        'manage expense categories' => false,
        'manage settings' => false,
        'manage users' => false,
        'view activity logs' => false,
        'manage suppliers' => false,
        'manage pos settings' => false,
        'manage opening balances' => false,
        'manage sales returns' => false,
        'manage purchase returns' => false,
        'view trial balance' => false,
        'view product margin report' => false,
        'view stock movements' => false,
        'import_master_data' => false,
    ];

    public function mount($id = null)
    {
        // Check permission
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Unauthorized');
        }

        if ($id) {
            $user = User::with('roles')->findOrFail($id);
            $this->user_id = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role_name = $user->roles->first()?->name ?? '';
            
            // Check direct permissions
            foreach ($this->menu_permissions as $perm => $val) {
                $this->menu_permissions[$perm] = $user->hasDirectPermission($perm);
            }
        }
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->user_id ?? 'NULL'),
            'role_name' => 'required|exists:roles,name',
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|min:8|confirmed';
        } elseif ($this->password) {
            $rules['password'] = 'min:8|confirmed';
        }

        $this->validate($rules);

        if ($this->user_id) {
            $user = User::findOrFail($this->user_id);
            $oldData = $user->toArray();
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            ActivityLog::log([
                'action' => 'updated',
                'module' => 'users',
                'description' => "Memperbarui user: {$this->name}",
                'old_values' => $oldData,
                'new_values' => $user->fresh()->toArray()
            ]);
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            ActivityLog::log([
                'action' => 'created',
                'module' => 'users',
                'description' => "Menambah user baru: {$this->name}",
                'new_values' => $user->toArray()
            ]);
        }

        // Sync role
        $user->syncRoles([$this->role_name]);

        // Manage Direct Permissions
        if ($this->role_name !== 'super-admin') {
            foreach ($this->menu_permissions as $perm => $enabled) {
                if ($enabled) {
                    $user->givePermissionTo($perm);
                } else {
                    $user->revokePermissionTo($perm);
                }
            }
        } else {
            // Clear direct permissions for super-admin to keep it clean
            $user->syncPermissions([]);
        }

        session()->flash('message', 'User berhasil disimpan.');
        return $this->redirect(route('admin.users.index'), navigate: true);
    }

    public function render()
    {
        $roles = Role::all();
        
        return view('livewire.admin.user-form', [
            'roles' => $roles,
        ]);
    }
}
