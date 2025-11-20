<?php

namespace App\Http\Controllers;
use App\Http\Requests\InvoiceItemFormRequest;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;
use App\Models\Selection;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $query = InvoiceItem::query()
            ->leftJoin('selections', 'invoice_items.pair', '=', 'selections.id')
            ->select(
                'invoice_items.*',
                'selections.selection_data as s_pair'
            )
            ->where('invoice_items.status', '0');
        
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }
        
        $invoice_items = $query->where('invoice_items.status', '0')->get();
        
        // Get the invoice number if items exist
        $invoice_number = '';
        if ($invoice_items->isNotEmpty()) {
            $first_item = $invoice_items->first();
            $invoice = $first_item->invoice;
            if ($invoice) {
                $invoice_number = $invoice->invoice_no;
            }
        }
        
        return view('invoice_items.index', compact('invoice_items', 'invoice_number'));
    }
    
    public function view($id)
    {
        $invoice_item = InvoiceItem::findOrFail($id); 
        $ro = 'readonly'; 
        
        return view('invoice_items.view', compact('invoice_item',  'ro'));
    }
    
    public function create()
    {        
        $invoice_item = new InvoiceItem();
        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();
    	 
        $ro = ''; 
        
        return view('invoice_items.create', compact('invoice_item', 'ro'));
    }
    
    public function store(InvoiceItemFormRequest $request)
    {
        $user = Auth::user();
        
        $invoice_item = InvoiceItem::create([  
                 'invoice_id'  => request('invoice_id'),
                 'reference_no'  => request('reference_no'),
                 'particulars'  => request('particulars'),
                 'weight'  => request('weight'),
                 'wastage'  => request('wastage'),
                 'total_weight'  => request('total_weight'),
                 'gold'  => request('gold'),
                 'workmanship'  => request('workmanship'),
                 'total'  => request('total'),
                 'grand_total'  => request('grand_total'),
                 'remark'  => request('remark'),
                 'created_by' => $user->id
            ]);
    

        return redirect()->route('invoice_items.index');    
    }
    
    public function edit($id)
    {
        $invoice_item = InvoiceItem::findOrFail($id);
    	 
        $ro = ''; 
        
        return view('invoice_items.edit', compact('invoice_item', 'ro'));
    }
    
    public function update(InvoiceItemFormRequest $request, $id)
    {
        $user = Auth::user();
    
        $invoice_item = InvoiceItem::where('id', $id)
            ->update([
                 'invoice_id'  => request('invoice_id'),
                 'reference_no'  => request('reference_no'),
                 'particulars'  => request('particulars'),
                 'weight'  => request('weight'),
                 'wastage'  => request('wastage'),
                 'total_weight'  => request('total_weight'),
                 'gold'  => request('gold'),
                 'workmanship'  => request('workmanship'),
                 'total'  => request('total'),
                 'grand_total'  => request('grand_total'),
                 'remark'  => request('remark'),
                 'updated_by' => $user->id
            ]);
    

        return redirect()->route('invoice_items.index');    
    }
    
    public function show($id)
    {
        $invoice_item = InvoiceItem::findOrFail($id);
    	 
        $ro = ''; 
              
        return view('invoice_items.show', compact('invoice_item', 'ro'));
    }
     
    public function destroy($id)
    {
        $user = Auth::user();

        $invoice_item = InvoiceItem::findOrFail($id);
        $invoice_item->delete();
        // InvoiceItem::where('id', $id)
        //         ->update([
        //             'updated_by' => $user->id,
        //             'status' => '1'
        //           ]);

        return redirect()->route('invoice_items.index');   
    }
}
