<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Models\OtherProfile;
use Illuminate\Http\Request;

// TO CHECK IF COMPANY NAME ALREADY EXISTS IN CUSTOMER PROFILE OR OTHER PROFILE
// Guna dekat web.php

class CompanyValidationController extends Controller
{
    public function checkCompanyName(Request $request)
    {
        $companyName = $request->company_name;

        // Check in CustomerProfile
        $existsInCustomer = CustomerProfile::where('company_name', $companyName)
            ->where('status', 0)
            ->exists();

        // Check in OtherProfile
        $existsInOther = OtherProfile::where('company_name', $companyName)
            ->where('status', 0)
            ->exists();

        if ($existsInCustomer) {
            return response()->json([
                'valid' => false,
                'message' => 'This company name already exists in Customer Profiles.'
            ]);
        }

        if ($existsInOther) {
            return response()->json([
                'valid' => false,
                'message' => 'This company name already exists in Other Profiles.'
            ]);
        }

        return response()->json([
            'valid' => true
        ]);
    }
} 