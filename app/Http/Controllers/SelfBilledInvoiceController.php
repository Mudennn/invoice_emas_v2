<?php

namespace App\Http\Controllers;
use App\Http\Requests\SelfBilledInvoiceFormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerProfile;
use App\Models\GoldPrice;
use App\Models\ProductDetail;
use App\Models\OtherProfile;
use App\Models\Selection;
use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\DB;
use App\Models\SelfBilledInvoiceItem;
use Illuminate\Support\Str;
use App\Models\SelfBilledInvoice;

class SelfBilledInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $self_billed_invoices = SelfBilledInvoice::select(
			'self_billed_invoices.*'
		)
		->where('self_billed_invoices.status', '0')
		->orderBy('self_billed_invoices.created_at', 'desc')
		->get();

        return view('self_billed_invoices.index', compact('self_billed_invoices'));
    }

    public function view(Request $request, $id = null)
    {
        $query = SelfBilledInvoiceItem::query()
            ->leftJoin('selections', 'self_billed_invoice_items.pair', '=', 'selections.id')
            ->select(
                'self_billed_invoice_items.*',
                'selections.selection_data as s_pair'
            )
            ->where('self_billed_invoice_items.status', '0');
        
        // Filter by the ID from route parameter
        if ($id) {
            $query->where('self_billed_invoice_id', $id);
        }
        // Or use the request parameter if provided
        elseif ($request->has('self_billed_invoice_id')) {
            $query->where('self_billed_invoice_id', $request->self_billed_invoice_id);
        }
        
        $selfBilledInvoiceItems = $query->get();
        
        // Get the invoice number if items exist
        $selfBilledInvoiceNumber = '';
        if ($selfBilledInvoiceItems->isNotEmpty()) {
            $first_item = $selfBilledInvoiceItems->first();
            $selfBilledInvoice = $first_item->selfBilledInvoice;
            if ($selfBilledInvoice) {
                $selfBilledInvoiceNumber = $selfBilledInvoice->self_billed_invoice_no;
            }
        }
        
        return view('self_billed_invoices.view', compact('selfBilledInvoiceItems', 'selfBilledInvoiceNumber'));
    }

    public function create()
    {
        $selfBilledInvoice = new SelfBilledInvoice();

        // Generate next invoice number
        $lastInvoice = SelfBilledInvoice::orderBy('self_billed_invoice_no', 'desc')->first();
        $nextInvoiceNo = $lastInvoice ? $this->generateNextInvoiceNo($lastInvoice->self_billed_invoice_no) : 'SB0001';
        $selfBilledInvoice->self_billed_invoice_no = $nextInvoiceNo;

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

        // $goldPrices = GoldPrice::where('status', '0')->orderBy('date_change', 'desc')->get();
        $products = ProductDetail::where('status', '0')->get();
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();
        $ro = '';

        return view('self_billed_invoices.create', compact('selfBilledInvoice', 'allCompanies', 'ro', 'products', 'states', 'pair'));
    }

    public function generateNextInvoiceNo($lastInvoiceNo): string
    {
        // Extract the numeric part
        $number = (int) substr($lastInvoiceNo, 3);
        // Increment it
        $number++;
        // Format it back with leading zeros
        return 'SB' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    public function store(SelfBilledInvoiceFormRequest $request)
    {
        try {
            $user = Auth::user();

            // Generate a UUID if not provided
            $selfBilledInvoiceUuid = $request->self_billed_invoice_uuid ?? Str::uuid()->toString();

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
            $selfBilledInvoice = SelfBilledInvoice::create([
                'company_name' => $request->company_name === 'Other' ? $request->other_company_name : $request->company_name,
                'self_billed_invoice_date' => $request->self_billed_invoice_date ? \Carbon\Carbon::parse($request->self_billed_invoice_date)->format('Y/m/d') : null,
                'self_billed_invoice_no' => $request->self_billed_invoice_no,
                'goods_received_by' => $request->goods_received_by,
                'payment_received_by' => $request->payment_received_by,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '01',
                'self_billed_invoice_uuid' => $selfBilledInvoiceUuid,
                // 'signed_info' => $request->signed_info,
                // 'signature_value' => $request->signature_value,
                // 'x509_certificate' => $request->x509_certificate,
                // 'issuer_name' => $request->issuer_name,
                // 'serial_number' => $request->serial_number,
                'status' => '0',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // Create invoice items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
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
                    
                    $selfBilledInvoice->selfBilledInvoiceItems()->create([
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

                        // Add tax details from invoice level
                        // 'currency_code' => $request->currency_code,
                        // 'classification' => $request->classification,
                        // 'tax_type' => $request->tax_type,
                        // 'tax_rate' => $request->tax_rate,
                        // 'tax_amount' => $request->tax_amount,
                        // 'tax_exemption' => $request->tax_exemption,
                        // 'amount_tax_exemption' => $request->amount_tax_exemption,
                        'quantity' => $item['quantity'] ?? null,
                        'pair' => $item['pair'],
                        'unit_price' => $item['unit_price'] ?? null,
                        'kt' => $kt,
                        'remark_total' => $item['remark_total'] ?? 0,
                        'status' => '0'
                    ]);
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice created successfully',
                    'redirect' => route('self_billed_invoices.index')
                ]);
            }

            return redirect()->route('self_billed_invoices.index')
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
        $selfBilledInvoice = SelfBilledInvoice::with(['selfBilledInvoiceItems' => function($query) {
            $query->where('status', '0');
        }])
        ->where('id', $id)
        ->first();

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();    

        // Get the first invoice item's tax details to display in the form  
        $taxDetails = $selfBilledInvoice->selfBilledInvoiceItems->first() ?? new SelfBilledInvoiceItem();

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

        // $goldPrices = GoldPrice::where('status', '0')->orderBy('date_change', 'desc')->get();
        $products = ProductDetail::where('status', '0')->get();
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();
        $ro = '';

        return view('self_billed_invoices.edit', compact('selfBilledInvoice', 'allCompanies', 'ro', 'products', 'states', 'taxDetails', 'pair'));
    }

    public function update(SelfBilledInvoiceFormRequest $request, $id)
    {
        try {
            $user = Auth::user();

            $selfBilledInvoice = SelfBilledInvoice::findOrFail($id);

            // Generate a UUID if not provided
            $selfBilledInvoiceUuid = $request->self_billed_invoice_uuid ?? Str::uuid()->toString();

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
            // $subtotal = collect($request->items)->sum('total');
            // $sst = $subtotal * 0.08; // 8% SST
            // $grand_total = $subtotal + $sst;
            // $grand_total = collect($request->items)->sum('total');
            $subtotal = collect($request->items)->sum('total');

            // Update invoice
            $selfBilledInvoice->update([
                'company_name' => $request->company_name === 'Other' ? $request->other_company_name : $request->company_name,
                'self_billed_invoice_date' => $request->self_billed_invoice_date ? \Carbon\Carbon::parse($request->self_billed_invoice_date)->format('Y/m/d') : null,
                'self_billed_invoice_no' => $request->self_billed_invoice_no,
                'goods_received_by' => $request->goods_received_by,
                'payment_received_by' => $request->payment_received_by,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '01',
                'self_billed_invoice_uuid' => $selfBilledInvoiceUuid,
                // 'signed_info' => $request->signed_info,
                // 'signature_value' => $request->signature_value,
                // 'x509_certificate' => $request->x509_certificate,
                // 'issuer_name' => $request->issuer_name,
                // 'serial_number' => $request->serial_number,
                'updated_by' => $user->id,
            ]);

            // Get existing item IDs for tracking
            $existingIds = $selfBilledInvoice->selfBilledInvoiceItems()
                ->where('status', '0')
                ->pluck('id')
                ->toArray();

            $submittedIds = [];

            // Update or create invoice items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    // Skip empty items
                    if (empty($item['reference_no']) && empty($item['particulars'])) {
                        continue;
                    }

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

                    if (!empty($item['id'])) {
                        // Update existing item
                        $selfBilledInvoice->selfBilledInvoiceItems()
                            ->where('id', $item['id'])
                            ->where('status', '0')
                            ->update([
                                'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                                'particulars' => $item['particulars'],
                                'weight' => $item['weight'],
                                'wastage' => $item['wastage'],
                                'total_weight' => $item['total_weight'],
                                // 'gold_price_id' => $item['gold_price_id'] ?? null,
                                'gold' => $item['gold'] ?? null,
                                'workmanship' => $item['workmanship'] ?? null,
                                'total' => $item['total'] ?? 0,
                                // 'sst' => $sst,
                                'sst' => 0,
                                // 'grand_total' => $grand_total,
                                'subtotal' => $subtotal,
                                'remark' => $item['remark'],
                                'item_type' => $item['item_type'],
                                'custom_reference' => $item['custom_reference'] ?? null,
                                'pure_gold' => $pureGold,
                                
                                // Add tax details from invoice level
                                // 'currency_code' => $request->currency_code,
                                // 'classification' => $request->classification,
                                // 'tax_type' => $request->tax_type,
                                // 'tax_rate' => $request->tax_rate,
                                // 'tax_amount' => $request->tax_amount,
                                // 'tax_exemption' => $request->tax_exemption,
                                // 'amount_tax_exemption' => $request->amount_tax_exemption,
                                'quantity' => $item['quantity'] ?? null,
                                'unit_price' => $item['unit_price'] ?? null,
                                'pair' => $item['pair'],
                                'remark_total' => $item['remark_total'] ?? 0,
                                'kt' => $kt,
                            ]);

                        $submittedIds[] = $item['id'];
                    } else {
                        // Only create if it's truly a new item
                        $selfBilledInvoice->selfBilledInvoiceItems()->create([
                            'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                            'particulars' => $item['particulars'],
                            'weight' => $item['weight'],
                            'wastage' => $item['wastage'],
                            'total_weight' => $item['total_weight'],
                            // 'gold_price_id' => $item['gold_price_id'] ?? null,
                            'gold' => $item['gold'] ?? null,
                            'workmanship' => $item['workmanship'] ?? null,
                            'total' => $item['total'] ?? 0,
                            // 'sst' => $sst,
                            'sst' => 0,
                            // 'grand_total' => $grand_total,
                            'subtotal' => $subtotal,
                            'remark' => $item['remark'],
                            'item_type' => $item['item_type'],
                            'custom_reference' => $item['custom_reference'] ?? null,
                            'pure_gold' => $pureGold,

                            // Add tax details from invoice level
                            // 'currency_code' => $request->currency_code,
                            // 'classification' => $request->classification,
                            // 'tax_type' => $request->tax_type,
                            // 'tax_rate' => $request->tax_rate,
                            // 'tax_amount' => $request->tax_amount,
                            // 'tax_exemption' => $request->tax_exemption,
                            // 'amount_tax_exemption' => $request->amount_tax_exemption,
                            'quantity' => $item['quantity'] ?? null,
                            'unit_price' => $item['unit_price'] ?? null,
                            'pair' => $item['pair'],
                            'remark_total' => $item['remark_total'] ?? 0,
                            'kt' => $kt,
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
                $selfBilledInvoice->selfBilledInvoiceItems()
                    ->whereIn('id', $deletedItems)
                    ->delete();
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice updated successfully',
                    'redirect' => route('self_billed_invoices.index')
                ]);
            }

            return redirect()->route('self_billed_invoices.index')
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
        $selfBilledInvoice = SelfBilledInvoice::with(['selfBilledInvoiceItems' => function($query) {
            $query->where('status', '0');
        }])
        ->findOrFail($id);

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        // Get the first invoice item's tax details to display in the form
        $taxDetails = $selfBilledInvoice->selfBilledInvoiceItems->first() ?? new SelfBilledInvoiceItem();

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

        // $goldPrices = GoldPrice::where('status', '0')->orderBy('date_change', 'desc')->get();
        $products = ProductDetail::where('status', '0')->get();
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();
        $ro = '';

        return view('self_billed_invoices.show', compact(
            'selfBilledInvoice',
            'allCompanies',
            'ro',
            // 'goldPrices',
            'products',
            'states',
            'taxDetails',
            'pair'
        ));
    }

    public function destroy($id)
    {
        $user = Auth::user();

        // $invoice = Invoice::findOrFail($id);
        // $invoice->invoiceItems()->delete();
        // $invoice->delete();
        SelfBilledInvoice::where('id', $id)
                ->update([
                    'updated_by' => $user->id,
                    'status' => '1'
                  ]);

        return redirect()->route('self_billed_invoices.index')->with('success', 'Self-Billed Invoice deleted successfully');
    }

    public function payments($id)
    {
        $selfBilledInvoice = SelfBilledInvoice::with(['payments' => function($query) {
            $query->where('status', '0');
        }])->findOrFail($id);

        return view('self_billed_invoices.payments', compact('selfBilledInvoice'));
    }

    // Fetch invoice details for payment form page
    public function getDetails(SelfBilledInvoice $selfBilledInvoice)
    {
        $grandTotal = $selfBilledInvoice->selfBilledInvoiceItems()
            ->where('status', '0')
            ->sum('total');

        // Format the number with 2 decimal places
        // $formattedTotal = number_format($grandTotal + ($grandTotal * 0.08), 2, '.', '');

        return response()->json([
            'grand_total' => $grandTotal
        ]);
    }

    // Print Invoice
    public function print($id)
    {
        // Get invoice data 
        $selfBilledInvoice = SelfBilledInvoice::findOrFail($id);
        
        // Get invoice items separately with proper join
        $selfBilledInvoiceItems = SelfBilledInvoiceItem::where('self_billed_invoice_items.self_billed_invoice_id', $id)
            ->where('self_billed_invoice_items.status', '0')
            ->leftJoin('selections', function($join) {
                $join->on('self_billed_invoice_items.pair', '=', 'selections.id')
                    ->where('selections.selection_type', '=', 'pair')
                    ->where('selections.status', '0');
            })
            ->select(
                'self_billed_invoice_items.*',
                'selections.selection_data as s_pair'
            )
            ->get();
        
        // Attach items to invoice
        $selfBilledInvoice->setRelation('selfBilledInvoiceItems', $selfBilledInvoiceItems);

        // Get customer profile data
        $customerProfile = CustomerProfile::where('company_name', $selfBilledInvoice->company_name)
            ->where('customer_profiles.status', '0')
            ->leftJoin('selections as state', 'customer_profiles.state', '=', 'state.id')
            ->select(
                'customer_profiles.*',
                'state.selection_data as s_state',
                DB::raw("'Main Client' as profile_type")
            )
            ->first();

        // Get other profile data
        $otherProfile = OtherProfile::where('company_name', $selfBilledInvoice->company_name)
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

        return view('self_billed_invoices.print', compact('selfBilledInvoice', 'ourCompany', 'customerProfile', 'otherProfile'));
    }
}