<?php

// Test slugification
$headers = [
    'ID Produk (JANGAN DIUBAH)',
    'Barcode',
    'Nama Produk',
    'Kategori',
    'Satuan',
    'Stok Saat Ini',
    'Jumlah Masuk (Isi Disini)',
    'Tgl Kadaluarsa (YYYY-MM-DD)',
    'Harga Beli (Update jika perlu)',
    'Harga Jual (Update jika perlu)',
];

foreach ($headers as $header) {
    $slug = \Illuminate\Support\Str::slug($header, '_');
    echo "$header => $slug\n";
}
