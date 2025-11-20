<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherProfile extends Model
{
    protected $table = 'others';

    protected $primaryKey = 'id';
   
    protected $fillable = ['company_name', 'address_line_1', 'address_line_2', 'state', 'city', 'postcode', 'contact_name_1', 'contact_1', 'email_1', 'contact_name_2', 'contact_2', 'email_2', 'contact_name_3', 'contact_3', 'email_3', 'tin', 'sst_registration_no', 'business_registration_no', 'identification_number', 'passport_number', 'status', 'created_by', 'updated_by'];
    
}
