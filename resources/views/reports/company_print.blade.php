<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Sales Report - {{ $companyName }}</title>
    @include('layouts.styles.print')
    @include('layouts.styles.index')
</head>

<body>
    <div class="invoice-container">
        <div class="invoice-details">
            <div class="header">
                <div class="company-header">
                    <div class="company-name">{{ $ourCompany->company_name }}</div>
                    <div>{{ $ourCompany->address_line_1 }}, {{ $ourCompany->address_line_2 }},
                        {{ $ourCompany->postcode }}
                        {{ $ourCompany->city }},
                        {{ $ourCompany->s_state }} </div>
                    <div>{{ $ourCompany->contact }}</div>
                    <div>{{ $ourCompany->email }}</div>
                </div>
            </div>

            <div class="report-header" style="margin-bottom: 24px;">
                <h2 class="report-title">Company Sales Report</h2>
                <div style="line-height: 1.5;">
                    <p style="font-size: 0.688rem !important; color: black !important;"><strong>Company:</strong>
                        {{ $companyName }}</p>
                    <p style="font-size: 0.688rem !important; color: black !important;"><strong>Report Period:</strong>
                        {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
                    <p style="font-size: 0.688rem !important; color: black !important;"><strong>Generated Date:</strong>
                        {{ now()->timezone('Asia/Kuala_Lumpur')->format('d F Y') }}</p>
                    <p style="font-size: 0.688rem !important; color: black !important;"><strong>Generated Time:</strong>
                        {{ now()->timezone('Asia/Kuala_Lumpur')->format('h:i:s A') }}</p>
                </div>
            </div>

            <div class="invoice-items">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;" class="no-title">No</th>
                            <th style="width: 20%;">Document No</th>
                            <th style="width: 15%;">Date</th>
                            <th class="total-data">Subtotal</th>
                            <th class="total-data">Payment</th>
                            <th class="total-data">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salesReport as $report)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $report->document_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($report->document_date)->format('d-m-Y') }}</td>
                                {{-- <td class="total-data">
                                    @if ($report->document_type == 'Credit Note')
                                        RM{{ number_format($report->amount ?? 0, 2) }}
                                    @else
                                        RM{{ number_format($report->subtotal ?? 0, 2) }}
                                    @endif
                                </td> --}}
                                <td class="total-data">
                                    @if ($report->document_type == 'Credit Note')
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>RM</span>
                                            <span>{{ number_format($report->amount ?? 0, 2) }}</span>
                                        </div>
                                    @else
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>RM</span>
                                            <span>{{ number_format($report->subtotal ?? 0, 2) }}</span>
                                        </div>
                                    @endif
                                </td>
                                    <td class="total-data">
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>RM</span>
                                       {{ number_format($report->total_payment ?? 0, 2) }}
                                    </div>

                                </td>
                                    <td class="total-data {{ ($report->balance ?? 0) > 0 ? 'text-danger' : '' }}">
                                    <div style="display: flex; justify-content: space-between;">
                                        @if(($report->balance ?? 0) < 0)
                                            <span>-RM</span>
                                            <span>{{ number_format(abs($report->balance ?? 0), 2) }}</span>
                                        @else
                                            <span>RM</span>
                                            <span>{{ number_format($report->balance ?? 0, 2) }}</span>
                                        @endif
                                    </div>

                                </td>
                                {{-- <td class="total-data">
                                    RM{{ number_format($report->total_payment ?? 0, 2) }}
                                </td>
                                <td class="total-data {{ ($report->balance ?? 0) > 0 ? 'text-danger' : '' }}">
                                    RM{{ number_format($report->balance ?? 0, 2) }}
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="total">Total</td>
                            <td class="total">RM{{ number_format($salesReport->sum('subtotal'), 2) }}</td>
                            <td class="total">RM{{ number_format($salesReport->sum('total_payment'), 2) }}</td>
                            <td class="total {{ $salesReport->sum('balance') > 0 ? 'text-danger' : '' }}">
                                RM{{ number_format($salesReport->sum('balance'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
