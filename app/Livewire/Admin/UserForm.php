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
    public $menu_permissions = [];

    public function getPermissionStructureProperty()
    {
        return [
            'Dashboard' => [
                'icon' => 'home',
                'color' => 'blue',
                'items' => [
                    'view dashboard' => ['label' => 'Halaman Dashboard', 'type' => 'view'],
                    'view financial overview' => ['label' => 'Ringkasan Keuangan (Hutang/Piutang)', 'type' => 'view'],
                ]
            ],
            'Kasir (POS)' => [
                'icon' => 'shopping-cart',
                'color' => 'purple',
                'items' => [
                    'access pos' => ['label' => 'Akses Mesin Kasir', 'type' => 'view'],
                    'create sale' => ['label' => 'Input Penjualan', 'type' => 'action'], // Core action of POS
                    'void transaction' => ['label' => 'Batalkan Transaksi', 'type' => 'action'], // Critical action
                    'view sales history' => ['label' => 'Riwayat Penjualan (Kasir)', 'type' => 'view'],
                ]
            ],
            'Stok & Pengadaan' => [
                'icon' => 'archive',
                'color' => 'orange',
                'items' => [
                    'view stock' => ['label' => 'Lihat Stok & Opname', 'type' => 'view'],
                    'import stock' => ['label' => 'Import Stok via Excel', 'type' => 'action'],
                    'adjust stock' => ['label' => 'Penyesuaian Stok', 'type' => 'action'],
                    'view stock movements' => ['label' => 'Riwayat Mutasi Stok', 'type' => 'view'],
                    'view purchase orders' => ['label' => 'Pesanan Pembelian (PO)', 'type' => 'view'],
                    'view goods receipts' => ['label' => 'Penerimaan Pesanan', 'type' => 'view'],
                    'manage expired products' => ['label' => 'Kelola Produk Kadaluarsa', 'type' => 'view'], // Menu access
                ]
            ],
            'Retur Barang' => [
                'icon' => 'refresh',
                'color' => 'red',
                'items' => [
                    'manage sales returns' => ['label' => 'Retur Penjualan', 'type' => 'view'], // Menu access
                    'manage purchase returns' => ['label' => 'Retur Pembelian', 'type' => 'view'], // Menu access
                ]
            ],
            'Data Master' => [
                'icon' => 'database',
                'color' => 'green',
                'items' => [
                    'view products' => ['label' => 'Lihat Produk', 'type' => 'view'],
                    'create products' => ['label' => 'Tambah Produk', 'type' => 'action'],
                    'edit products' => ['label' => 'Edit Produk', 'type' => 'action'],
                    'delete products' => ['label' => 'Hapus Produk', 'type' => 'action'],
                    'manage categories' => ['label' => 'Kategori Produk', 'type' => 'view'], // Menu access
                    'manage units' => ['label' => 'Master Satuan', 'type' => 'view'], // Menu access
                    'manage product units' => ['label' => 'Konversi Satuan', 'type' => 'view'], // Menu access
                    'manage suppliers' => ['label' => 'Supplier', 'type' => 'view'], // Menu access
                    'manage customers' => ['label' => 'Pelanggan', 'type' => 'view'], // Menu access
                    'import_master_data' => ['label' => 'Import Excel Master Data (Produk/Supplier/Pelanggan)', 'type' => 'action'],
                ]
            ],
            'Laporan Keuangan' => [
                'icon' => 'chart-pie',
                'color' => 'indigo',
                'items' => [
                    'view trial balance' => ['label' => 'Neraca Saldo Awal', 'type' => 'view'],
                    'view balance sheet' => ['label' => 'Neraca Saldo Akhir', 'type' => 'view'],
                    'view profit loss' => ['label' => 'Laporan Laba Rugi', 'type' => 'view'],
                    'view income statement' => ['label' => 'Laporan Arus Kas', 'type' => 'view'],
                    'view general ledger' => ['label' => 'Buku Besar', 'type' => 'view'],
                    'view ppn report' => ['label' => 'Laporan PPN', 'type' => 'view'],
                    'view ap aging report' => ['label' => 'Laporan Umur Hutang & Piutang', 'type' => 'view'],
                ]
            ],
            'Laporan Operasional' => [
                'icon' => 'clipboard-list',
                'color' => 'teal',
                'items' => [
                    'view reports' => ['label' => 'Akses Menu Laporan', 'type' => 'view'],
                    'view sales reports' => ['label' => 'Laporan Penjualan Detail', 'type' => 'view'],
                    'view stock' => ['label' => 'Laporan Stok', 'type' => 'view'], 
                    'view product margin report' => ['label' => 'Laporan Margin Produk', 'type' => 'view'],
                    'view stock movements' => ['label' => 'Riwayat Transaksi Produk', 'type' => 'view'],
                ]
            ],
            'Keuangan & Administrasi' => [
                'icon' => 'calculator',
                'color' => 'cyan',
                'items' => [
                    'view accounts' => ['label' => 'Daftar Akun (COA)', 'type' => 'view'],
                    'manage accounts' => ['label' => 'Kelola Akun', 'type' => 'action'], // Usually a critical action page
                    'view journals' => ['label' => 'Lihat Jurnal Umum', 'type' => 'view'],
                    'create journal' => ['label' => 'Input Jurnal Manual', 'type' => 'action'],
                    'edit journals' => ['label' => 'Edit Jurnal Draft', 'type' => 'action'],
                    'delete journals' => ['label' => 'Hapus/Reversal Jurnal', 'type' => 'action'],
                    'view opening balances' => ['label' => 'Lihat Neraca Awal', 'type' => 'view'],
                    'edit opening balances' => ['label' => 'Input/Edit Neraca Awal', 'type' => 'action'],
                    'lock opening balances' => ['label' => 'Kunci Neraca Awal', 'type' => 'action'],
                    'unlock opening balances' => ['label' => 'Buka Kunci Neraca Awal', 'type' => 'action'],
                    'view expenses' => ['label' => 'Daftar Pengeluaran', 'type' => 'view'],
                    'manage expense categories' => ['label' => 'Kategori Pengeluaran', 'type' => 'view'], // Menu access
                    'manage finance' => ['label' => 'Manajemen Aset Tetap', 'type' => 'view'], // Menu access
                ]
            ],
            'Pengaturan Sistem' => [
                'icon' => 'cog',
                'color' => 'gray',
                'items' => [
                    'manage settings' => ['label' => 'Identitas Toko', 'type' => 'view'], // Menu access
                    'manage pos settings' => ['label' => 'Konfigurasi Kasir', 'type' => 'view'], // Menu access
                    'manage users' => ['label' => 'Kelola User', 'type' => 'view'], // Menu access
                    'view activity logs' => ['label' => 'Log Aktivitas', 'type' => 'view'],
                    'view audit log' => ['label' => 'Audit Log', 'type' => 'view'],
                ]
            ],
        ];
    }

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
