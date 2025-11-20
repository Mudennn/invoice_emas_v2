<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    protected $table = 'product_details';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'code', 'category', 'status', 'created_by', 'updated_by'];


    public function categoryProduct()
    {
        return $this->belongsTo(CategoryProduct::class, 'category');
    }
}
