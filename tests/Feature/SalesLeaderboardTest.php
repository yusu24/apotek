<?php

use App\Livewire\Dashboard\SalesLeaderboard;
use App\Models\User;
use App\Models\Sale;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('sales leaderboard component can be mounted', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(SalesLeaderboard::class)
        ->assertStatus(200);
});

test('sales leaderboard displays turnover, transactions and ranks cashiers correctly', function () {
    // Create roles
    $cashierRole = Role::create(['name' => 'Kasir']);

    $cashier1 = User::factory()->create(['name' => 'Cashier A']);
    $cashier1->assignRole($cashierRole);

    $cashier2 = User::factory()->create(['name' => 'Cashier B']);
    $cashier2->assignRole($cashierRole);

    // Create completed sales for current month
    // Cashier 1: 2 sales, total 150,000
    Sale::create([
        'user_id' => $cashier1->id,
        'invoice_no' => 'INV-001',
        'date' => now()->format('Y-m-d'),
        'total_amount' => 50000,
        'grand_total' => 50000,
        'status' => 'completed',
    ]);
    Sale::create([
        'user_id' => $cashier1->id,
        'invoice_no' => 'INV-002',
        'date' => now()->format('Y-m-d'),
        'total_amount' => 100000,
        'grand_total' => 100000,
        'status' => 'completed',
    ]);

    // Cashier 2: 1 sale, total 75,000
    Sale::create([
        'user_id' => $cashier2->id,
        'invoice_no' => 'INV-003',
        'date' => now()->format('Y-m-d'),
        'total_amount' => 75000,
        'grand_total' => 75000,
        'status' => 'completed',
    ]);

    // An incomplete sale (should not count)
    Sale::create([
        'user_id' => $cashier2->id,
        'invoice_no' => 'INV-004',
        'date' => now()->format('Y-m-d'),
        'total_amount' => 200000,
        'grand_total' => 200000,
        'status' => 'pending',
    ]);

    // A completed sale from previous month (should not count)
    Sale::create([
        'user_id' => $cashier1->id,
        'invoice_no' => 'INV-005',
        'date' => now()->subMonth()->format('Y-m-d'),
        'total_amount' => 300000,
        'grand_total' => 300000,
        'status' => 'completed',
    ]);

    $this->actingAs($cashier1);

    Livewire::test(SalesLeaderboard::class)
        ->assertViewHas('monthlyTurnover', 225000)
        ->assertViewHas('monthlyTransactions', 3)
        ->assertSee('Cashier A')
        ->assertSee('Cashier B')
        ->assertSee('Rp 150.000')
        ->assertSee('Rp 75.000')
        ->assertSee('👑') // Rank 1 highlight
        ->assertSee('🥇')
        ->assertSee('🥈');
});
