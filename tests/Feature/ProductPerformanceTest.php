<?php

use App\Livewire\Dashboard\ProductPerformance;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('product performance component can be mounted', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(ProductPerformance::class)
        ->assertStatus(200);
});

test('product performance component calculates and passes data correctly', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create some dummy products
    $product1 = Product::factory()->create(['name' => 'Panadol Extra']);
    $product2 = Product::factory()->create(['name' => 'Bodrex Sakit Kepala']);

    // Create a sale and items
    $sale = Sale::create([
        'user_id' => $user->id,
        'invoice_no' => 'INV-PP-001',
        'date' => now()->format('Y-m-d'),
        'total_amount' => 50000,
        'grand_total' => 50000,
        'status' => 'completed',
    ]);

    SaleItem::create([
        'sale_id' => $sale->id,
        'product_id' => $product1->id,
        'quantity' => 10,
        'sell_price' => 2000,
        'subtotal' => 20000,
    ]);

    SaleItem::create([
        'sale_id' => $sale->id,
        'product_id' => $product2->id,
        'quantity' => 4,
        'sell_price' => 1500,
        'subtotal' => 6000,
    ]);

    Livewire::test(ProductPerformance::class)
        ->assertViewHas('topSellingChart', function ($chart) {
            return in_array('Panadol Extra', $chart['labels']) && in_array(10, $chart['data']);
        })
        ->assertViewHas('slowMovingChart', function ($chart) {
            return in_array('Bodrex Sakit Kepala', $chart['labels']) && in_array(4, $chart['data']);
        });
});
