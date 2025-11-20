@extends('layouts.main', ['title' => 'IS List'])

@section('content')
    <div class="table-header">
        <h1>IS List</h1>
        <a href="{{ route('is.create') }}" class="primary-button">Create Is</a>
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
                            <th scope="col" style="width: 3%;">IS No</th>
                            <th scope="col" style="width: 3%;">IS Date</th>
                            <th scope="col" style="width: 10%;">Company Name</th>
                            <th scope="col" style="width: 10%;">Weight</th>
                            <th scope="col" style="width: 5%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($iss as $index => $is)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $is->is_no }}</td>
                                <td>
                                    <div><i class="nav-icon fas fa-calendar text-info"></i>
                                        {{ Carbon\Carbon::parse($is->is_date)->format('d F Y') }}</div>
                                </td>
                                <td>{{ $is->company_name }}</td>
                                <td>{{ number_format($is->weight, 2) }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"

                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a href="{{ route('is.edit', $is->id) }}" class="dropdown-item"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit</a></li>
                                            <li><a href="{{ route('is.destroy', $is->id) }}"
                                                    class="dropdown-item text-danger"
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
