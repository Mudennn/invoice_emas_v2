@extends('layouts.main', ['title' => 'Balance Adjustments'])

@section('content')
    <div class="table-header">
        <h1>Gold Balance Adjustments</h1>
        <a href="{{ route('reports.balance-adjustments.create') }}" class="primary-button">
            <span class="material-symbols-outlined">add</span>
            Add Adjustment
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Effective Date</th>
                            <th class="text-end">Amount</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adjustments as $adjustment)
                            <tr>
                                <td>{{ $adjustment->company_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($adjustment->effective_date)->format('d/m/Y') }}</td>
                                <td class="text-end">{{ number_format($adjustment->adjustment_amount, 2) }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-primary">{{ ucfirst(str_replace('_', ' ', $adjustment->adjustment_type)) }}</span>
                                </td>
                                <td>{{ $adjustment->description ?: '-' }}</td>
                                <td>{{ $adjustment->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a href="{{ route('reports.balance-adjustments.edit', $adjustment->id) }}" class="dropdown-item"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit</a></li>
                    
                                            <li><form method="POST"
                                              action="{{ route('reports.balance-adjustments.destroy', $adjustment->id) }}"
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to deactivate this adjustment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" title="Deactivate"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px; border: none; background: none; width: 100%;">
                                                <span class="material-symbols-outlined" style="font-size: 14px">delete</span> Delete
                                            </button>
                                        </form></li>
                                        </ul>
                                    </div>
                                </td>
                                {{-- <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="{{ route('reports.balance-adjustments.edit', $adjustment->id) }}"
                                           class="form-secondary-button btn-sm" title="Edit">
                                            <span class="material-symbols-outlined">edit</span>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('reports.balance-adjustments.destroy', $adjustment->id) }}"
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to deactivate this adjustment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="form-danger-button btn-sm" title="Deactivate">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No balance adjustments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="{{ route('reports.is') }}" class="form-secondary-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to IS Report
                </a>
            </div>
        </div>
    </div>
@endsection