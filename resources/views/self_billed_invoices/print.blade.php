<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Self Billed Invoice #{{ $self_billed_invoice->invoice_no }}</title>
    @include('layouts.styles.print')
    @include('layouts.styles.index')

    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>

<body>
    @php
        $itemsPerPage = 8;
        $items = $self_billed_invoice->self_billed_invoice_items->where('status', '0')->values();
        $chunks = $items->chunk($itemsPerPage);
        $totalPages = $chunks->count();
        $subtotal = $items->sum('total');
        $grandTotal = $subtotal;
    @endphp

    @foreach ($chunks as $pageNum => $pageItems)
        <div class="invoice-container {{ $pageNum > 0 ? 'page-break' : '' }}">
            <div class="invoice-details">
                @if ($pageNum === 0)
                    <div class="header">
                        <div class="company-header">
                            <h5 class="chinese-text">金晶珠宝制造商</h5>
                            <h6 class="company-name">{{ $ourCompany->company_name }}</h6>
                            <p class="company-address">{{ $ourCompany->address_line_1 }}, {{ $ourCompany->address_line_2 }},
                                {{ $ourCompany->postcode }}
                                {{ $ourCompany->city }},
                                {{ $ourCompany->s_state }} </p>
                            <p>{{ $ourCompany->contact }}</p>
                               <p>{{ $ourCompany->email }}</p>
                        </div>
                    </div>

                    <div class="customer-details">
                        <div class="left-side">
                            <h6>Issued To:</h6>
                            <p class="customer-name">{{ $self_billed_invoice->company_name }}</p>
                            @php
                                $profile = $customerProfile ?? $otherProfile;
                            @endphp
                            @if ($profile)
                                <p class="customer-address">
                                    {{ $profile->address_line_1 }}, {{ $profile->address_line_2 }}
                                </p>
                                <p class="customer-address">
                                    {{ $profile->postcode }}, {{ $profile->city }}, {{ $profile->s_state }}
                                </p>
                                <p>{{ $profile->contact_1 }}</p>
                                <p>{{ $profile->email_1 }}</p>
                            @endif
                        </div>
                        {{-- <div class="center-side">
                        </div> --}}
                        <div class="right-side">
                            <h6 class="invoice-no">Invoice No: <span class="invoice-no-text">{{ $self_billed_invoice->invoice_no }}</span></h6>
                            <p class="invoice-date">Date:
                                {{ \Carbon\Carbon::parse($self_billed_invoice->invoice_date)->format('d F Y') }}</p>
                            <p class="page-number">Page {{ $pageNum + 1 }} of {{ $totalPages }}</p>
                        </div>
                    </div>
                @else
                    <div class="continuation-header">
                        <div class="left-side">
                            <div class="company-name">{{ $ourCompany->company_name }}</div>
                        </div>
                        <div class="right-side">
                            <p class="invoice-no">Invoice No: <span class="invoice-no-text">{{ $self_billed_invoice->invoice_no }}</span></p>
                            <p class="invoice-date">Date:
                                {{ \Carbon\Carbon::parse($self_billed_invoice->invoice_date)->format('d F Y') }}</p>
                            <p class="page-number">Page {{ $pageNum + 1 }} of {{ $totalPages }}</p>
                        </div>
                    </div>
                @endif

                <div class="invoice-items">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="no-text">No</th>
                                <th style="width: 10%;">Reference No</th>
                                <th style="width: 15%;">Particulars</th>
                                <th style="width: 5%;" class="total-data">Quantity</th>
                                <th style="width: 5%;" class="total-data">Weight</th>
                                <th style="width: 5%;" class="total-data">Wastage</th>
                                <th style="width: 6%;" class="total-data">Total Weight</th>
                                <th style="width: 9%;" class="total-data">Gold Price</th>
                                <th style="width: 6%;" class="total-data">Pure Gold</th>
                                <th style="width: 5%;" class="total-data">Workmanship</th>
                                <th style="width: 8%;" class="total-data">Unit Price</th>
                                <th style="width: 10%;" class="total-data">Total</th>
                                <th style="width: 15%;">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pageItems as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-wrap">{{ $item->reference_no }} <br> {{ $item->custom_reference }}
                                    </td>
                                    <td>{{ $item->particulars }}</td>
                                    <td class="total-data">{{ number_format($item->quantity) == 0 ? '-' : number_format($item->quantity) }} {{ $item->s_pair }} </td>
                                    <td class="total-data">{{ number_format($item->weight, 2) == 0 ? '-' : number_format($item->weight, 2) }}</td>
                                    <td class="total-data">{{ number_format($item->wastage) == 0 ? '-' : number_format($item->wastage) . '%' }}</td>
                                    <td class="total-data">{{ number_format($item->total_weight, 2) == 0 ? '-' : number_format($item->total_weight, 2) }}</td>
                                    <td class="total-data">{{ number_format($item->gold, 2) == 0 ? '-' : 'RM ' . number_format($item->gold, 2) }}
                                    </td>
                                    <td class="total-data">{{ $item->pure_gold }}</td>
                                    <td class="text-end">{{ $item->workmanship == 'FOC' ? 'FOC' : ($item->workmanship == 0 ? '-' : 'RM ' . number_format($item->workmanship, 2)) }}</td>
                                    </td>
                                    <td class="total-data">{{ number_format($item->unit_price, 2) == 0 ? '-' : 'RM ' . number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="total-data">RM{{ number_format($item->total, 2) }}</td>
                                    <td>{{ $item->remark }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($pageNum === $totalPages - 1)
                            <tfoot class="totals-table">
                                <tr>
                                    <td colspan="11" class="total">Subtotal:</td>
                                    <td style="font-weight: bold !important;" class="total-data total">RM{{ number_format($subtotal, 2) }}
                                    </td>
                                    <td>PG: {{ number_format($items->sum('remark_total'), 2) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>


                </div>

                <div class="signature-section">
                    <div class="signature-box">
                        <p class="signature-line">
                            Goods Received By: {{ $self_billed_invoice->goods_received_by }}
                        </p>
                    </div>
                    <div class="signature-box">
                        <p class="signature-line">
                            Payment Received By: {{ $self_billed_invoice->payment_received_by }}
                        </p>
                    </div>
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
