<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

use App\Traits\HasPermissionStructure;

#[Layout('layouts.app')]
class UserForm extends Component
{
    use HasPermissionStructure;

    public $user_id;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role_name;
    public $is_active = true;
    public $menu_permissions = [];

    public function toggleGroup($group)
    {
        $structure = $this->permissionStructure;
        if (!isset($structure[$group])) return;

        // Check if all representive items are currently checked
        $items = array_keys($structure[$group]['items']);
        $allChecked = true;
        foreach ($items as $perm) {
            if (empty($this->menu_permissions[$perm])) {
                $allChecked = false;
                break;
            }
        }

        // Toggle
        $newValue = !$allChecked;
        foreach ($items as $perm) {
            $this->menu_permissions[$perm] = $newValue;
        }
    }

    public function mount($id = null)
    {
        // Check permission
        if (!auth()->user()->can('manage users')) {
            abort(403, 'Unauthorized');
        }

        // Initialize all permissions to false
        foreach ($this->permissionStructure as $group) {
            foreach ($group['items'] as $perm => $data) {
                $this->menu_permissions[$perm] = false;
            }
        }

        if ($id) {
            $user = User::with('roles')->findOrFail($id);
            $this->user_id = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role_name = $user->roles->first()?->name ?? '';
            $this->is_active = $user->is_active;
            
            // Check direct permissions
            foreach (array_keys($this->menu_permissions) as $perm) {
                // Ensure permission exists in DB to avoid errors
                try {
                    $this->menu_permissions[$perm] = $user->hasDirectPermission($perm);
                } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                    // Permission doesn't exist in DB yet, default to false
                    $this->menu_permissions[$perm] = false;
                    \Log::warning("Permission '{$perm}' not found in database for guard 'web'");
                }
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
                'is_active' => $this->is_active,
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
                'is_active' => $this->is_active,
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
            // Get only enabled permissions
            $enabledPermissions = array_keys(array_filter($this->menu_permissions, fn($enabled) => $enabled === true));
            
            // Filter out permissions that don't exist in database
            $validPermissions = [];
            foreach ($enabledPermissions as $perm) {
                try {
                    // Check if permission exists
                    \Spatie\Permission\Models\Permission::findByName($perm, 'web');
                    $validPermissions[] = $perm;
                } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                    \Log::warning("Cannot assign permission '{$perm}' - not found in database");
                }
            }
            
            // Sync permissions (this will add new ones and remove unchecked ones)
            $user->syncPermissions($validPermissions);
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
