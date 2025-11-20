<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    
 protected $table = 'gold_prices';

 protected $primaryKey = 'id';

 protected $fillable = ['name', 'price', 'date_change', 'status', 'created_by', 'updated_by'];
 
}
