<?php

use App\Livewire\Pos\Cashier;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use App\Models\Sale;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('cashier component can be mounted', function () {
    \Spatie\Permission\Models\Permission::create(['name' => 'access pos']);
    $user = User::factory()->create();
    $user->givePermissionTo('access pos');
    $this->actingAs($user);

    Livewire::test(Cashier::class)
        ->assertStatus(200);
});

test('cashier can add items to cart and checkout with fifo stock deduction', function () {
    $this->withoutExceptionHandling();
    // Setup Data
    \Spatie\Permission\Models\Permission::create(['name' => 'access pos']);
    $user = User::factory()->create();
    $user->givePermissionTo('access pos');
    $this->actingAs($user);

    $category = Category::create(['name' => 'Obat Keras', 'slug' => 'obat-keras']);
    $unit = Unit::create(['name' => 'Strip']);
    
    $product = Product::create([
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'name' => 'Amoxicillin',
        'slug' => 'amoxicillin',
        'barcode' => '123456',
        'sell_price' => 5000,
        'min_stock' => 5,
    ]);

    // Create 2 Batches
    // Batch A: Expiring soon (should be taken first) - Stock 10
    $batchA = Batch::create([
        'product_id' => $product->id,
        'batch_no' => 'BATCH-A',
        'expired_date' => now()->addMonth(),
        'stock_in' => 10,
        'stock_current' => 10,
        'buy_price' => 4000
    ]);

    // Batch B: Expiring later - Stock 20
    $batchB = Batch::create([
        'product_id' => $product->id,
        'batch_no' => 'BATCH-B',
        'expired_date' => now()->addYear(),
        'stock_in' => 20,
        'stock_current' => 20,
        'buy_price' => 4000
    ]);

    // Scenario: User buys 15 items. 
    // Expectation: 10 from Batch A (Emptying it), 5 from Batch B.
    
    Livewire::test(Cashier::class)
        ->call('addToCart', $product->id) // 1
        ->call('updateQty', $product->id, 15) // Update to 15
        ->set('payment_method', 'cash')
        ->set('cash_amount', 100000) // 15 * 5000 = 75000
        ->call('processPayment')
        ->assertHasNoErrors();

    // Verify Sales
    $sale = Sale::first();
    expect($sale)->not->toBeNull();
    expect($sale->total_amount)->toEqual(75000);
    expect($sale->change_amount)->toEqual(25000);

    // Verify Stock Deduction (FIFO)
    $batchA->refresh();
    $batchB->refresh();

    expect($batchA->stock_current)->toEqual(0); // All 10 taken
    expect($batchB->stock_current)->toEqual(15); // 20 - 5 taken

    // Verify Sale Items
    $this->assertDatabaseHas('sale_items', [
        'sale_id' => $sale->id,
        'batch_id' => $batchA->id,
        'quantity' => 10
    ]);

    $this->assertDatabaseHas('sale_items', [
        'sale_id' => $sale->id,
        'batch_id' => $batchB->id,
        'quantity' => 5
    ]);
});
