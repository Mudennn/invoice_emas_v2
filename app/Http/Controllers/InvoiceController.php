<?php

namespace App\Http\Controllers;
use App\Http\Requests\InvoiceFormRequest;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerProfile;
use App\Models\GoldPrice;
use App\Models\ProductDetail;
use App\Models\OtherProfile;
use App\Models\Selection;
use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\DB;
use App\Models\InvoiceItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $invoices = Invoice::select(
			'invoices.*'
		)
		->where('invoices.status', '0')
		->orderBy('invoices.created_at', 'desc')
		->get();

        return view('invoices.index', compact('invoices'));
    }

    public function view($id)
    {
        $invoice = Invoice::findOrFail($id);
        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        $ro = 'readonly';

        return view('invoices.view', compact('invoice', 'pair', 'ro'));
    }

    public function create()
    {
        $invoice = new Invoice();

        // Generate next invoice number
        $lastInvoice = Invoice::orderBy('invoice_no', 'desc')->first();
        $nextInvoiceNo = $lastInvoice ? $this->generateNextInvoiceNo($lastInvoice->invoice_no) : 'INV0001';
        $invoice->invoice_no = $nextInvoiceNo;

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        // Get customers and others, then combine them
        $customerCompanies = CustomerProfile::select('company_name')
            ->where('status', '0')
            ->get()
            ->pluck('company_name');

        $otherCompanies = OtherProfile::select('company_name')
            ->where('status', '0')
            ->get()
            ->pluck('company_name');

        // Combine both collections and remove duplicates
        $allCompanies = $customerCompanies->concat($otherCompanies)->unique()->values();

        $products = ProductDetail::where('status', '0')->get();
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();
        $ro = '';

        return view('invoices.create', compact('invoice', 'allCompanies', 'ro', 'products', 'states', 'pair'));
    }

    public function generateNextInvoiceNo($lastInvoiceNo): string
    {
        // Extract the numeric part
        $number = (int) substr($lastInvoiceNo, 3);
        // Increment it
        $number++;
        // Format it back with leading zeros
        return 'INV' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    public function store(InvoiceFormRequest $request)
    {
        try {
            $user = Auth::user();

            // Generate a UUID if not provided
            $invoiceUuid = $request->invoice_uuid ?? Str::uuid()->toString();

            // If "Other" company is selected, create it in Others table first
            if ($request->company_name === 'Other' && $request->other_company_name) {
                // Create new company in Others table
                $otherProfile = OtherProfile::create([
                    'company_name' => $request->other_company_name,
                    'address_line_1' => $request->other_address_line_1,
                    'address_line_2' => $request->other_address_line_2,
                    'state' => $request->other_state,
                    'city' => $request->other_city,
                    'postcode' => $request->other_postcode,
                    'contact_name_1' => $request->other_contact_name_1,
                    'contact_1' => $request->other_contact_1,
                    'email_1' => $request->other_email_1,
                    'contact_name_2' => $request->other_contact_name_2,
                    'contact_2' => $request->other_contact_2,
                    'email_2' => $request->other_email_2,
                    'contact_name_3' => $request->other_contact_name_3,
                    'contact_3' => $request->other_contact_3,
                    'email_3' => $request->other_email_3,
                    'tin' => $request->other_tin,
                    'sst_registration_no' => $request->other_sst_registration_no,
                    'business_registration_no' => $request->other_business_registration_no,
                    'identification_number' => $request->other_identification_number,
                    'passport_number' => $request->other_passport_number,
                    'status' => '0',
                ]);
            }

            // Calculate totals
            $subtotal = collect($request->items)->sum('total');

            // Create the invoice
            $invoice = Invoice::create([
                'company_name' => $request->company_name === 'Other' ? $request->other_company_name : $request->company_name,
                'invoice_date' => $request->invoice_date ? \Carbon\Carbon::parse($request->invoice_date)->format('Y/m/d') : null,
                'invoice_no' => $request->invoice_no,
                'goods_received_by' => $request->goods_received_by,
                'payment_received_by' => $request->payment_received_by,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '01',
                'invoice_uuid' => $invoiceUuid,
                'status' => '0',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // Create invoice items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    // Debug logging
                    // Log::info('Store Invoice Item Debug:', [
                    //     'item_type' => $item['item_type'] ?? 'NOT_SET',
                    //     'kt_received' => $item['kt'] ?? 'NOT_SET',
                    //     'pure_gold_received' => $item['pure_gold'] ?? 'NOT_SET',
                    //     'kt_isset' => isset($item['kt']) ? 'YES' : 'NO',
                    //     'pure_gold_isset' => isset($item['pure_gold']) ? 'YES' : 'NO'
                    // ]);
                    
                    // Determine kt and pure_gold based on item_type
                    $itemType = $item['item_type'] ?? '';
                    $kt = null;
                    $pureGold = null;
                    
                    if (str_contains($itemType, 'with-gold')) {
                        // For with-gold items, only save kt
                        $kt = $item['kt'] ?? null;
                        // Explicitly set pure_gold to null for with-gold items
                        $pureGold = null;
                    } else {
                        // For without-gold items, only save pure_gold  
                        $pureGold = isset($item['pure_gold']) && !empty($item['pure_gold']) ? (string)$item['pure_gold'] : null;
                        // Explicitly set kt to null for without-gold items
                        $kt = null;
                    }
                    
                    // Log::info('Store Final Values:', [
                    //     'item_type' => $itemType,
                    //     'final_kt' => $kt,
                    //     'final_pure_gold' => $pureGold
                    // ]);
                    
                    $invoice->invoiceItems()->create([
                        'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                        'particulars' => $item['particulars'],
                        'weight' => $item['weight'],
                        'wastage' => $item['wastage'],
                        'total_weight' => $item['total_weight'],
                        'gold' => $item['gold'] ?? null,
                        'workmanship' => $item['workmanship'] ?? null,
                        'total' => $item['total'] ?? 0,
                        'sst' => 0,
                        'subtotal' => $subtotal,
                        'remark' => $item['remark'],
                        'item_type' => $item['item_type'],
                        'custom_reference' => $item['custom_reference'] ?? null,
                        'pure_gold' => $pureGold,
                        'quantity' => $item['quantity'] ?? null,
                        'pair' => $item['pair'],
                        'kt' => $kt,
                        'unit_price' => $item['unit_price'] ?? null,
                        'remark_total' => $item['remark_total'] ?? 0,
                        'status' => '0'
                    ]);
                }
            }

            // Ensure all invoice items have consistent subtotal
            $this->syncInvoiceItemSubtotals($invoice->id);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice created successfully',
                    'redirect' => route('invoices.index')
                ]);
            }

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice created successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['error' => [$e->getMessage()]]
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['invoiceItems' => function($query) {
            $query->where('status', '0');
        }])
        ->where('id', $id)
        ->first();

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();    

        // Get the first invoice item's tax details to display in the form
        $taxDetails = $invoice->invoiceItems->first() ?? new InvoiceItem();

        // Get customers and others, then combine them
        $customerCompanies = CustomerProfile::select('company_name')
            ->where('status', '0')
            ->get()
            ->pluck('company_name');

        $otherCompanies = OtherProfile::select('company_name')
            ->where('status', '0')
            ->get()
            ->pluck('company_name');

        // Combine both collections and remove duplicates
        $allCompanies = $customerCompanies->concat($otherCompanies)->unique()->values();

        $products = ProductDetail::where('status', '0')->get();
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();
        $ro = '';

        return view('invoices.edit', compact('invoice', 'allCompanies', 'ro', 'products', 'states', 'taxDetails', 'pair'));
    }

    public function update(InvoiceFormRequest $request, $id)
    {
        try {
            $user = Auth::user();

            $invoice = Invoice::findOrFail($id);

            // Generate a UUID if not provided
            $invoiceUuid = $request->invoice_uuid ?? Str::uuid()->toString();

            // If "Other" company is selected, create or update in Others table
            if ($request->company_name === 'Other' && $request->other_company_name) {
                OtherProfile::updateOrCreate(
                    ['company_name' => $request->other_company_name],
                    [
                        'address_line_1' => $request->other_address_line_1,
                        'address_line_2' => $request->other_address_line_2,
                        'state' => $request->other_state,
                        'city' => $request->other_city,
                        'postcode' => $request->other_postcode,
                        'contact_name_1' => $request->other_contact_name_1,
                        'contact_1' => $request->other_contact_1,
                        'email_1' => $request->other_email_1,
                        'contact_name_2' => $request->other_contact_name_2,
                        'contact_2' => $request->other_contact_2,
                        'email_2' => $request->other_email_2,
                        'contact_name_3' => $request->other_contact_name_3,
                        'contact_3' => $request->other_contact_3,
                        'email_3' => $request->other_email_3,
                        'tin' => $request->other_tin,
                        'sst_registration_no' => $request->other_sst_registration_no,
                        'business_registration_no' => $request->other_business_registration_no,
                        'identification_number' => $request->other_identification_number,
                        'passport_number' => $request->other_passport_number,
                        'status' => '0',
                    ]
                );
            }

            // Calculate totals
            $subtotal = collect($request->items)->sum('total');

            // Update invoice
            $invoice->update([
                'company_name' => $request->company_name === 'Other' ? $request->other_company_name : $request->company_name,
                'invoice_date' => $request->invoice_date ? \Carbon\Carbon::parse($request->invoice_date)->format('Y/m/d') : null,
                'invoice_no' => $request->invoice_no,
                'goods_received_by' => $request->goods_received_by,
                'payment_received_by' => $request->payment_received_by,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '01',
                'invoice_uuid' => $invoiceUuid,
                'updated_by' => $user->id,
            ]);

            // Get existing item IDs for tracking
            $existingIds = $invoice->invoiceItems()
                ->where('status', '0')
                ->pluck('id')
                ->toArray();

            $submittedIds = [];

            // Update or create invoice items
            if ($request->has('items')) {
                foreach ($request->items as $index => $item) {
                    // Skip empty items
                    if (empty($item['reference_no']) && empty($item['particulars'])) {
                        continue;
                    }

                    // Debug logging
                    // Log::info('Update Invoice Item Debug:', [
                    //     'item_id' => $item['id'] ?? 'NEW_ITEM',
                    //     'item_type' => $item['item_type'] ?? 'NOT_SET',
                    //     'kt_received' => $item['kt'] ?? 'NOT_SET',
                    //     'pure_gold_received' => $item['pure_gold'] ?? 'NOT_SET',
                    //     'kt_isset' => isset($item['kt']) ? 'YES' : 'NO',
                    //     'pure_gold_isset' => isset($item['pure_gold']) ? 'YES' : 'NO'
                    // ]);

                    // Determine kt and pure_gold based on item_type
                    $itemType = $item['item_type'] ?? '';
                    $kt = null;
                    $pureGold = null;
                    
                    if (str_contains($itemType, 'with-gold')) {
                        // For with-gold items, only save kt
                        $kt = $item['kt'] ?? null;
                        // Explicitly set pure_gold to null for with-gold items
                        $pureGold = null;
                    } else {
                        // For without-gold items, only save pure_gold  
                        $pureGold = isset($item['pure_gold']) && !empty($item['pure_gold']) ? (string)$item['pure_gold'] : null;
                        // Explicitly set kt to null for without-gold items
                        $kt = null;
                    }
                    
                    // Log::info('Update Final Values:', [
                    //     'item_type' => $itemType,
                    //     'final_kt' => $kt,
                    //     'final_pure_gold' => $pureGold
                    // ]);

                    if (!empty($item['id'])) {
                        // Update existing item
                        $invoice->invoiceItems()
                            ->where('id', $item['id'])
                            ->where('status', '0')
                            ->update([
                                'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                                'particulars' => $item['particulars'],
                                'weight' => $item['weight'],
                                'wastage' => $item['wastage'],
                                'total_weight' => $item['total_weight'],
                                'gold' => $item['gold'] ?? null,
                                'workmanship' => $item['workmanship'] ?? null,
                                'total' => $item['total'] ?? 0,
                                'sst' => 0,
                                'subtotal' => $subtotal,
                                'remark' => $item['remark'],
                                'item_type' => $item['item_type'],
                                'custom_reference' => $item['custom_reference'] ?? null,
                                'pure_gold' => $pureGold,
                                'quantity' => $item['quantity'] ?? null,
                                'unit_price' => $item['unit_price'] ?? null,
                                'kt' => $kt,
                                'pair' => $item['pair'],    
                                'remark_total' => $item['remark_total'] ?? 0,
                            ]);

                        $submittedIds[] = $item['id'];
                    } else {
                        // Only create if it's truly a new item
                        $newItem = $invoice->invoiceItems()->create([
                            'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                            'particulars' => $item['particulars'],
                            'weight' => $item['weight'],
                            'wastage' => $item['wastage'],
                            'total_weight' => $item['total_weight'],
                            'gold' => $item['gold'] ?? null,
                            'workmanship' => $item['workmanship'] ?? null,
                            'total' => $item['total'] ?? 0,
                            'sst' => 0,
                            'subtotal' => $subtotal,
                            'remark' => $item['remark'],
                            'item_type' => $item['item_type'],
                            'custom_reference' => $item['custom_reference'] ?? null,
                            'pure_gold' => $pureGold,
                            'quantity' => $item['quantity'] ?? null,
                            'unit_price' => $item['unit_price'] ?? null,
                            'kt' => $kt,
                            'pair' => $item['pair'],
                            'remark_total' => $item['remark_total'] ?? 0,
                            'status' => '0'
                        ]);
                    }
                }
            }

            // Handle deletion of items that were intentionally removed from the form
            $deletedItems = [];
            if ($request->has('deleted_items') && !empty($request->deleted_items)) {
                $deletedItems = json_decode($request->deleted_items, true) ?? [];
            }
            
            if (!empty($deletedItems)) {
                $invoice->invoiceItems()
                    ->whereIn('id', $deletedItems)
                    ->delete();
            }

            // Ensure all invoice items have consistent subtotal after updates
            $this->syncInvoiceItemSubtotals($invoice->id);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice updated successfully',
                    'redirect' => route('invoices.index')
                ]);
            }

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice updated successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['error' => [$e->getMessage()]]
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with(['invoiceItems' => function($query) {
            $query->where('status', '0');
        }])
        ->findOrFail($id);

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        // Get the first invoice item's tax details to display in the form
        $taxDetails = $invoice->invoiceItems->first() ?? new InvoiceItem();

        // Get customers and others, then combine them
        $customerCompanies = CustomerProfile::select('company_name')
            ->where('status', '0')
            ->get()
            ->pluck('company_name');

        $otherCompanies = OtherProfile::select('company_name')
            ->where('status', '0')
            ->get()
            ->pluck('company_name');

        // Combine both collections and remove duplicates
        $allCompanies = $customerCompanies->concat($otherCompanies)->unique()->values();

        $products = ProductDetail::where('status', '0')->get();
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();
        $ro = '';

        return view('invoices.show', compact(
            'invoice',
            'allCompanies',
            'ro',
            'products',
            'states',
            'taxDetails',
            'pair'
        ));
    }

    public function destroy($id)
    {
        $user = Auth::user();

        Invoice::where('id', $id)
                ->update([
                    'updated_by' => $user->id,
                    'status' => '1'
                  ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    public function payments($id)
    {
        $invoice = Invoice::with(['payments' => function($query) {
            $query->where('status', '0');
        }])->findOrFail($id);

        return view('invoices.payments', compact('invoice'));
    }

    // Fetch invoice details for payment form page
    public function getDetails(Invoice $invoice)
    {
        $grandTotal = $invoice->invoiceItems()
            ->where('status', '0')
            ->sum('total');

        return response()->json([
            'grand_total' => $grandTotal
        ]);
    }

    // Print Invoice
    public function print($id)
    {
        // Get invoice data 
        $invoice = Invoice::findOrFail($id);
        
        // Get invoice items separately with proper join
        $invoiceItems = InvoiceItem::where('invoice_items.invoice_id', $id)
            ->where('invoice_items.status', '0')
            ->leftJoin('selections', function($join) {
                $join->on('invoice_items.pair', '=', 'selections.id')
                    ->where('selections.selection_type', '=', 'pair')
                    ->where('selections.status', '0');
            })
            ->select(
                'invoice_items.*',
                'selections.selection_data as s_pair'
            )
            ->get();
        
        // Attach items to invoice
        $invoice->setRelation('invoiceItems', $invoiceItems);

        // Get customer profile data
        $customerProfile = CustomerProfile::where('company_name', $invoice->company_name)
            ->where('customer_profiles.status', '0')
            ->leftJoin('selections as state', 'customer_profiles.state', '=', 'state.id')
            ->select(
                'customer_profiles.*',
                'state.selection_data as s_state',
                DB::raw("'Main Client' as profile_type")
            )
            ->first();

        // Get other profile data
        $otherProfile = OtherProfile::where('company_name', $invoice->company_name)
            ->where('others.status', '0')
            ->leftJoin('selections as state', 'others.state', '=', 'state.id')
            ->select(
                'others.*',
                'state.selection_data as s_state',
                DB::raw("'Other' as profile_type")
            )
            ->first();

        // Get our company profile
        $ourCompany = CompanyProfile::where('company_profiles.status', '0')
            ->leftJoin('selections as state', 'company_profiles.state', '=', 'state.id')
            ->select('company_profiles.*', 'state.selection_data as s_state')
            ->first();

        return view('invoices.print', compact('invoice', 'ourCompany', 'customerProfile', 'otherProfile'));
    }

    /**
     * Sync subtotal for all invoice items of a specific invoice
     * This ensures all items have the same correct subtotal value
     */
    private function syncInvoiceItemSubtotals($invoiceId)
    {
        // Calculate the correct subtotal from all active invoice items
        $correctSubtotal = InvoiceItem::where('invoice_id', $invoiceId)
            ->where('status', '0')
            ->sum('total');

        // Update all active invoice items with the correct subtotal
        InvoiceItem::where('invoice_id', $invoiceId)
            ->where('status', '0')
            ->update(['subtotal' => $correctSubtotal]);
    }
}