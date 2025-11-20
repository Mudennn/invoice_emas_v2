<?php

namespace App\Http\Controllers;
use App\Http\Requests\CompanyProfileFormRequest;
use App\Models\CompanyProfile;
use App\Models\Selection;
use App\Models\Msic;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $company_profiles = CompanyProfile::select(
			'company_profiles.*'
			, 'state.selection_data as s_state' 
		)
		->leftJoin('selections as state', 'company_profiles.state', '=', 'state.id')
		->where('company_profiles.status', '0')
		->get();
	
        return view('company_profiles.index', compact('company_profiles'));
    }
    
    public function view($id)
    {
        $company_profile = CompanyProfile::findOrFail($id);
    	$states = Selection::select('id', 'selection_data')->where('selection_type', 'state')->where('status', '0')->get();
        $msics = Msic::select('id', 'msic_code', 'description')->where('status', '0')->orderBy('msic_code')->get();

        $ro = 'readonly';

        return view('company_profiles.view', compact('company_profile', 'states', 'msics', 'ro'));
    }
    
    public function create()
    {
        $company_profile = new CompanyProfile();
    	$states = Selection::select('id', 'selection_data')->where('selection_type', 'state')->where('status', '0')->get();
        $msics = Msic::select('id', 'msic_code', 'description')->where('status', '0')->orderBy('msic_code')->get();

        $ro = '';

        return view('company_profiles.create', compact('company_profile', 'states', 'msics', 'ro'));
    }
    
    public function store(CompanyProfileFormRequest $request)
    {
        $user = Auth::user();
        
        $company_profile = CompanyProfile::create([  
                 'company_name'  => request('company_name'),
                 'address_line_1'  => request('address_line_1'),
                 'address_line_2'  => request('address_line_2'),
                 'state'  => request('state'),
                 'city'  => request('city'),
                 'postcode'  => request('postcode'),
                 'email'  => request('email'),
                 'contact'  => request('contact'),
                 'tin'  => request('tin'),
                 'sst_registration_no'  => request('sst_registration_no'),
                 'msic_code'  => request('msic_code'),
                 'business_registration_no'  => request('business_registration_no'),
                 'identification_number'  => request('identification_number'),
                 'passport_number'  => request('passport_number'),
                 'created_by' => $user->id
            ]);

            if ($request->hasFile('is_image') && $request->file('is_image')->isValid()) {
                $company_profile->addMediaFromRequest('is_image')->toMediaCollection('is_image');
                $company_profile->updateProfilePicture($company_profile->id, 1);
            }
    

        return redirect()->route('company_profiles.index')
            ->with('success', 'Company Profile created successfully.');    
    }
    
    public function edit($id)
    {
        $company_profile = CompanyProfile::findOrFail($id);
    	$states = Selection::select('id', 'selection_data')->where('selection_type', 'state')->where('status', '0')->get();
        $msics = Msic::select('id', 'msic_code', 'description')->where('status', '0')->orderBy('msic_code')->get();

        $ro = '';

        return view('company_profiles.edit', compact('company_profile', 'states', 'msics', 'ro'));
    }
    
    public function update(CompanyProfileFormRequest $request, $id)
    {
        $user = Auth::user();
    
        $company_profile = CompanyProfile::where('id', $id)
            ->update([
                 'company_name'  => request('company_name'),
                 'address_line_1'  => request('address_line_1'),
                 'address_line_2'  => request('address_line_2'),
                 'state'  => request('state'),
                 'city'  => request('city'),
                 'postcode'  => request('postcode'),
                 'email'  => request('email'),
                 'contact'  => request('contact'),
                 'tin'  => request('tin'),
                 'sst_registration_no'  => request('sst_registration_no'),
                 'msic_code'  => request('msic_code'),
                 'business_registration_no'  => request('business_registration_no'),
                 'identification_number'  => request('identification_number'),
                 'passport_number'  => request('passport_number'),
                 'updated_by' => $user->id
            ]);

            if ($request->hasFile('is_image') && $request->file('is_image')->isValid()) {
                $company_profile = CompanyProfile::findOrFail($id);
                $company_profile->clearMediaCollection('is_image');
                $company_profile->addMediaFromRequest('is_image')->toMediaCollection('is_image');
                $company_profile->updateProfilePicture($id, 1);
            }
    

        return redirect()->route('company_profiles.index')
            ->with('success', 'Company Profile updated successfully.');    
    }
    
    public function show($id)
    {
        $company_profile = CompanyProfile::findOrFail($id);
    	$states = Selection::select('id', 'selection_data')->where('selection_type', 'state')->where('status', '0')->get();
        $msics = Msic::select('id', 'msic_code', 'description')->where('status', '0')->orderBy('msic_code')->get();

        $ro = '';

        return view('company_profiles.show', compact('company_profile', 'states', 'msics', 'ro'));
    }
     
    public function destroy($id)
    {
        $user = Auth::user();

        CompanyProfile::where('id', $id)
                ->update([
                    'updated_by' => $user->id,
                    'status' => '1'
                  ]);

        return redirect()->route('company_profiles.index')
            ->with('success', 'Company Profile deleted successfully.');   
    }
}
