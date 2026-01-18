<table>
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; font-size: 14px; text-align: center;">NERACA SALDO (TRIAL BALANCE)</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">Periode: {{ \Carbon\Carbon::parse($data['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data['end_date'])->format('d/m/Y') }}</th>
        </tr>
        <tr><td></td><td></td><td></td><td></td></tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">KODE</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff;">NAMA AKUN</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff; text-align: right;">DEBIT</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #1e3a8a; color: #ffffff; text-align: right;">KREDIT</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sections = [
                ['label' => 'ASET', 'data' => $data['assets'], 'debit' => $data['total_assets_debit'], 'credit' => $data['total_assets_credit'], 'color' => 'eff6ff'],
                ['label' => 'KEWAJIBAN', 'data' => $data['liabilities'], 'debit' => $data['total_liabilities_debit'], 'credit' => $data['total_liabilities_credit'], 'color' => 'fefce8'],
                ['label' => 'EKUITAS', 'data' => $data['equity'], 'debit' => $data['total_equity_debit'], 'credit' => $data['total_equity_credit'], 'color' => 'faf5ff'],
                ['label' => 'PENDAPATAN', 'data' => $data['revenue'], 'debit' => $data['total_revenue_debit'], 'credit' => $data['total_revenue_credit'], 'color' => 'f0fdf4'],
                ['label' => 'BEBAN', 'data' => $data['expenses'], 'debit' => $data['total_expenses_debit'], 'credit' => $data['total_expenses_credit'], 'color' => 'fef2f2'],
            ];
        @endphp

        @foreach($sections as $section)
            @if(count($section['data']) > 0)
                <tr>
                    <td colspan="4" style="font-weight: bold; background-color: #f1f5f9;">{{ $section['label'] }}</td>
                </tr>
                @foreach($section['data'] as $account)
                    <tr>
                        <td style="border: 1px solid #e5e7eb;">{{ $account->code }}</td>
                        <td style="border: 1px solid #e5e7eb;">{{ $account->name }}</td>
                        <td style="border: 1px solid #e5e7eb; text-align: right;">{{ $account->total_debit > 0 ? $account->total_debit : 0 }}</td>
                        <td style="border: 1px solid #e5e7eb; text-align: right;">{{ $account->total_credit > 0 ? $account->total_credit : 0 }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="font-weight: bold; background-color: #{{ $section['color'] }}; border: 1px solid #e5e7eb;">Subtotal {{ $section['label'] }}</td>
                    <td style="font-weight: bold; text-align: right; background-color: #{{ $section['color'] }}; border: 1px solid #e5e7eb;">{{ $section['debit'] }}</td>
                    <td style="font-weight: bold; text-align: right; background-color: #{{ $section['color'] }}; border: 1px solid #e5e7eb;">{{ $section['credit'] }}</td>
                </tr>
            @endif
        @endforeach

        <tr><td></td><td></td><td></td><td></td></tr>
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #111827; color: #ffffff; border: 1px solid #000000;">TOTAL</td>
            <td style="font-weight: bold; text-align: right; background-color: #111827; color: #ffffff; border: 1px solid #000000;">{{ $data['grand_total_debit'] }}</td>
            <td style="font-weight: bold; text-align: right; background-color: #111827; color: #ffffff; border: 1px solid #000000;">{{ $data['grand_total_credit'] }}</td>
        </tr>
    </tbody>
</table>
