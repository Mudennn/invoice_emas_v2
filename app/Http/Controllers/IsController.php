<?php

namespace App\Http\Controllers;
use App\Http\Requests\IsFormRequest;
use App\Models\Is;
use App\Models\CustomerProfile;
use App\Models\OtherProfile;
use Illuminate\Support\Facades\Auth;


class IsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $iss = Is::where('status', '0')
		->orderBy('created_at', 'desc')
		->get();

        return view('is.index', compact('iss'));
    }

    public function view($id)
    {
        $is = Is::findOrFail($id);

        $ro = 'readonly';

        return view('is.view', compact('is', 'ro'));
    }

    public function create()
    {
        $is = new Is();
        
        // Get existing company names from both customer_profiles and others tables
        $customer_companies = CustomerProfile::select('company_name')->where('status', '0')->distinct()->pluck('company_name');
        $other_companies = OtherProfile::select('company_name')->where('status', '0')->distinct()->pluck('company_name');
        $company_names = $customer_companies->merge($other_companies)->unique()->sort()->values();
        
        $ro = '';

        return view('is.create', compact('is', 'company_names', 'ro'));
    }

    public function store(IsFormRequest $request)
    {
        $user = Auth::user();

        $is = Is::create([
            'is_no'  => $request->is_no,
            'is_date'  => $request->is_date ? \Carbon\Carbon::parse($request->is_date)->format('Y/m/d') : null,
            'company_name'  => $request->company_name,
            'weight'  => $request->weight,
            'status'  => '0',
            'created_by' => $user->id
        ]);

        return redirect()->route('is.index')
            ->with('success', 'Is created successfully.');
    }

    public function edit($id)
    {
        $is = Is::findOrFail($id);
        
        // Get existing company names from both customer_profiles and others tables
        $customer_companies = CustomerProfile::select('company_name')->where('status', '0')->distinct()->pluck('company_name');
        $other_companies = OtherProfile::select('company_name')->where('status', '0')->distinct()->pluck('company_name');
        $company_names = $customer_companies->merge($other_companies)->unique()->sort()->values();

        $ro = '';

        return view('is.edit', compact('is', 'company_names', 'ro'));
    }

    public function update(IsFormRequest $request, $id)
    {
        $user = Auth::user();
        $is = Is::findOrFail($id);

        $is->update([
            'is_no'  => $request->is_no,
            'is_date'  => $request->is_date ? \Carbon\Carbon::parse($request->is_date)->format('Y/m/d') : null,
            'company_name'  => $request->company_name,
            'weight'  => $request->weight,
            'updated_by' => $user->id
        ]);

        return redirect()->route('is.index')
            ->with('success', 'Is updated successfully.');
    }

    public function show($id)
    {
        $is = Is::findOrFail($id);

        $ro = '';

        return view('is.show', compact('is', 'ro'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $is = Is::findOrFail($id);

        $is->update([
            'updated_by' => $user->id,
            'status' => '1'
        ]);

        return redirect()->route('is.index')
            ->with('success', 'Is deleted successfully.');
    }

}
