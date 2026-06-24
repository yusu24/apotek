<?php

use App\Livewire\Reports\SalesChart;
use App\Models\User;
use Livewire\Livewire;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('sales chart component can be mounted and defaults to active month', function () {
    // Create permission and user
    \Spatie\Permission\Models\Permission::create(['name' => 'view sales reports']);
    $user = User::factory()->create();
    $user->givePermissionTo('view sales reports');
    $this->actingAs($user);

    $expectedStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
    $expectedEndDate = Carbon::now()->endOfMonth()->format('Y-m-d');

    Livewire::test(SalesChart::class)
        ->assertStatus(200)
        ->assertSet('period', 'active_month')
        ->assertSet('startDate', $expectedStartDate)
        ->assertSet('endDate', $expectedEndDate);
});

test('changing period to daily updates dates correctly', function () {
    \Spatie\Permission\Models\Permission::create(['name' => 'view sales reports']);
    $user = User::factory()->create();
    $user->givePermissionTo('view sales reports');
    $this->actingAs($user);

    $expectedStartDate = Carbon::now()->subDays(30)->format('Y-m-d');
    $expectedEndDate = Carbon::now()->format('Y-m-d');

    Livewire::test(SalesChart::class)
        ->set('period', 'daily')
        ->assertSet('startDate', $expectedStartDate)
        ->assertSet('endDate', $expectedEndDate);
});
