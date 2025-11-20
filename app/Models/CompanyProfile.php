<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompanyProfile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'company_profiles';

    protected $primaryKey = 'id';

    protected $fillable = ['company_name', 'address_line_1', 'address_line_2', 'state', 'city', 'postcode', 'email', 'contact', 'is_image', 'tin', 'sst_registration_no', 'msic_code', 'business_registration_no', 'identification_number', 'passport_number', 'status', 'created_by', 'updated_by'];

    // Update profile picture
    public function updateProfilePicture($id, $status)
    {
        $companyProfile = CompanyProfile::find($id);
        if ($companyProfile) {
            $companyProfile->is_image = $status;
            $companyProfile->save();
        }
    }
}
