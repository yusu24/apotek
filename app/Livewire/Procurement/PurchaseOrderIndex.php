<?php

namespace App\Livewire\Procurement;

use Livewire\Component;

class PurchaseOrderIndex extends Component
{
    use \Livewire\WithPagination;

    public $search = '';

    public function render()
    {
        $orders = \App\Models\PurchaseOrder::with('supplier', 'user')
            ->where('po_number', 'like', '%' . $this->search . '%')
            ->orWhereHas('supplier', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10)
            ->onEachSide(2);

        return view('livewire.procurement.purchase-order-index', compact('orders'));
    }

    public function delete($id)
    {
        $po = \App\Models\PurchaseOrder::findOrFail($id);
        if ($po->goodsReceipts()->exists()) {
            session()->flash('error', 'Tidak dapat menghapus PO yang sudah ada penerimaan barang.');
            return;
        }
        $po->delete();
        session()->flash('message', 'Pesanan pembelian berhasil dihapus.');
    }
}
