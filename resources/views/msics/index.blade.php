@extends('layouts.dashboard', ['title' => 'MSICs'])

@section('content')
    <div style="padding: 40px;">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-2xl font-bold">MSICs</h2>
            <a href="{{ route('msics.create') }}" class="primary-button">Create</a>
        </div>
        <div class="table-responsive" style="min-height: 200px; overflow-y: auto; margin-top: 32px;">
            <table class="table table-hover table-bordered align-middle text-nowrap" id="invoiceTable">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 1%;">No</th>
                        <th scope="col" style="width: 3%;" class="text-center">Category Reference</th>
                        <th scope="col" style="width: 3%;" class="text-center">MSIC Code</th>
                        <th scope="col" style="width: 30%;">Description</th>
                        <th scope="col" style="width: 5%;" class="text-center">Actions</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($msics as $index => $msic)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $msic->category_reference }}</td>
                            <td class="text-center">{{ $msic->msic_code }}</td>
                            <td>{{ $msic->description }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ph ph-dots-three-vertical" style="font-size: 18px; color: #646e78;"></i>
                                    </button>
                                    <ul class="dropdown-menu w-50" aria-labelledby="dropdownMenuButton1">
                                        <li><a href="{{ route('msics.edit', $msic->id) }}" class="dropdown-item"
                                                href="#"
                                                style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><i
                                                    class="ph ph-pencil" style="font-size: 16px"></i> Edit</a></li>
                                        <li><a href="{{ route('msics.destroy', $msic->id) }}"
                                                class="dropdown-item text-danger" href="#"
                                                style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><i
                                                    class="ph ph-trash" style="font-size: 16px"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
