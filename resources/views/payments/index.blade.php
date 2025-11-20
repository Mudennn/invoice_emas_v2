@extends('layouts.main', ['title' => 'Payments'])

@section('content')
    <div class="table-header">
        <h1>Payments</h1>
        <a href="{{ route('payments.create') }}" class="primary-button">Create Payment</a>
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
                            <th scope="col" class="text-center" style="width: 2%;">No</th>
                            <th scope="col" style="width: 15%;">Invoice No</th>
                            <th scope="col" style="width: 5%;">Payment Date</th>
                            <th scope="col" style="width: 5%;" >Payment Voucher</th>
                            <th scope="col" style="width: 5%;" class="text-end">Total Payment</th>
                            <th scope="col" style="width: 5%;" class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($payments as $index => $payment)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $payment->invoice->invoice_no }}</td>
                                <td>
                                    <div><i class="nav-icon fas fa-calendar text-info"></i>
                                        {{ Carbon\Carbon::parse($payment->payment_date)->format('d F Y') }}</div>
                                </td>
                                <td>{{ $payment->payment_voucher }}</td>
                                <td class="d-flex justify-content-between"><span>RM</span><span>{{ number_format($payment->total_payment, 2) }}</span></td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a href="{{ route('payments.edit', $payment->id) }}" class="dropdown-item"
                                                    href="#"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit</a></li>
                                            <li><a href="{{ route('payments.destroy', $payment->id) }}"
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
