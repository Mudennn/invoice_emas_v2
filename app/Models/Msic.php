<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Msic extends Model 
{
    protected $table = 'msics';

    protected $primaryKey = 'id';

    protected $fillable = [
        'msic_code',
        'description',
        'category_reference',
        'status',
        'created_by',
        'updated_by'
    ];
}
