<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    protected $table = 'category_products';

    protected $primaryKey = 'id';
   
    protected $fillable = ['name', 'status', 'created_by', 'updated_by'];
}
