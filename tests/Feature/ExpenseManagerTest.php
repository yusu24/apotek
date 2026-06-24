<?php

use App\Livewire\Finance\ExpenseManager;
use App\Models\User;
use App\Models\Expense;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('expense manager component can be mounted', function () {
    \Spatie\Permission\Models\Permission::create(['name' => 'view expenses']);
    $user = User::factory()->create();
    $user->givePermissionTo('view expenses');
    $this->actingAs($user);

    Livewire::test(ExpenseManager::class)
        ->assertStatus(200);
});

test('expense manager component filters expenses by search query', function () {
    \Spatie\Permission\Models\Permission::create(['name' => 'view expenses']);
    $user = User::factory()->create();
    $user->givePermissionTo('view expenses');
    $this->actingAs($user);

    // Create expenses
    Expense::create([
        'date' => now()->format('Y-m-d'),
        'description' => 'Bayar Listrik Bulanan',
        'amount' => 500000,
        'category' => 'Operasional',
        'user_id' => $user->id,
    ]);

    Expense::create([
        'date' => now()->format('Y-m-d'),
        'description' => 'Beli Kertas A4 Printer',
        'amount' => 150000,
        'category' => 'Perlengkapan',
        'user_id' => $user->id,
    ]);

    // Test search matching description
    Livewire::test(ExpenseManager::class)
        ->set('search', 'Listrik')
        ->assertSee('Bayar Listrik Bulanan')
        ->assertDontSee('Beli Kertas A4');

    // Test search matching category
    Livewire::test(ExpenseManager::class)
        ->set('search', 'Perlengkapan')
        ->assertSee('Beli Kertas A4 Printer')
        ->assertDontSee('Bayar Listrik Bulanan');
});

test('expense manager component can trigger excel and pdf exports', function () {
    \Spatie\Permission\Models\Permission::create(['name' => 'view expenses']);
    $user = User::factory()->create();
    $user->givePermissionTo('view expenses');
    $this->actingAs($user);

    Livewire::test(ExpenseManager::class)
        ->call('exportExcel')
        ->assertHasNoErrors();

    Livewire::test(ExpenseManager::class)
        ->call('exportPdf')
        ->assertRedirect(route('pdf.expenses', [
            'search' => '',
            'from' => \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'),
            'to' => \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'),
        ]));
});
