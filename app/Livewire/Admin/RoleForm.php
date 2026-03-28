<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\ActivityLog;
use App\Traits\HasPermissionStructure;

#[Layout('layouts.app')]
class RoleForm extends Component
{
    use HasPermissionStructure;

    public $role_id;
    public $name;
    public $menu_permissions = [];

    public function toggleGroup($group)
    {
        $structure = $this->permissionStructure;
        if (!isset($structure[$group])) return;

        $items = array_keys($structure[$group]['items']);
        $allChecked = true;
        foreach ($items as $perm) {
            if (empty($this->menu_permissions[$perm])) {
                $allChecked = false;
                break;
            }
        }

        $newValue = !$allChecked;
        foreach ($items as $perm) {
            $this->menu_permissions[$perm] = $newValue;
        }
    }

    public function mount($id = null)
    {
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
            $role = Role::with('permissions')->findOrFail($id);
            $this->role_id = $role->id;
            $this->name = $role->name;
            
            foreach ($role->permissions as $permission) {
                if (isset($this->menu_permissions[$permission->name])) {
                    $this->menu_permissions[$permission->name] = true;
                }
            }
        }
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name,' . ($this->role_id ?? 'NULL'),
        ];

        $this->validate($rules);

        if ($this->role_id) {
            $role = Role::findOrFail($this->role_id);
            $oldData = $role->toArray();
            
            if (in_array($role->name, ['super-admin']) && $this->name !== 'super-admin') {
                session()->flash('error', 'Nama super-admin tidak dapat diubah.');
                return;
            }

            $role->update(['name' => $this->name]);

            ActivityLog::log([
                'action' => 'updated',
                'module' => 'roles',
                'description' => "Memperbarui jabatan: {$this->name}",
                'old_values' => $oldData,
                'new_values' => $role->fresh()->toArray()
            ]);
        } else {
            $role = Role::create(['name' => $this->name, 'guard_name' => 'web']);

            ActivityLog::log([
                'action' => 'created',
                'module' => 'roles',
                'description' => "Menambah jabatan baru: {$this->name}",
                'new_values' => $role->toArray()
            ]);
        }

        // Sync Permissions
        $enabledPermissions = array_keys(array_filter($this->menu_permissions, fn($enabled) => $enabled === true));
        
        $validPermissions = [];
        foreach ($enabledPermissions as $perm) {
            try {
                Permission::findByName($perm, 'web');
                $validPermissions[] = $perm;
            } catch (\Exception $e) {
                // Auto create permission if missing in DB but defined in structure
                Permission::create(['name' => $perm, 'guard_name' => 'web']);
                $validPermissions[] = $perm;
            }
        }

        $role->syncPermissions($validPermissions);

        session()->flash('message', 'Jabatan berhasil disimpan.');
        return $this->redirect(route('admin.roles.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.role-form');
    }
}
