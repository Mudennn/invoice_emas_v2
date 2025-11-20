<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pure Gold Account Report Report - {{ $companyName }}</title>
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
                <h2 class="report-title">Pure Gold Account Report</h2>
                <div style="line-height: 1.5;">
                    <p style="font-size: 0.688rem !important; color: black !important;"><strong>Company:</strong> {{ $companyName }}</p>
                    <p style="font-size: 0.688rem !important; color: black !important;"><strong>Report Period:</strong> 
                        @php
                            $monthNames = [
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                            ];
                        @endphp
                        {{ $monthNames[$month] }} {{ $year }}
                    </p>
                    <p style="font-size: 0.688rem !important; color: black !important;"><strong>Generated Date:</strong> {{ now()->timezone('Asia/Kuala_Lumpur')->format('d F Y') }}</p>
                    <p style="font-size: 0.688rem !important; color: black !important;"><strong>Generated Time:</strong> {{ now()->timezone('Asia/Kuala_Lumpur')->format('h:i:s A') }}</p>
                </div>
            </div>

            <div class="invoice-items">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Date</th>
                            <th style="width: 30%;">Documents</th>
                            <th class="total-data" style="width: 15%;">IN</th>
                            <th class="total-data" style="width: 15%;">OUT</th>
                            <th class="total-data" style="width: 20%;">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($isReport as $report)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($report->document_date)->format('d/m/Y') }}</td>
                                <td>{{ $report->document_no }}</td>
                                <td class="total-data">
                                    @if($report->in_weight > 0)
                                        {{ number_format($report->in_weight, 2) }}
                                    @endif
                                </td>
                                <td class="total-data">
                                    @if($report->out_weight > 0)
                                        {{ number_format($report->out_weight, 2) }}
                                    @endif
                                </td>
                                <td class="total-data {{ $report->balance < 0 ? 'text-danger' : '' }}">
                                    {{ number_format($report->balance, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="total">Total</td>
                            <td class="total">{{ number_format($isReport->sum('in_weight'), 2) }}</td>
                            <td class="total">{{ number_format($isReport->sum('out_weight'), 2) }}</td>
                            <td class="total {{ $isReport->last()->balance < 0 ? 'text-danger' : '' }}">
                                {{ number_format($isReport->last()->balance ?? 0, 2) }}
                            </td>
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