
@extends('layouts.main')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header form-inline"> 
                    <div class="col-6 font-weight-bold pl-0"><i class="nav-icon  fas fa-file fa-2x text-secondary pr-3"></i>
                        <span class="header-title">INVOICES</span> 
                    </div>
                   
                    <div class="header-process col-6" style="text-align:right; float:right">VIEW</div>
                </div> 

                <div class="card-body">
                    <div class="d-flex">
                        <div class="col-md-6 mt-3 d-inline-block">
                        
                            @include('invoices.form')

                            <hr class="mt-3 mb-3">

                            <div class="form-group row mb-0">
                                <div class="col-md-12 ml-1" style="text-align: right">
                                    <a href="{{ route('invoices.index') }}" class="btn btn-xs btn-outline-secondary pull-right"><i class="nav-icon fas fa-arrow-left"></i> Back</a>
                                </div>
                            </div>
							
				        </div>
                        <div class="col-md-6 mt-3 d-inline-block"></div>	
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

