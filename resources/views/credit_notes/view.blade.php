@extends('layouts.main', ['title' => 'Credit Note Items for Credit Note #' . $credit_note->credit_note_no])

@section('content')
    <div class="table-header">
        <h1 class="h3 mb-0">Credit Note Items</h1>
        <a href="{{ route('credit_notes.index') }}" class="third-button">
            <span class="material-symbols-outlined">
                arrow_back
            </span> Back
        </a>
    </div>
    <div class="card">
        <div class="table-responsive" style="padding: 24px !important; min-height: 200px; overflow-y: auto;">
            <table class="table table-hover table-bordered align-middle text-nowrap" style=" max-width: 100%; table-layout: fixed;">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" style="width: 65px;" class="text-center">No</th>
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
                    @forelse($credit_note->creditItems as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->reference_no }} <br>
                                <p style="color: var(--main-color) !important; margin-top: 8px !important;">
                                    {{ $item->custom_reference }}
                                </p>
                            </td>
                            <td class="text-wrap">
                                {{ $item->kt ? $item->kt . ' - ' : '' }}
                                {{ $item->pure_gold ? $item->pure_gold . ' - ' : '' }}
                                {{ $item->particulars }}
                            </td>
                            <td class="text-end">{{ number_format($item->quantity) == 0 ? '' : number_format($item->quantity) }} {{$item->s_pair}}</td>
                            <td class="text-end">{{ number_format($item->weight, 2) == 0 ? '' : number_format($item->weight, 2) }}</td>
                            <td class="text-end">{{ number_format($item->wastage) == 0 ? '' : number_format($item->wastage) . '%' }}</td>
                            <td class="text-end">{{ number_format($item->total_weight, 2) == 0 ? '' : number_format($item->total_weight, 2) }}</td>
                            <td>
                                @if ($item->gold == 0)
                                    <span style="display: flex; justify-content: flex-end;">
                                        {{ $item->gold === 0 ? '' : '' }}
                                    </span>
                                @else
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>RM</span>
                                        <span>{{ number_format($item->gold, 2) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($item->pure_gold == "916")
                                    <span> {{ number_format($item->total_weight, 2) }} × 0.95 =</span>
                                @elseif($item->pure_gold == "835")
                                    <span> {{ number_format($item->total_weight, 2) }} × 0.87 =</span>
                                @elseif($item->pure_gold == "750W")
                                    <span> {{ number_format($item->total_weight, 2) }} × 0.78 =</span>
                                @elseif($item->pure_gold == "750R")
                                    <span> {{ number_format($item->total_weight, 2) }} × 0.78 =</span>
                                @elseif($item->pure_gold == "750Y")
                                    <span> {{ number_format($item->total_weight, 2) }} × 0.78 =</span>
                                @elseif($item->pure_gold == "375W")
                                    <span> {{ number_format($item->total_weight, 2) }} × 0.40 =</span>
                                @elseif($item->pure_gold == "375R")
                                    <span> {{ number_format($item->total_weight, 2) }} × 0.40 =</span>
                                @endif
                                {{ $item->remark_total == 0 ? '' : $item->remark_total }}
                            </td>
                            <td>
                                @if($item->unit_price === 'FOC' || $item->unit_price == 0)
                                    <span style="display: flex; justify-content: flex-end;">
                                        {{ $item->unit_price === 'FOC' ? 'FOC' : '' }}
                                    </span>
                                @else
                                    <div style="display: flex; justify-content: space-between;">
                                            <span>RM</span>
                                            <span>{{ number_format($item->unit_price, 2) }}</span>
                                        </div>
                                    @endif
                            </td>
                            <td>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>RM</span>
                                    <span>{{ number_format($item->total, 2) }}</span>
                                </div>
                            </td>
                            <td class="text-wrap">{{ $item->remark }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">No items found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="10" class="text-end">Subtotal</th>
                        <th>
                            <div style="display: flex; justify-content: space-between;">
                                <span>RM</span>
                                <span>{{ number_format($credit_note->creditItems->first()->subtotal ?? 0, 2) }}</span>
                            </div>
                        </th>
                        <th>PG: {{ number_format($credit_note->creditItems->sum('remark_total'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
