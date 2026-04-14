<table>
    <thead>
        <tr>
            <th colspan="6" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN LABA RUGI (DETAIL)</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        
        {{-- SUMMARY --}}
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #e5e7eb;">RINGKASAN</td>
            <td colspan="4" style="background-color: #e5e7eb;"></td>
        </tr>
        <tr>
            <td>Penjualan Bersih</td>
            <td style="text-align: right;">{{ $data['revenue'] }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>Total HPP</td>
            <td style="text-align: right;">{{ $data['cogs'] }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Laba Kotor</td>
            <td style="font-weight: bold; text-align: right;">{{ $data['grossProfit'] }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>Total Beban Ops</td>
            <td style="text-align: right;">{{ $data['operatingExpenses'] }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>Total Beban Pajak</td>
            <td style="text-align: right;">{{ $data['taxExpenses'] }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #1e40af; color: #ffffff;">LABA BERSIH</td>
            <td style="font-weight: bold; text-align: right; background-color: #1e40af; color: #ffffff;">{{ $data['netProfit'] }}</td>
            <td colspan="4"></td>
        </tr>

        <tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>

        {{-- SALES DETAIL --}}
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #f3f4f6;">DETAIL PENJUALAN</td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Tanggal</td>
            <td>No. Ref</td>
            <td>Subtotal</td>
            <td>Diskon</td>
            <td>Pajak</td>
            <td>Total Netto</td>
        </tr>
        @foreach($data['salesDetails'] as $sale)
        <tr>
            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $sale->invoice_no }}</td>
            <td style="text-align: right;">{{ $sale->total_amount }}</td>
            <td style="text-align: right;">{{ $sale->discount }}</td>
            <td style="text-align: right;">{{ $sale->tax }}</td>
            <td style="text-align: right;">{{ $sale->total_amount - $sale->discount }}</td>
        </tr>
        @endforeach

        <tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>

        {{-- COGS DETAIL --}}
        <tr>
            <td colspan="5" style="font-weight: bold; background-color: #f3f4f6;">DETAIL HPP (FIFO-ISH)</td>
            <td></td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Tanggal Jual</td>
            <td>Produk</td>
            <td style="text-align: center;">Qty</td>
            <td style="text-align: right;">Harga Beli</td>
            <td style="text-align: right;">Total HPP</td>
            <td></td>
        </tr>
        @foreach($data['cogsDetails'] as $item)
        <tr>
            <td>{{ \Carbon\Carbon::parse($item->sale_date)->format('d/m/Y') }}</td>
            <td>{{ $item->product_name }}</td>
            <td style="text-align: center;">{{ $item->quantity }}</td>
            <td style="text-align: right;">{{ $item->cost_price }}</td>
            <td style="text-align: right;">{{ $item->quantity * $item->cost_price }}</td>
            <td></td>
        </tr>
        @endforeach

        <tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>

        {{-- EXPENSES DETAIL --}}
        <tr>
            <td colspan="4" style="font-weight: bold; background-color: #f3f4f6;">DETAIL BEBAN</td>
            <td></td>
            <td></td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Tanggal</td>
            <td>Keterangan</td>
            <td>Kategori</td>
            <td style="text-align: right;">Jumlah</td>
            <td></td>
            <td></td>
        </tr>
        @foreach($data['expenseDetails'] as $expense)
        <tr>
            <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
            <td>{{ $expense->description }}</td>
            <td>{{ $expense->category }}</td>
            <td style="text-align: right;">{{ $expense->amount }}</td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
        @foreach($data['taxDetails'] as $tax)
        <tr>
            <td>{{ \Carbon\Carbon::parse($tax->date)->format('d/m/Y') }}</td>
            <td>{{ $tax->description }}</td>
            <td>PAJAK</td>
            <td style="text-align: right;">{{ $tax->amount }}</td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
