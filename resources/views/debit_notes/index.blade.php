@extends('layouts.main', ['title' => 'Debit Notes'])

@section('content')
    <div class="table-header">
        <h1 class="h3 mb-0">Debit Notes</h1>
        <a href="{{ route('debit_notes.create') }}" class="primary-button">Create Debit Note</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="card">
        <div class="card-body">
        <div class="table-responsive " style="min-height: 200px; overflow-y: auto;">
            <table class="table table-hover table-bordered align-middle text-nowrap" id="invoiceTable">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 5%;" class="text-center">No</th>
                        <th style="width: 5%;">Debit Note No</th>
                        <th style="width: 5%;">Invoice No</th>
                        <th style="width: 5%;">Date</th>
                        <th style="width: 20%;">Company Name</th>
                        <th style="width: 5%;" class="text-end">Subtotal</th>
                        <th style="width: 10%;">Note</th>
                        <th style="width: 5%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($debit_notes as $index => $debit_note)
                        <tr>
                            <td class="text-center">{{$index + 1}}</td>
                            <td>{{ $debit_note->debit_note_no }}</td>
                            <td>{{ $debit_note->invoice_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($debit_note->date)->format('d F Y') }}</td>
                            <td class="text-wrap"> {{ $debit_note->invoice->company_name ?? 'N/A' }}</td>
                            <td>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>RM</span>
                                    <span>{{ number_format($debit_note->debitItems->first()->subtotal ?? 0, 2) }}</span>
                                </div>
                            </td>
                            <td class="text-wrap">{{ $debit_note->note }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                            more_vert
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><a href="{{ route('debit_notes.edit', $debit_note->id) }}"
                                                class="dropdown-item" href="#"
                                                style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                    class="material-symbols-outlined" style="font-size: 14px">
                                                    edit
                                                </span> Edit</a></li>
                                        <li><a href="{{ route('debit_notes.view', $debit_note->id) }}"
                                                class="dropdown-item"
                                                style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                    class="material-symbols-outlined" style="font-size: 14px">
                                                    list_alt
                                                </span> View Items</a></li>
                                        <li><a href="{{ route('debit_notes.destroy', $debit_note->id) }}"
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
