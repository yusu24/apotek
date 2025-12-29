<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

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
        'view expenses' => false,
        'view profit loss' => false,
        'view balance sheet' => false,
        'view journals' => false,
        'manage expense categories' => false,
        'manage settings' => false,
        'manage users' => false,
        'view guide' => false,
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
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
        }

        // Sync role
        $user->syncRoles([$this->role_name]);

        // Manage Direct Permissions
        foreach ($this->menu_permissions as $perm => $enabled) {
            if ($enabled) {
                $user->givePermissionTo($perm);
            } else {
                $user->revokePermissionTo($perm);
            }
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
