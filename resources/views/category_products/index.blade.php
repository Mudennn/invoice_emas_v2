@extends('layouts.main', ['title' => 'Product Categories'])

@section('content')
    <div class="table-header">
        <h1>Product Categories</h1>
        <a href="{{ route('category_products.create') }}" class="primary-button">Create Category</a>
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
                            <th style="width: 5%;" class="text-center">No</th>
                            <th style="width: 80%;">Name</th>
                            <th style="width: 5%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($category_products as $index => $category_product)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-wrap">{{ $category_product->name }}</td>



                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                                more_vert
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a href="{{ route('category_products.edit', $category_product->id) }}"
                                                    class="dropdown-item" href="#"
                                                    style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;"><span
                                                        class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit</a></li>
                                            <li><a href="{{ route('category_products.destroy', $category_product->id) }}"
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
