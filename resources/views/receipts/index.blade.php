@extends('layouts.main', ['title' => 'Receipts'])

@section('content')
    <div class="table-header">
        <h1>Receipts</h1>
        <a href="{{ route('receipts.create') }}" class="primary-button">Create Receipt</a>
    </div>

    @if(session('success'))
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
                            <th scope="col" class="text-center" style="width: 1%;">No</th>
                            <th scope="col" style="width: 3%;">Receipt No</th>
                            <th scope="col" style="width: 3%;">Receipt Date</th>
                            <th scope="col" style="width: 10%;">Receipt Image</th>
                            <th scope="col" style="width: 10%;">Receipt Notes</th>
                            <th scope="col" style="width: 5%;" class="text-center">Actions</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receipts as $index => $receipt)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $receipt->receipt_no }}</td>
                                <td>
                                    <div><i class="nav-icon fas fa-calendar text-info"></i>
                                        {{ Carbon\Carbon::parse($receipt->receipt_date)->format('d F Y') }}</div>
                                </td>
                                <td>
                                    @if ($receipt->getFirstMediaUrl('receipt_image'))
                                        <img src="{{ $receipt->getFirstMediaUrl('receipt_image') }}" alt="Receipt Image"
                                            style="width: 100px; height: auto;" data-bs-toggle="modal"
                                            data-bs-target="#imageModal{{ $receipt->id }}">

                                        <!-- Image Modal -->
                                        <div class="modal fade" id="imageModal{{ $receipt->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-md modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Receipt Image</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ $receipt->getFirstMediaUrl('receipt_image') }}"
                                                            alt="Receipt Image" style="max-width: 100%; height: auto;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p>No image available</p>
                                    @endif
                                </td>

                                <td class="text-wrap">{{ $receipt->receipt_note }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"

                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a href="{{ route('receipts.edit', $receipt->id) }}" class="dropdown-item"
                                                    href="#"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit</a></li>
                                            <li><a href="{{ route('receipts.destroy', $receipt->id) }}"
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
