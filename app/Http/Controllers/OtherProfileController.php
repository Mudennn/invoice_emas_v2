<?php

namespace App\Http\Controllers;

use App\Models\OtherProfile;
use Illuminate\Http\Request;
use App\Models\Selection;
use App\Http\Requests\OtherProfileFormRequest;
use Illuminate\Support\Facades\Auth;
class OtherProfileController extends Controller
{
    public function create()
    {        
        $other = new OtherProfile();
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();
        $ro = ''; 
        
        return view('others.create', compact('other', 'states', 'ro'));
    }
    
    public function store(OtherProfileFormRequest $request)
    {
        try {
            $user = Auth::user();
            
            $other_profile = OtherProfile::create([  
                'company_name'  => request('company_name'),
                'contact_name_1'  => request('contact_name_1'),
                'contact_1'  => request('contact_1'),
                'email_1'  => request('email_1'),
                'contact_name_2'  => request('contact_name_2'),
                'contact_2'  => request('contact_2'),
                'email_2'  => request('email_2'),
                'contact_name_3'  => request('contact_name_3'),
                'contact_3'  => request('contact_3'),
                'email_3'  => request('email_3'),
                'address_line_1'  => request('address_line_1'),
                'address_line_2'  => request('address_line_2'),
                'state'  => request('state'),
                'city'  => request('city'),
                'postcode'  => request('postcode'),
                'status' => '0',
                'created_by' => $user->id
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company profile saved successfully',
                    'company' => $other_profile
                ]);
            }

            return redirect()->route('customer_profiles.index')->with('success', 'Profile created successfully');    

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving company profile: ' . $e->getMessage()
                ], 500);
            }

            throw $e;
        }
    }

    public function edit($id)
    {
        $other = OtherProfile::findOrFail($id);
        $states = Selection::select('id', 'selection_data')->where('selection_type', 'state')->where('status', '0')->get();
        $ro = ''; 
        
        return view('others.edit', compact('other', 'states', 'ro'));
    }

    public function update(OtherProfileFormRequest $request, $id)
    {
        $user = Auth::user();
    
        $other_profile = OtherProfile::where('id', $id)
            ->update([
                'company_name'  => request('company_name'),
                'contact_name_1'  => request('contact_name_1'),
                'contact_1'  => request('contact_1'),
                'email_1'  => request('email_1'),
                'contact_name_2'  => request('contact_name_2'),
                'contact_2'  => request('contact_2'),
                'email_2'  => request('email_2'),
                'contact_name_3'  => request('contact_name_3'),
                'contact_3'  => request('contact_3'),
                'email_3'  => request('email_3'),
                'address_line_1'  => request('address_line_1'),
                'address_line_2'  => request('address_line_2'),
                'state'  => request('state'),
                'city'  => request('city'),
                'postcode'  => request('postcode'),
                'updated_by' => $user->id
            ]);

        return redirect()->route('customer_profiles.index')->with('success', 'Profile updated successfully');    
    }

    public function destroy($id)
    {
        $user = Auth::user();

        OtherProfile::where('id', $id)
            ->update([
                'updated_by' => $user->id,
                'status' => '1'
            ]);

        return redirect()->route('customer_profiles.index');   
    }

    public function storeAjax(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'company_name' => 'required|string|max:255|unique:others,company_name',
                'address_line_1' => 'required|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'state' => 'required',
                'city' => 'required|string|max:255',
                'postcode' => 'required|string|max:20',
                'contact_name_1' => 'required|string|max:255',
                'contact_1' => 'required|string|max:255',
                'email_1' => 'required|email|max:255',
                'contact_name_2' => 'nullable|string|max:255',
                'contact_2' => 'nullable|string|max:255',
                'email_2' => 'nullable|email|max:255',
                'contact_name_3' => 'nullable|string|max:255',
                'contact_3' => 'nullable|string|max:255',
                'email_3' => 'nullable|email|max:255',
                'tin' => 'nullable|string|max:255',
                'sst_registration_no' => 'nullable|string|max:255',
                'business_registration_no' => 'nullable|string|max:255',
                'identification_number' => 'nullable|string|max:255',
                'passport_number' => 'nullable|string|max:255',
            ]);
            
            $other_profile = OtherProfile::create([  
                'company_name'  => $validated['company_name'],
                'contact_name_1'  => $validated['contact_name_1'],
                'contact_1'  => $validated['contact_1'],
                'email_1'  => $validated['email_1'],
                'contact_name_2'  => $validated['contact_name_2'],
                'contact_2'  => $validated['contact_2'],
                'email_2'  => $validated['email_2'],
                'contact_name_3'  => $validated['contact_name_3'],
                'contact_3'  => $validated['contact_3'],
                'email_3'  => $validated['email_3'],
                'address_line_1'  => $validated['address_line_1'],
                'address_line_2'  => $validated['address_line_2'],
                'state'  => $validated['state'],
                'city'  => $validated['city'],
                'postcode'  => $validated['postcode'],
                'tin' => $validated['tin'],
                'sst_registration_no' => $validated['sst_registration_no'],
                'business_registration_no' => $validated['business_registration_no'],
                'identification_number' => $validated['identification_number'],
                'passport_number' => $validated['passport_number'],
                'status' => '0',
                'created_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Company profile saved successfully',
                'company' => $other_profile
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving company profile: ' . $e->getMessage()
            ], 500);
        }
    }

}
