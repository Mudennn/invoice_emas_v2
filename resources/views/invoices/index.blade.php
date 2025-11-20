@extends('layouts.main', ['title' => 'Invoices'])

@section('content')
    <div class="table-header">
        <h1>Invoices</h1>
        <a href="{{ route('invoices.create') }}" class="primary-button">Create Invoice</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="card">
        <div class="card-body">
            <div class="table-responsive" style="min-height: 200px; overflow-y: auto;">
                <table class="table table-hover table-bordered align-middle text-nowrap" id="invoiceTable">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 5%;">No</th>
                            <th scope="col" style="width: 10%;">Invoice No</th>
                            <th scope="col" style="width: 40%;">Company Name</th>
                            <th scope="col" style="width: 10%;">Invoice Date</th>
                            <th scope="col" style="width: 10%;">Goods Received By</th>
                            <th scope="col" style="width: 10%;">Payment Received By</th>
                            <th scope="col" style="width: 5%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $index => $invoice)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $invoice->invoice_no }}</td>
                                <td class="text-wrap">{{ $invoice->company_name }}</td>
                                <td>
                                    <div><i class="nav-icon fas fa-calendar text-info"></i>

                                        {{ Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') }}</div>
                                </td>
                                <td>{{ $invoice->goods_received_by }}</td>
                                <td>{{ $invoice->payment_received_by }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a href="{{ route('invoices.edit', $invoice->id) }}" class="dropdown-item"
                                                    href="#"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit</a></li>
                                            <li><a href="{{ route('invoices.payments', $invoice->id) }}"
                                                    class="dropdown-item"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        payments
                                                    </span> View Payments</a></li>
                                            <li><a href="{{ route('invoice_items.index', ['invoice_id' => $invoice->id]) }}"
                                                    class="dropdown-item"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        list_alt
                                                    </span> View Items</a></li>
                                            <li><a href="{{ route('invoices.print', $invoice->id) }}" class="dropdown-item"
                                                    target="_blank"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        print
                                                    </span> Print Invoice</a></li>
                                            <li><a href="{{ route('invoices.destroy', $invoice->id) }}"
                                                    class="dropdown-item text-danger" href="#"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        delete
                                                    </span>Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
