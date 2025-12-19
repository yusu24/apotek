<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

class ReceiptController extends Controller
{
    public function show($id)
    {
        $sale = Sale::with(['saleItems.product', 'user'])->findOrFail($id);
        
        return view('receipt', compact('sale'));
    }
}
