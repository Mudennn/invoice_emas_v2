@extends('layouts.main', ['title' => 'Sales Report'])

@section('content')
    <div class="table-header">
        <h1>Sales Report</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reports.sales') }}" method="GET" class="mb-4">
                <div class="row">
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
                            <label for="company-filter">Companies to Show</label>
                            <div class="dropdown" id="company-filter-dropdown">
                                <button class="form-control text-start dropdown-toggle" type="button" id="company-filter"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="selected-companies-text">All Companies</span>
                                </button>
                                <div class="dropdown-menu p-3" aria-labelledby="company-filter" style="min-width: 300px;" onclick="event.stopPropagation()">
                                    <div class="mb-2">
                                        <label class="d-flex align-items-center" style="cursor: pointer;">
                                            <input type="checkbox" id="select-all-companies" class="me-2" checked>
                                            <strong>Select All</strong>
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    @foreach ((isset($allCustomerCompanies) ? $allCustomerCompanies : $customerCompanies) as $company)
                                        <div class="mb-1">
                                            <label class="d-flex align-items-center" style="cursor: pointer;">
                                                <input type="checkbox" name="companies[]" value="{{ $company }}"
                                                    class="company-checkbox me-2"
                                                    {{ in_array($company, request('companies', [])) || empty(request('companies')) ? 'checked' : '' }}>
                                                {{ $company }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="primary-button">Generate Report</button>
                                @if (isset($salesReport) && $salesReport->isNotEmpty())
                                    <button type="submit" formaction="{{ route('reports.print') }}" formtarget="_blank"
                                        class="form-secondary-button">
                                        <span class="material-symbols-outlined">
                                            print
                                        </span>
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
                                <th>Invoice</th>
                                <th class="text-end" style="width: 150px !important; min-width: 150px;">Amount</th>
                                <th class="text-end" style="width: 150px !important; min-width: 150px;">Gold Price</th>
                                <th class="text-end" style="width: 150px !important; min-width: 150px;">Pure Gold</th>
                                {{-- <th class="text-end">Workmanship</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Unit Price</th> --}}
                                @foreach ($customerCompanies as $company)
                                    <th class="text-end" style="width: 150px !important; min-width: 150px;">
                                        {{ $company }}</th>
                                @endforeach
                                <th class="text-end" style="width: 150px !important; min-width: 150px;">Others</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesReport as $report)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $report->invoice_no }}</td>

                                    <td class="text-end">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                            <span>{{ number_format($report->amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($report->gold_price, 2) == 0 ? '-' : '' . number_format($report->gold_price, 2) }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($report->remark_total, 2) == 0 ? '-' : number_format($report->remark_total, 2) }}
                                    </td>
                                    {{-- <td class="text-end">RM{{ number_format($report->workmanship, 2) }}</td>
                                    <td class="text-end">{{ number_format($report->quantity, 2) == 0 ? '-' : number_format($report->quantity, 2) }}</td>
                                    <td class="text-end">{{ number_format($report->unit_price, 2) == 0 ? '-' : 'RM' . number_format($report->unit_price, 2) }}</td> --}}
                                    @foreach ($customerCompanies as $company)
                                        @php
                                            $columnName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($company));
                                            $columnName = rtrim($columnName, '_');
                                        @endphp
                                        <td class="text-end">
                                            <div class="d-flex justify-content-between align-items-center"
                                                style="width: 100%;">
                                                <span>RM</span>
                                                <span>{{ $report->$columnName != 0 ? '' . number_format($report->$columnName, 2) : '-' }}</span>
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="text-end">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                            <span>{{ $report->others != 0 ? '' . number_format($report->others, 2) : '-' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 7 + count($customerCompanies) }}" class="text-center">
                                        No records found for the selected date range.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($salesReport->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td class="text-end" style="font-weight: bold !important;">Total</td>
                                    <td class="text-end" style="font-weight: bold !important;">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                            <span>{{ number_format($salesReport->sum('amount'), 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end" style="font-weight: bold !important;">
                                        {{ number_format($salesReport->sum('gold_price'), 2) }}
                                    </td>
                                    <td class="text-end" style="font-weight: bold !important;">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>PG</span>
                                            <span>{{ number_format($salesReport->sum('remark_total'), 2) }}</span>
                                        </div>
                                    </td>
                                    {{-- <td class="text-end" style="font-weight: bold !important;">
                                        RM{{ number_format($salesReport->sum('workmanship'), 2) }}</td>
                                    <td class="text-end" style="font-weight: bold !important;">
                                        {{ number_format($salesReport->sum('quantity'), 2) }}</td>
                                    <td class="text-end" style="font-weight: bold !important;">
                                        RM{{ number_format($salesReport->sum('unit_price'), 2) }}</td> --}}
                                    @foreach ($customerCompanies as $company)
                                        @php
                                            $columnName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($company));
                                            $columnName = rtrim($columnName, '_');
                                        @endphp
                                        <td class="text-end" style="font-weight: bold !important;">
                                            <div class="d-flex justify-content-between align-items-center"
                                                style="width: 100%;">
                                                <span>RM</span>
                                                <span>{{ number_format($salesReport->sum($columnName), 2) }}</span>
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="text-end" style="font-weight: bold !important;">
                                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                            <span>RM</span>
                                            <span>{{ number_format($salesReport->sum('others'), 2) }}</span>
                                        </div>
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
                // Set min date on end_date based on start_date selection
                document.getElementById('start_date').addEventListener('change', function() {
                    document.getElementById('end_date').min = this.value;
                });

                // Set max date on start_date based on end_date selection
                document.getElementById('end_date').addEventListener('change', function() {
                    document.getElementById('start_date').max = this.value;
                });

                // Company filter checkbox functionality
                const selectAllCheckbox = document.getElementById('select-all-companies');
                const companyCheckboxes = document.querySelectorAll('.company-checkbox');
                const selectedCompaniesText = document.getElementById('selected-companies-text');

                // Update the display text based on selected companies
                function updateSelectedText() {
                    const checkedBoxes = document.querySelectorAll('.company-checkbox:checked');
                    const totalBoxes = companyCheckboxes.length;

                    if (checkedBoxes.length === 0) {
                        selectedCompaniesText.textContent = 'None Selected';
                    } else if (checkedBoxes.length === totalBoxes) {
                        selectedCompaniesText.textContent = 'All Companies';
                        selectAllCheckbox.checked = true;
                    } else {
                        selectedCompaniesText.textContent = `${checkedBoxes.length} of ${totalBoxes} companies`;
                        selectAllCheckbox.checked = false;
                    }
                }

                // Select/Deselect all functionality
                selectAllCheckbox.addEventListener('change', function() {
                    companyCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectedText();
                });

                // Individual checkbox change
                companyCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateSelectedText();
                    });
                });

                // Initialize the text on page load
                updateSelectedText();

                // Prevent dropdown from closing when clicking inside
                document.querySelector('#company-filter-dropdown .dropdown-menu').addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        </script>
    @endpush
@endsection
