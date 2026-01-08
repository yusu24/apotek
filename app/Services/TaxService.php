<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaxService
{
    /**
     * Get PPN Keluaran (Output Tax) from Sales
     */
    public function getPpnKeluaran($startDate, $endDate)
    {
        return Sale::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->where('status', 'completed')
            ->where('ppn_mode', '!=', 'off')
            ->select([
                'id',
                'invoice_no',
                'date',
                'dpp',
                'tax as ppn_amount',
                'grand_total'
            ])
            ->orderBy('date')
            ->get();
    }

    /**
     * Get PPN Masukan (Input Tax) from Purchases
     */
    public function getPpnMasukan($startDate, $endDate)
    {
        $ppnRate = (float) \App\Models\Setting::get('pos_ppn_rate', 11) / 100;

        return DB::table('goods_receipt_items')
            ->join('goods_receipts', 'goods_receipt_items.goods_receipt_id', '=', 'goods_receipts.id')
            ->join('purchase_orders', 'goods_receipts.purchase_order_id', '=', 'purchase_orders.id')
            ->join('purchase_order_items', function($join) {
                $join->on('purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
                     ->on('goods_receipt_items.product_id', '=', 'purchase_order_items.product_id');
            })
            ->whereDate('goods_receipts.received_date', '>=', $startDate)
            ->whereDate('goods_receipts.received_date', '<=', $endDate)
            ->where('purchase_order_items.has_ppn', true)
            ->select([
                'goods_receipts.id',
                'goods_receipts.delivery_note_number',
                'goods_receipts.received_date as date',
                DB::raw('(goods_receipt_items.qty_received * goods_receipt_items.buy_price) as dpp'),
                DB::raw('ROUND((goods_receipt_items.qty_received * goods_receipt_items.buy_price) * ' . $ppnRate . ', 2) as ppn_amount')
            ])
            ->orderBy('goods_receipts.received_date')
            ->get();
    }

    /**
     * Get monthly summary of PPN
     */
    public function getMonthlySummary($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $ppnKeluaran = $this->getPpnKeluaran($startDate, $endDate);
        $ppnMasukan = $this->getPpnMasukan($startDate, $endDate);
        
        $totalPpnKeluaran = $ppnKeluaran->sum('ppn_amount');
        $totalPpnMasukan = $ppnMasukan->sum('ppn_amount');
        $kurangLebih = $totalPpnKeluaran - $totalPpnMasukan;
        
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_ppn_keluaran' => $totalPpnKeluaran,
            'total_ppn_masukan' => $totalPpnMasukan,
            'kurang_lebih' => $kurangLebih,
            'status' => $kurangLebih > 0 ? 'kurang_bayar' : ($kurangLebih < 0 ? 'lebih_bayar' : 'nihil'),
            'ppn_keluaran_details' => $ppnKeluaran,
            'ppn_masukan_details' => $ppnMasukan,
            'total_dpp_keluaran' => $ppnKeluaran->sum('dpp'),
            'total_dpp_masukan' => $ppnMasukan->sum('dpp'),
        ];
    }

    /**
     * Get yearly summary
     */
    public function getYearlySummary($year)
    {
        $monthlySummaries = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthlySummaries[$month] = $this->getMonthlySummary($year, $month);
        }
        
        return $monthlySummaries;
    }
}
