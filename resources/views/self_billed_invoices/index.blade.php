@extends('layouts.main', ['title' => 'Self Billed Invoices'])

@section('content')
    <div class="table-header">
        <h1>Self Billed Invoices</h1>
        <a href="{{ route('self_billed_invoices.create') }}" class="primary-button">Create Self Billed Invoice</a>
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
                            <th scope="col" style="width: 10%;">Self Billed Invoice No</th>
                            <th scope="col" style="width: 40%;">Company Name</th>
                            <th scope="col" style="width: 10%;">Invoice Date</th>
                            <th scope="col" style="width: 10%;">Goods Received By</th>
                            <th scope="col" style="width: 10%;">Payment Received By</th>
                            <th scope="col" style="width: 5%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($self_billed_invoices as $index => $self_billed_invoice)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $self_billed_invoice->self_billed_invoice_no }}</td>
                                <td class="text-wrap">{{ $self_billed_invoice->company_name }}</td>
                                <td>
                                    <div><i class="nav-icon fas fa-calendar text-info"></i>
                                        {{ Carbon\Carbon::parse($self_billed_invoice->invoice_date)->format('d F Y') }}</div>
                                </td>
                                <td>{{ $self_billed_invoice->goods_received_by }}</td>
                                <td>{{ $self_billed_invoice->payment_received_by }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a href="{{ route('self_billed_invoices.edit', $self_billed_invoice->id) }}" class="dropdown-item"
                                                    href="#"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit</a></li>
                                            <li><a href="{{ route('self_billed_invoices.view', ['id' => $self_billed_invoice->id]) }}"
                                                    class="dropdown-item"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        list_alt
                                                    </span> View Items</a></li>
                                            <li><a href="{{ route('self_billed_invoices.destroy', $self_billed_invoice->id) }}"
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
