<table>
    <thead>
        <tr>
            <th colspan="2" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">Periode: {{ \Carbon\Carbon::parse($data['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data['end_date'])->format('d/m/Y') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr><td></td><td></td></tr>
        
        {{-- REVENUE --}}
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">PENDAPATAN</td>
            <td style="background-color: #f3f4f6;"></td>
        </tr>
        @foreach($data['revenue_accounts'] as $account)
        <tr>
            <td>{{ $account->code }} - {{ $account->name }}</td>
            <td style="text-align: right;">{{ $account->balance }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold;">TOTAL PENDAPATAN</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #000;">{{ $data['total_revenue'] }}</td>
        </tr>
        
        <tr><td></td><td></td></tr>

        {{-- COGS --}}
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">HARGA POKOK PENJUALAN (HPP)</td>
            <td style="background-color: #f3f4f6;"></td>
        </tr>
        @foreach($data['cogs_accounts'] as $account)
        <tr>
            <td>{{ $account->code }} - {{ $account->name }}</td>
            <td style="text-align: right;">{{ $account->balance }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold;">TOTAL HPP</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #000;">{{ $data['total_cogs'] }}</td>
        </tr>
        
        <tr><td></td><td></td></tr>
        
        {{-- GROSS PROFIT --}}
        <tr>
            <td style="font-weight: bold; background-color: #e5e7eb;">LABA KOTOR</td>
            <td style="font-weight: bold; text-align: right; background-color: #e5e7eb;">{{ $data['gross_profit'] }}</td>
        </tr>
        
        <tr><td></td><td></td></tr>

        {{-- OPERATING EXPENSES --}}
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">BEBAN OPERASIONAL</td>
            <td style="background-color: #f3f4f6;"></td>
        </tr>
        @foreach($data['operating_expense_accounts'] as $account)
        <tr>
            <td>{{ $account->code }} - {{ $account->name }}</td>
            <td style="text-align: right;">{{ $account->balance }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold;">TOTAL BEBAN OPERASIONAL</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #000;">{{ $data['total_operating_expenses'] }}</td>
        </tr>
        
        <tr><td></td><td></td></tr>

        {{-- OTHER EXPENSES --}}
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">BEBAN LAIN-LAIN</td>
            <td style="background-color: #f3f4f6;"></td>
        </tr>
        @foreach($data['other_expense_accounts'] as $account)
        <tr>
            <td>{{ $account->code }} - {{ $account->name }}</td>
            <td style="text-align: right;">{{ $account->balance }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold;">TOTAL BEBAN LAIN-LAIN</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #000;">{{ $data['total_other_expenses'] }}</td>
        </tr>
        
        <tr><td></td><td></td></tr>

        {{-- TAX EXPENSES --}}
        @if(isset($data['tax_accounts']) && $data['tax_accounts']->count() > 0)
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">BEBAN PAJAK (TAX)</td>
            <td style="background-color: #f3f4f6;"></td>
        </tr>
        @foreach($data['tax_accounts'] as $account)
        <tr>
            <td>{{ $account->code }} - {{ $account->name }}</td>
            <td style="text-align: right;">{{ $account->balance }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold;">TOTAL BEBAN PAJAK</td>
            <td style="font-weight: bold; text-align: right; border-top: 1px solid #000;">{{ $data['total_tax_expenses'] }}</td>
        </tr>
        <tr><td></td><td></td></tr>
        @endif

        {{-- NET INCOME --}}
        <tr>
            <td style="font-weight: bold;">LABA SEBELUM PAJAK</td>
            <td style="font-weight: bold; text-align: right;">{{ $data['net_income_before_tax'] ?? $data['net_income'] }}</td>
        </tr>
        
        <tr>
            <td style="font-weight: bold; background-color: #1e40af; color: #ffffff;">LABA BERSIH</td>
            <td style="font-weight: bold; text-align: right; background-color: #1e40af; color: #ffffff;">{{ $data['net_income'] }}</td>
        </tr>
    </tbody>
</table>
