@extends('layouts.main')

@section('content')
    <div class="table-header">
        <h1>Gold Price</h1>
        <a href="{{ route('gold_prices.create') }}" class="primary-button">Create Gold Price</a>
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
                            <th scope="col" class="text-center" style="width: 5%;">No</th>
                            <th scope="col" style="width: 70%;">Gold Type</th>
                            <th scope="col" style="width: 10%;">Price (RM)</th>
                            <th scope="col" style="width: 5%;">Date Change</th>
                            <th scope="col" style="width: 5%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gold_prices as $index => $gold_pric)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-wrap">{{ $gold_pric->name }}</td>
                                <td>{{ $gold_pric->price }}</td>
                                <td>{{ Carbon\Carbon::parse($gold_pric->date_change)->format('d-m-Y') }}</td>



                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a href="{{ route('gold_prices.edit', $gold_pric->id) }}"
                                                    class="dropdown-item" href="#"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit</a></li>
                                            <li><a href="{{ route('gold_prices.destroy', $gold_pric->id) }}"
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
