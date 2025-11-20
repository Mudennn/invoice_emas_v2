@extends('layouts.main', ['title' => 'Invoice Items' . ($invoice_number ? ' for Invoice #' . $invoice_number : '')])

@section('content')
    <div class="table-header">
        <h1>Invoice Items</h1>
        <a href="{{ route('invoices.index') }}" class="third-button">
            <span class="material-symbols-outlined">
                arrow_back
            </span> Back
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="card">
        <div class="card-body">
            <div class="table-responsive" style="min-height: 200px; overflow-x: auto !important;">
                <table class="table table-hover table-bordered align-middle text-nowrap" style=" max-width: 100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" style="width: 65px;">No</th>
                            <th scope="col" style="width: 150px;">Reference No</th>
                            <th scope="col" style="width: 200px;">Particulars</th>
                            <th scope="col" class="text-end" style="width: 120px;">Quantity</th>
                            <th scope="col" class="text-end" style="width: 120px;">Weight</th>
                            <th scope="col" class="text-end" style="width: 120px;">Wastage</th>
                            <th scope="col" class="text-end" style="width: 150px;">Total Weight</th>
                            <th scope="col" class="text-end" style="width: 130px;">Gold Price</th>
                            <th scope="col" class="text-end" style="width: 220px;">Pure Gold</th>
                            <th scope="col" class="text-end" style="width: 140px;">Unit Price</th>
                            <th scope="col" class="text-end" style="width: 200px;">Total</th>
                            <th scope="col" style="width: 250px;">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice_items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->reference_no }} <br>
                                    <p style="color: var(--main-color) !important; margin-top: 8px !important;">
                                        {{ $item->custom_reference }}
                                    </p>
                                </td>
                                <td>
                                    {{ $item->kt ? $item->kt . ' - ' : '' }}
                                    {{ $item->pure_gold ? $item->pure_gold . ' - ' : '' }}
                                    {{ $item->particulars }}
                                </td>
                                <td class="text-end">{{ number_format($item->quantity) == 0 ? '' : number_format($item->quantity) }} {{$item->s_pair}}</td>
                                <td class="text-end">{{ number_format($item->weight, 2) == 0 ? '' : number_format($item->weight, 2) }}</td>
                                <td class="text-end">{{ number_format($item->wastage) == 0 ? '' : number_format($item->wastage) . '%' }}</td>
                                <td class="text-end">{{ number_format($item->total_weight, 2) == 0 ? '' : number_format($item->total_weight, 2) }}</td>
                                <td class="text-end"><div class="d-flex justify-content-between"><span>RM</span><span>{{ number_format($item->gold, 2) }}</span></div></td>
                                
                                <td class="text-end">
                                    @if($item->pure_gold == '916')
                                        <span> {{ number_format($item->total_weight, 2) }} × 0.95 =</span>
                                    @elseif($item->pure_gold == '835')
                                        <span> {{ number_format($item->total_weight, 2) }} × 0.87 =</span>
                                    @elseif($item->pure_gold == '750W')
                                        <span> {{ number_format($item->total_weight, 2) }} × 0.78 =</span>
                                    @elseif($item->pure_gold == '750R')
                                        <span> {{ number_format($item->total_weight, 2) }} × 0.78 =</span>
                                    @elseif($item->pure_gold == '750Y')
                                        <span> {{ number_format($item->total_weight, 2) }} × 0.78 =</span>
                                    @elseif($item->pure_gold == '375W')
                                        <span> {{ number_format($item->total_weight, 2) }} × 0.40 =</span>
                                    @elseif($item->pure_gold == '375R')
                                        <span> {{ number_format($item->total_weight, 2) }} × 0.40 =</span>
                                    @endif
                                    {{ $item->remark_total == 0 ? '' : $item->remark_total }}
                                </td>
                                <td>
                                    @if($item->unit_price === 'FOC' || $item->unit_price == 0)
                                        <span class="d-flex justify-content-end">
                                            {{ $item->unit_price === 'FOC' ? 'FOC' : '' }}
                                        </span>
                                    @else
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                            <span>{{ number_format($item->unit_price, 2) }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td><div class="d-flex justify-content-between"><span>RM</span><span>{{ number_format($item->total, 2) }}</span></div></td>
                                <td class="text-wrap">{{ $item->remark }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $subtotal = $invoice_items->sum('total');
                        @endphp
                        <tr>
                            <th colspan="10" class="text-end">Subtotal</th>
                            <th>
                                <div class="d-flex justify-content-between"><span>RM</span><span>{{ number_format($subtotal, 2) }}</span></div>
                            </th>
                            <th>PG: {{ number_format($invoice_items->sum('remark_total'), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
