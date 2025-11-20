<?php

namespace App\Http\Controllers;
use App\Http\Requests\PaymentFormRequest;
use App\Models\Payment;
use App\Models\Selection;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Invoice;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $payments = Payment::select('payments.*')
            ->with('invoice')
            ->where('payments.status', '0')
            ->get();
	
        return view('payments.index', compact('payments'));
    }
    
    public function view($id)
    {
        $payment = Payment::findOrFail($id);
         
        $ro = 'readonly'; 
        
        return view('payments.view', compact('payment',  'ro'));
    }
    
    public function create()
    {        
        $payment = new Payment();    
         
        $ro = ''; 
        
        $invoices = Invoice::where('status', '0')->get();
        
        return view('payments.create', compact('payment', 'ro', 'invoices'));
    }
    
    public function store(PaymentFormRequest $request)
    {
        $user = Auth::user();
        
        $payment = Payment::create([  
                 'invoice_id'  => $request->invoice_id,
                 'total_payment'  => $request->total_payment,
                 'payment_voucher'  => $request->payment_voucher,
                //  'balance_payment'  => request('balance_payment'),
                //  'is_image'  => 0,
                //  'payment_date'  => $request->payment_date ? \Carbon\Carbon::parse($request->payment_date)->format('Y/m/d') : null,
                //  'payment_notes'  => request('payment_notes'),
                 'created_by' => $user->id,
                 'updated_by' => $user->id,
                 'status' => '0',
            ]);
    
            // if ($request->hasFile('is_image') && $request->file('is_image')->isValid()) {
            //     $payment->addMediaFromRequest('is_image')->toMediaCollection('is_image');
            //     $payment->updatePaymentPicture($payment->id, 1);
            // }
        

        return redirect()->route('payments.index')->with('success', 'Payment created successfully.');    
    }
    
    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
         
        $ro = ''; 
        
        $invoices = Invoice::where('status', '0')->get();
        
        return view('payments.edit', compact('payment', 'ro', 'invoices'));
    }
    
    public function update(PaymentFormRequest $request, $id)
    {
        $user = Auth::user();
    
        $payment = Payment::where('id', $id)
            ->update([
                 'invoice_id'  => $request->invoice_id,
                 'total_payment'  => $request->total_payment,
                 'payment_voucher'  => $request->payment_voucher,
                //  'balance_payment'  => request('balance_payment'),
                //  'is_image'  => request('is_image'),
                //  'payment_date'  => $request->payment_date ? \Carbon\Carbon::parse($request->payment_date)->format('Y/m/d') : null,
                //  'payment_notes'  => request('payment_notes'),
                 'created_by' => $user->id,
                 'updated_by' => $user->id,   
                 'status' => '0',
            ]);

            // if ($request->hasFile('is_image') && $request->file('is_image')->isValid()) {
            //     $payment = Payment::findOrFail($id);
            //     $payment->clearMediaCollection('is_image');
            //     $payment->addMediaFromRequest('is_image')->toMediaCollection('is_image');
            //     $payment->updatePaymentPicture($id, 1);
            // }

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');    
    }
    
    public function show($id)
    {
        $payment = Payment::findOrFail($id);
         
        $ro = 'readonly'; 

        $invoices = Invoice::where('status', '0')->get();
              
        return view('payments.show', compact('payment', 'ro', 'invoices'));
    }
     
    public function destroy($id)
    {
        $user = Auth::user();

        Payment::where('id', $id)
                ->update([
                    'updated_by' => $user->id,
                    'status' => '1'
                  ]);

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');   
    }
    
}
