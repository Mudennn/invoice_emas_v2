@extends('layouts.main', ['title' => 'IS Report'])

@section('content')
    <div class="table-header">
        <h1>Pure Gold Account Report</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reports.is.generate') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="company">Company</label>
                            <select name="company" id="company" class="form-control select2" required>
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    @if (str_contains($company, 'Habib Jewelry Manufacturing'))
                                        <option value="{{ $company }}"
                                            {{ request('company') == $company ? 'selected' : '' }}>
                                            {{ $company }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="year">Year</label>
                            <select name="year" id="year" class="form-control" required>
                                <option value="">Select Year</option>
                                @for ($i = 2025; $i <= date('Y') ; $i++)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="month">Month</label>
                            <select name="month" id="month" class="form-control" required>
                                <option value="">Select Month</option>
                                @php
                                    $months = [
                                        1 => 'January',
                                        2 => 'February',
                                        3 => 'March',
                                        4 => 'April',
                                        5 => 'May',
                                        6 => 'June',
                                        7 => 'July',
                                        8 => 'August',
                                        9 => 'September',
                                        10 => 'October',
                                        11 => 'November',
                                        12 => 'December',
                                    ];
                                @endphp
                                @foreach ($months as $num => $name)
                                    <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="primary-button">Generate Report</button>
                                @if (isset($isReport) && $isReport->isNotEmpty())
                                    <button type="submit" formaction="{{ route('reports.is.print') }}" formtarget="_blank"
                                        class="form-secondary-button">
                                        <span class="material-symbols-outlined">print</span>
                                        Print Report
                                    </button>
                                @endif
                                <a href="{{ route('reports.balance-adjustments') }}" class="form-secondary-button">
                                    <span class="material-symbols-outlined">settings</span>
                                    Manage Balance
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @if (isset($isReport))
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Documents</th>
                                <th class="text-end">IN</th>
                                <th class="text-end">OUT</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($isReport as $report)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($report->document_date)->format('d/m/Y') }}</td>
                                    <td>{{ $report->document_no }}</td>
                                    <td class="text-end">
                                        @if ($report->in_weight > 0)
                                            {{ number_format($report->in_weight, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($report->out_weight > 0)
                                            {{ number_format($report->out_weight, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-end {{ $report->balance < 0 ? 'text-danger' : '' }}">
                                        {{ number_format($report->balance, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No records found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (isset($isReport) && $isReport->isNotEmpty())
                            <tfoot>
                                <tr style="font-weight: bold !important;">
                                    <td colspan="2" class="text-end">Total</td>
                                    <td class="text-end">{{ number_format($isReport->sum('in_weight'), 2) }}</td>
                                    <td class="text-end">{{ number_format($isReport->sum('out_weight'), 2) }}</td>
                                    <td class="text-end {{ $isReport->last()->balance < 0 ? 'text-danger' : '' }}"
                                        style="font-weight: bold !important;">
                                        {{ number_format($isReport->last()->balance ?? 0, 2) }}
                                    </td>
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
            });
        </script>
    @endpush
@endsection
