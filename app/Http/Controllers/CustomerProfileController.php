<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerProfileFormRequest;
use App\Models\CustomerProfile;
use App\Models\Selection;
use App\Models\OtherProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get customer profiles
        $customer_profiles = CustomerProfile::select(
            'customer_profiles.id',
            'customer_profiles.company_name',
            'customer_profiles.address_line_1',
            'customer_profiles.address_line_2',
            'customer_profiles.city',
            'customer_profiles.postcode',
            'customer_profiles.state',
            'customer_profiles.contact_name_1',
            'customer_profiles.contact_1',
            'customer_profiles.email_1',
            'customer_profiles.contact_name_2',
            'customer_profiles.contact_2',
            'customer_profiles.email_2',
            'customer_profiles.contact_name_3',
            'customer_profiles.contact_3',
            'customer_profiles.email_3',
            'customer_profiles.tin',
            'customer_profiles.sst_registration_no',
            'customer_profiles.business_registration_no',
            'customer_profiles.identification_number',
            'customer_profiles.passport_number',
            'state.selection_data as s_state',
            DB::raw("'Main Client' as profile_type")
        )
            ->leftJoin('selections as state', 'customer_profiles.state', '=', 'state.id')
            ->where('customer_profiles.status', '0');

        // Get other profiles
        $other_profiles = OtherProfile::select(
            'others.id',
            'others.company_name',
            'others.address_line_1',
            'others.address_line_2',
            'others.city',
            'others.postcode',
            'others.state',
            'others.contact_name_1',
            'others.contact_1',
            'others.email_1',
            'others.contact_name_2',
            'others.contact_2',
            'others.email_2',
            'others.contact_name_3',
            'others.contact_3',
            'others.email_3',
            'others.tin',
            'others.sst_registration_no',
            'others.business_registration_no',
            'others.identification_number',
            'others.passport_number',
            'state.selection_data as s_state',
            DB::raw("'other' as profile_type")
        )
            ->leftJoin('selections as state', 'others.state', '=', 'state.id')
            ->where('others.status', '0');

        // Combine both queries
        $all_profiles = $customer_profiles->union($other_profiles)->get();

        return view('customer_profiles.index', compact('all_profiles'))->with('success', 'Profile created successfully');
    }

    public function view($id)
    {
        $customer_profile = CustomerProfile::findOrFail($id);
        $states = Selection::select('id', 'selection_data')->where('selection_type', 'state')->where('status', '0')->get();

        $ro = 'readonly';

        return view('customer_profiles.view', compact('customer_profile', 'states', 'ro'));
    }

    public function create()
    {
        $customer_profile = new CustomerProfile();
        $states = Selection::select('id', 'selection_data')->where('selection_type', 'state')->where('status', '0')->get();
        
        // Get existing company names from both customer_profiles and others tables
        $customer_companies = CustomerProfile::select('company_name')->where('status', '0')->distinct()->pluck('company_name');
        $other_companies = OtherProfile::select('company_name')->where('status', '0')->distinct()->pluck('company_name');
        $company_names = $customer_companies->merge($other_companies)->unique()->sort()->values();

        $ro = '';

        return view('customer_profiles.create', compact('customer_profile', 'states', 'company_names', 'ro'));
    }

    public function store(CustomerProfileFormRequest $request)
    {
        $user = Auth::user();

        $customer_profile = CustomerProfile::create([
            'company_name'  => $request->company_name,
            'contact_name_1'  => $request->contact_name_1,
            'contact_1'  => $request->contact_1,
            'email_1'  => $request->email_1,
            'contact_name_2'  => $request->contact_name_2,
            'contact_2'  => $request->contact_2,
            'email_2'  => $request->email_2,
            'contact_name_3'  => $request->contact_name_3,
            'contact_3'  => $request->contact_3,
            'email_3'  => $request->email_3,
            'address_line_1'  => $request->address_line_1,
            'address_line_2'  => $request->address_line_2,
            'state'  => $request->state,
            'city'  => $request->city,
            'postcode'  => $request->postcode,
            'tin'  => $request->tin,
            'sst_registration_no'  => $request->sst_registration_no,
            'business_registration_no'  => $request->business_registration_no,
            'identification_number'  => $request->identification_number,
            'passport_number'  => $request->passport_number,
            'created_by' => $user->id,
            'status' => '0'
        ]);


        return redirect()->route('customer_profiles.index')->with('success', 'Profile created successfully');
    }

    public function edit($id)
    {
        $customer_profile = CustomerProfile::findOrFail($id);
        $states = Selection::select('id', 'selection_data')->where('selection_type', 'state')->where('status', '0')->get();
        
        // Get existing company names from both customer_profiles and others tables
        $customer_companies = CustomerProfile::select('company_name')->where('status', '0')->distinct()->pluck('company_name');
        $other_companies = OtherProfile::select('company_name')->where('status', '0')->distinct()->pluck('company_name');
        $company_names = $customer_companies->merge($other_companies)->unique()->sort()->values();
        
        $ro = '';

        return view('customer_profiles.edit', compact('customer_profile', 'states', 'company_names', 'ro'));
    }

    public function update(CustomerProfileFormRequest $request, $id)
    {
        $user = Auth::user();

        $customer_profile = CustomerProfile::where('id', $id)
            ->update([
                'company_name'  => $request->company_name,
                'contact_name_1'  => $request->contact_name_1,
                'contact_1'  => $request->contact_1,
                'email_1'  => $request->email_1,
                'contact_name_2'  => $request->contact_name_2,
                'contact_2'  => $request->contact_2,
                'email_2'  => $request->email_2,
                'contact_name_3'  => $request->contact_name_3,
                'contact_3'  => $request->contact_3,
                'email_3'  => $request->email_3,
                'address_line_1'  => $request->address_line_1,
                'address_line_2'  => $request->address_line_2,
                'state'  => $request->state,
                'city'  => $request->city,
                'postcode'  => $request->postcode,
                'tin'  => $request->tin,
                'sst_registration_no'  => $request->sst_registration_no,
                'business_registration_no'  => $request->business_registration_no,
                'identification_number'  => $request->identification_number,
                'passport_number'  => $request->passport_number,
                'updated_by' => $user->id
            ]);


        return redirect()->route('customer_profiles.index')->with('success', 'Profile updated successfully');
    }

    public function show($id)
    {
        $customer_profile = CustomerProfile::findOrFail($id);
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();

        $ro = '';

        return view('customer_profiles.show', compact('customer_profile', 'states', 'ro'));
    }

    public function destroy($id)
    {
        $user = Auth::user();

        CustomerProfile::where('id', $id)
            ->update([
                'updated_by' => $user->id,
                'status' => '1'
            ]);

        return redirect()->route('customer_profiles.index');
    }
}
