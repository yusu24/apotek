<?php

use App\Models\User;
use App\Models\Sale;
use App\Imports\OmsetImport;
use App\Exports\OmsetTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

test('authorized user can download omset template', function () {
    \Spatie\Permission\Models\Permission::create(['name' => 'view sales reports']);
    $user = User::factory()->create();
    $user->givePermissionTo('view sales reports');
    $this->actingAs($user);

    Excel::fake();

    $response = $this->get(route('import.download-omset-template'));

    $response->assertStatus(200);
    Excel::assertDownloaded('template_omset.xlsx', function (OmsetTemplateExport $export) {
        return $export->headings() === ['tanggal', 'tahun', 'omset', 'hpp', 'laba'];
    });
});

test('authorized user can upload omset excel', function () {
    \Spatie\Permission\Models\Permission::create(['name' => 'view sales reports']);
    $user = User::factory()->create();
    $user->givePermissionTo('view sales reports');
    $this->actingAs($user);

    Excel::fake();

    $file = UploadedFile::fake()->create('omset.xlsx');

    $response = $this->post(route('import.omset'), [
        'file' => $file,
    ]);

    $response->assertRedirect();
    Excel::assertImported('omset.xlsx');
});

test('omset import inserts records into database', function () {
    // Seed accounts since import will post to journal
    $this->seed(\Database\Seeders\AccountSeeder::class);

    $user = User::factory()->create();
    $this->actingAs($user);

    $rows = new Collection([
        [
            'tanggal' => '2022-06-15',
            'tahun' => '',
            'omset' => '1500000',
            'hpp' => '1000000',
            'laba' => '500000',
        ],
        [
            'tanggal' => '',
            'tahun' => '2023',
            'omset' => '2500000',
            'hpp' => '1800000',
            'laba' => '700000',
        ]
    ]);

    $import = new OmsetImport();
    $import->collection($rows);

    expect($import->getSuccessCount())->toBe(2);
    expect(Sale::count())->toBe(2);

    $sale1 = Sale::where('date', 'like', '2022-06-15%')->first();
    expect($sale1->total_amount)->toEqual(1500000);
    expect($sale1->grand_total)->toEqual(1500000);
    expect($sale1->dpp)->toEqual(1500000);
    expect($sale1->cogs)->toEqual(1000000);
    expect($sale1->profit)->toEqual(500000);
    expect($sale1->payment_method)->toBe('cash');

    $sale2 = Sale::where('date', 'like', '2023-01-01%')->first();
    expect($sale2->total_amount)->toEqual(2500000);
    expect($sale2->grand_total)->toEqual(2500000);
    expect($sale2->dpp)->toEqual(2500000);
    expect($sale2->cogs)->toEqual(1800000);
    expect($sale2->profit)->toEqual(700000);
    expect($sale2->payment_method)->toBe('cash');
});
