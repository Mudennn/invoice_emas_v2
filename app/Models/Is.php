<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Is extends Model
{
    protected $table = 'is';

    protected $primaryKey = 'id';

    protected $fillable = [
        'is_no',
        'is_date',
        'company_name',
        'weight',
        'status',
        'created_by',
        'updated_by'
    ];

}
