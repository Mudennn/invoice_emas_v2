<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    @include('layouts.styles.print')
    @include('layouts.styles.index')
</head>

<body>
    @php
        $itemsPerPage = 15;
        $items = $salesReport->values();
        $chunks = $items->chunk($itemsPerPage);
        $totalPages = $chunks->count(); 
    @endphp

    @foreach ($chunks as $pageNum => $pageItems)
        <div class="invoice-container {{ $pageNum > 0 ? 'page-break' : '' }}">
            <div class="invoice-details">
                <div class="header">
                    <div class="company-header">
                        <h6 class="company-name">{{ $ourCompany->company_name }}</h6>
                        <p>{{ $ourCompany->address_line_1 }}, {{ $ourCompany->address_line_2 }},
                            {{ $ourCompany->postcode }}
                            {{ $ourCompany->city }},
                            {{ $ourCompany->s_state }} </p>
                        <p>{{ $ourCompany->contact }}</p>
                        <p>{{ $ourCompany->email }}</p>
                    </div>
                </div>

                <div class="report-header" style="margin-bottom: 24px;">
                    <h2 class="report-title">Sales Report</h2>
                    <div style="line-height: 1.5;">
                        <p style="font-size: 0.688rem !important; color: black !important;"><strong>Report Period:</strong>
                            {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} -
                            {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
                        <p style="font-size: 0.688rem !important; color: black !important;"><strong>Generated Date:</strong>
                            {{ now()->timezone('Asia/Kuala_Lumpur')->format('d F Y') }}</p>
                        <p style="font-size: 0.688rem !important; color: black !important;"><strong>Generated Time:</strong>
                            {{ now()->timezone('Asia/Kuala_Lumpur')->format('h:i:s A') }}</p>
                        <p style="font-size: 0.688rem !important; color: black !important;"><strong>Page:</strong>
                            {{ $pageNum + 1 }} of {{ $totalPages }}</p>
                    </div>
                </div>

                <div class="invoice-items">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 10%;">Invoice No</th>
                                <th class="total-data">Amount</th>
                                <th class="total-data">Gold Price</th>
                                <th class="total-data">Pure Gold</th>
                                {{-- <th class="total-data">Quantity</th>
                                <th class="total-data">Unit Price</th>
                                <th class="total-data">Workmanship</th> --}}
                                @foreach ($customerCompanies as $company)
                                    <th class="total-data">{{ $company }}</th>
                                @endforeach
                                <th class="total-data">Others</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pageItems as $index => $report)
                                <tr>
                                    <td class="text-center">{{ ($pageNum * $itemsPerPage) + $index + 1 }}</td>
                                    <td>{{ $report->invoice_no }}</td>
                                    <td class="total-data">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>RM</span>
                                            <span>{{ number_format($report->amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="total-data">
                                        {{ number_format($report->gold_price, 2) == 0 ? '-' : '' . number_format($report->gold_price, 2) }}
                                    </td>
                                    <td class="total-data">
                                        {{ number_format($report->remark_total, 2) == 0 ? '-' : number_format($report->remark_total, 2) }}
                                    </td>
                                    {{-- <td class="total-data">
                                        {{ number_format($report->quantity, 2) == 0 ? '-' : number_format($report->quantity, 2) }}
                                    </td>
                                    <td class="total-data">
                                        {{ number_format($report->unit_price, 2) == 0 ? '-' : 'RM' . number_format($report->unit_price, 2) }}
                                    </td>
                                    <td class="total-data">RM{{ number_format($report->workmanship, 2) }}</td> --}}
                                    @foreach ($customerCompanies as $company)
                                        @php
                                            $columnName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($company));
                                            $columnName = rtrim($columnName, '_');
                                        @endphp
                                        <td class="total-data">
                                            <div style="display: flex; justify-content: space-between;">
                                                <span>RM</span>
                                                <span>
                                                    {{ $report->$columnName != 0 ? '' . number_format($report->$columnName, 2) : '-' }}</span>
                                            </div>

                                        </td>
                                    @endforeach
                                    <td class="total-data">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>RM</span>
                                            <span>
                                                {{ $report->others != 0 ? '' . number_format($report->others, 2) : '-' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($pageNum === $totalPages - 1)
                            <tfoot class="totals-table">
                                <tr>
                                    <td></td>
                                    <td class="total">Total</td>
                                    <td class="total">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>RM</span>
                                            <span>
                                                {{ number_format($items->sum('amount'), 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="total">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>RM</span>
                                            <span>
                                                {{ number_format($items->sum('gold_price'), 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="total">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>PG</span>
                                            <span>
                                                {{ number_format($items->sum('remark_total'), 2) }}</span>
                                        </div>
                                    </td>
                                    {{-- <td class="total">{{ number_format($items->sum('quantity'), 2) }}</td>
                                    <td class="total">RM{{ number_format($items->sum('unit_price'), 2) }}</td>
                                    <td class="total">RM{{ number_format($items->sum('workmanship'), 2) }}</td> --}}
                                   @foreach ($customerCompanies as $company)
                                        @php
                                            $columnName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($company));
                                            $columnName = rtrim($columnName, '_');
                                        @endphp
                                        <td class="total">
                                            <div style="display: flex; justify-content: space-between;">
                                                <span>RM</span>
                                                <span>
                                                    {{ number_format($items->sum($columnName), 2) }}</span>
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="total">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>RM</span>
                                            <span>
                                                {{ number_format($items->sum('others'), 2) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
