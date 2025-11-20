<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Payment extends Model implements HasMedia

{
    use HasFactory, InteractsWithMedia;
    protected $table = 'payments';

 protected $primaryKey = 'id';

//  protected $fillable = ['invoice_id', 'total_payment', 'balance_payment', 'is_image', 'payment_date', 'payment_notes', 'status', 'created_by', 'updated_by'];
 protected $fillable = ['invoice_id', 'total_payment', 'payment_voucher', 'status', 'created_by', 'updated_by'];
 
    // Add relationship to Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Update payment picture
    public function updatePaymentPicture($id, $status)
    {
        $payment = Payment::find($id);
        $payment->is_image = $status;
        $payment->update();
    }
}
