<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Receipt extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'receipts';

    protected $primaryKey = 'id';

    protected $fillable = [
        'receipt_no',
        'receipt_date',
        'receipt_note',
        'receipt_image',
        'status',
        'created_by',
        'updated_by'
    ];

    // Update receipt picture
    public function updateReceiptPicture($id, $status)
    {
        $receipt = Receipt::find($id);
        $receipt->receipt_image = $status;
        $receipt->update();
    }
}
