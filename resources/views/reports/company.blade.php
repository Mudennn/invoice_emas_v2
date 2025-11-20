@extends('layouts.main', ['title' => 'Company Sales Report'])

@section('content')
    <div class="table-header">
        <h1>Company Sales Report</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reports.company.sales') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="company">Company</label>
                            <select name="company" id="company" class="form-control select2" required>
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company }}"
                                        {{ request('company') == $company ? 'selected' : '' }}>
                                        {{ $company }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="primary-button">Generate Report</button>
                                @if (isset($salesReport) && $salesReport->isNotEmpty())
                                    <button type="submit" formaction="{{ route('reports.company.print') }}"
                                        formtarget="_blank" class="form-secondary-button">
                                        <span class="material-symbols-outlined">print</span>
                                        Print Report
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @if (isset($salesReport))
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Document No</th>
                                <th>Date</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">Payment</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesReport as $report)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $report->document_no }}</td>
                                    <td>{{ \Carbon\Carbon::parse($report->document_date)->format('d-m-Y') }}</td>
                                    <td class="text-end">
                                        @if ($report->document_type == 'Credit Note')
                                            <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                                <span>RM</span>
                                                <span>{{ number_format($report->amount ?? 0, 2) }}</span>
                                            </div>
                                        @else
                                            <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                                <span>RM</span>
                                                <span>{{ number_format($report->subtotal ?? 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                           {{ number_format($report->total_payment ?? 0, 2) }}
                                        </div>
                                    </td>
                                    <td class="text-end {{ ($report->balance ?? 0) > 0 ? 'text-danger' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            @if(($report->balance ?? 0) < 0)
                                                <span>-RM</span>
                                                <span>{{ number_format(abs($report->balance ?? 0), 2) }}</span>
                                            @else
                                                <span>RM</span>
                                                <span>{{ number_format($report->balance ?? 0, 2) }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- <td class="text-end">
                                        RM{{ number_format($report->total_payment ?? 0, 2) }}
                                    </td>
                                    <td class="text-end {{ ($report->balance ?? 0) > 0 ? 'text-danger' : '' }}">
                                        RM{{ number_format($report->balance ?? 0, 2) }}
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No records found for the selected date range.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($salesReport->isNotEmpty())
                            <tfoot>
                                <tr style="font-weight: bold !important;">
                                    <td colspan="3" class="text-end">Total</td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                           {{ number_format($salesReport->sum('subtotal'), 2) }}
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                           {{ number_format($salesReport->sum('total_payment'), 2) }}
                                        </div>
                                    </td>
                                    <td class="text-end {{ $salesReport->sum('balance') > 0 ? 'text-danger' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                           {{ number_format($salesReport->sum('balance'), 2) }}
                                        </div>
                                    </td>
                                    {{-- <td class="total">RM{{ number_format($salesReport->sum('subtotal'), 2) }}</td> --}}
                                    {{-- <td class="text-end">RM{{ number_format($salesReport->sum('total_payment'), 2) }}</td>
                                    <td class="text-end {{ $salesReport->sum('balance') > 0 ? 'text-danger' : '' }}"
                                        style="font-weight: bold !important;">
                                        RM{{ number_format($salesReport->sum('balance'), 2) }}</td> --}}
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize select2
                $('.select2').select2({
                    placeholder: 'Select Company',
                    allowClear: true,
                    width: '100%'
                });

                // Set min date on end_date based on start_date selection
                document.getElementById('start_date').addEventListener('change', function() {
                    document.getElementById('end_date').min = this.value;
                });

                // Set max date on start_date based on end_date selection
                document.getElementById('end_date').addEventListener('change', function() {
                    document.getElementById('start_date').max = this.value;
                });
            });
        </script>
    @endpush
@endsection
