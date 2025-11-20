<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreditNoteFormRequest;
use App\Models\Invoice;
use App\Models\ProductDetail;
use App\Models\GoldPrice;
use App\Models\CustomerProfile;
use App\Models\OtherProfile;
use App\Models\Selection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\CreditNoteItem;

class CreditNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $credit_notes = CreditNote::with('invoice')
            ->select('credit_notes.*')
            ->where('credit_notes.status', '0')
            ->orderBy('credit_notes.created_at', 'desc')
            ->get();

        return view('credit_notes.index', compact('credit_notes'));
    }

    public function view($id)
    {
        $credit_note = CreditNote::with([
            'creditItems' => function ($query) {
                $query->where('credit_note_items.status', '0')
                    ->leftJoin('selections', 'credit_note_items.pair', '=', 'selections.id')
                    ->select(
                        'credit_note_items.*',
                        'selections.selection_data as s_pair'
                    );
            },
            'invoice'
        ])->findOrFail($id);

        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'credit_reason')
            ->where('status', '0')
            ->get();

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        $ro = 'readonly';

        return view('credit_notes.view', compact('credit_note', 'reasons', 'ro', 'pair'));
    }

    public function create()
    {
        $credit_note = new CreditNote();

        // Generate next invoice number
        $lastCreditNote = CreditNote::orderBy('credit_note_no', 'desc')->first();
        $nextCreditNoteNo = $lastCreditNote ? $this->generateNextCreditNoteNo($lastCreditNote->credit_note_no) : 'CN0001';
        $credit_note->credit_note_no = $nextCreditNoteNo;

        $invoices = Invoice::where('status', '0')->get();
        $products = ProductDetail::where('status', '0')->get();
        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'credit_reason')
            ->where('status', '0')
            ->get();

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        $ro = '';

        return view('credit_notes.create', compact('credit_note', 'invoices', 'products', 'reasons', 'ro', 'pair'));
    }

    private function generateNextCreditNoteNo($lastCreditNoteNo): string
    {
        // Extract the numeric part
        $number = (int) substr($lastCreditNoteNo, 3);
        // Increment it
        $number++;
        // Format it back with leading zeros
        return 'CN' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    public function store(CreditNoteFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Fetch the invoice_uuid from the selected invoice
            $invoice = Invoice::where('invoice_no', $request->invoice_no)->first();

            // Calculate totals
            $subtotal = collect($request->items)->sum('total');

            // Create credit note
            $credit_note = CreditNote::create([
                'credit_note_no' => $request->credit_note_no,
                'invoice_no' => $request->invoice_no,
                'original_invoice_uuid' => $invoice->invoice_uuid,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '02',
                'date' => $request->date,
                'note' => $request->note,
                'reason' => $request->reason,
                'status' => '0',
                'created_by' => $user->id,
            ]);

            // Create credit note items
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

                    $credit_note->creditItems()->create([
                        'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                        'particulars' => $item['particulars'],
                        'weight' => $item['weight'],
                        'wastage' => $item['wastage'],
                        'total_weight' => $item['total_weight'],
                        'gold' => $item['gold'] ?? null,
                        'total' => $item['total'] ?? 0,
                        'subtotal' => $subtotal,
                        'remark' => $item['remark'] ?? null,
                        'custom_reference' => $item['custom_reference'],
                        'pure_gold' => $pureGold,
                        'item_type' => $item['item_type'],
                        'quantity' => $item['quantity'] ?? null,
                        'pair' => $item['pair'],
                        'unit_price' => $item['unit_price'] ?? null,
                        'remark_total' => $item['remark_total'] ?? 0,
                        'kt' => $kt,
                        'status' => '0'
                    ]);

                    Log::info('Created credit note item', [
                        'credit_note_id' => $credit_note->id,
                        'item_reference' => $item['reference_no']
                    ]);
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Credit note created successfully',
                    'redirect' => route('credit_notes.index')
                ]);
            }

            return redirect()
                ->route('credit_notes.index')
                ->with('success', 'Credit note created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating credit note', [
                'error' => $e->getMessage()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['error' => [$e->getMessage()]]
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating credit note: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $credit_note = CreditNote::with([
            'creditItems' => function ($query) {
                $query->where('status', '0');
            },
            'creditItems.goldPrice',
            'invoice'
        ])->findOrFail($id);

        $taxDetails = $credit_note->creditItems->first() ?? new CreditNoteItem();

        // Get the grand total from the first item
        $subtotal = $credit_note->creditItems->first()->subtotal ?? 0;

        $invoices = Invoice::where('status', '0')->get();
        $products = ProductDetail::where('status', '0')->get();
        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'credit_reason')
            ->where('status', '0')
            ->get();

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        $ro = '';

        return view('credit_notes.edit', compact('credit_note', 'invoices', 'products', 'reasons', 'ro', 'subtotal', 'taxDetails', 'pair'));
    }

    public function update(CreditNoteFormRequest $request, $id)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            $credit_note = CreditNote::findOrFail($id);

            // Fetch the invoice_uuid from the selected invoice
            $invoice = Invoice::where('invoice_no', $request->invoice_no)->first();

            // Update credit note
            $credit_note->update([
                'credit_note_no' => $request->credit_note_no,
                'invoice_no' => $request->invoice_no,
                'original_invoice_uuid' => $invoice->invoice_uuid,
                'date' => $request->date,
                'note' => $request->note,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '02',
                'reason' => $request->reason,
                'status' => '0',
                'updated_by' => $user->id
            ]);

            // Calculate totals
            $subtotal = collect($request->items)->sum('total');

            // Get existing item IDs for tracking
            $existingIds = $credit_note->creditItems()
                ->where('status', '0')
                ->pluck('id')
                ->toArray();

            $submittedIds = [];

            // Update or create credit note items
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
                        $credit_note->creditItems()
                            ->where('id', $item['id'])
                            ->where('status', '0')
                            ->update([
                                'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                                'particulars' => $item['particulars'],
                                'weight' => $item['weight'],
                                'wastage' => $item['wastage'],
                                'total_weight' => $item['total_weight'],
                                'gold' => $item['gold'] ?? null,
                                'total' => $item['total'] ?? 0,
                                'subtotal' => $subtotal,
                                'remark' => $item['remark'] ?? null,
                                'custom_reference' => $item['custom_reference'],
                                'item_type' => $item['item_type'],
                                'quantity' => $item['quantity'] ?? null,
                                'pair' => $item['pair'],
                                'unit_price' => $item['unit_price'] ?? null,
                                'remark_total' => $item['remark_total'] ?? 0,
                                'pure_gold' => $pureGold,
                                'kt' => $kt,
                            ]);

                        $submittedIds[] = $item['id'];
                    } else {
                        // Create new item
                        $credit_note->creditItems()->create([
                            'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                            'particulars' => $item['particulars'],
                            'weight' => $item['weight'],
                            'wastage' => $item['wastage'],
                            'total_weight' => $item['total_weight'],
                            'gold' => $item['gold'] ?? null,
                            'total' => $item['total'] ?? 0,
                            'subtotal' => $subtotal,
                            'remark' => $item['remark'] ?? null,
                            'custom_reference' => $item['custom_reference'],
                            'pure_gold' => $pureGold,
                            'item_type' => $item['item_type'],
                            'quantity' => $item['quantity'] ?? null,
                            'pair' => $item['pair'],
                            'unit_price' => $item['unit_price'] ?? null,
                            'remark_total' => $item['remark_total'] ?? 0,
                            'kt' => $kt,
                            'status' => '0'
                        ]);
                    }

                    Log::info('Processed credit note item', [
                        'credit_note_id' => $credit_note->id,
                        'item_reference' => $item['reference_no']
                    ]);
                }
            }

            // Handle deletion of items that were intentionally removed from the form
            $deletedItems = [];
            if ($request->has('deleted_items') && !empty($request->deleted_items)) {
                $deletedItems = json_decode($request->deleted_items, true) ?? [];
            }
            
            if (!empty($deletedItems)) {
                $credit_note->creditItems()
                    ->whereIn('id', $deletedItems)
                    ->delete();
            }

            DB::commit();

            return redirect()
                ->route('credit_notes.index')
                ->with('success', 'Credit note updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating credit note', [
                'credit_note_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating credit note: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $credit_note = CreditNote::with([
            'creditItems' => function ($query) {
                $query->where('status', '0');
            },
            'invoice'
        ])->findOrFail($id);

        $taxDetails = $credit_note->creditItems->first() ?? new CreditNoteItem();

        // Get the grand total from the first item (since all items have the same grand total)
        $subtotal = $credit_note->creditItems->first()->subtotal ?? 0;

        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'credit_reason')
            ->where('status', '0')
            ->get();

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

        $invoices = Invoice::where('status', '0')->get();
        $products = ProductDetail::where('status', '0')->get();
        $states = Selection::select('id', 'selection_data')
            ->where('selection_type', 'state')
            ->where('status', '0')
            ->get();
        $ro = 'readonly'; // Changed to readonly for show view

        return view('credit_notes.show', compact(
            'credit_note',
            'allCompanies',
            'ro',
            'products',
            'states',
            'subtotal',
            'reasons',
            'invoices',
            'taxDetails',
            'pair'
        ));
    }

    public function destroy($id)
    {
        $user = Auth::user();

        CreditNote::where('id', $id)
            ->update([
                'updated_by' => $user->id,
                'status' => '1'
            ]);

        return redirect()->route('credit_notes.index')
            ->with('success', 'Credit Note deleted successfully.');
    }

    // Get invoice details for credit note
    public function getInvoiceDetails($invoice_no)
    {
        $invoice = Invoice::with(['invoiceItems' => function ($query) {
            $query->where('status', '0');
        }])->where('invoice_no', $invoice_no)
            ->first();

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        // Try to get address from CustomerProfile first
        $customerProfile = CustomerProfile::where('company_name', $invoice->company_name)
            ->where('status', '0')
            ->first();

        // Set address from CustomerProfile if found
        if ($customerProfile) {
            $stateName = Selection::where('selection_type', 'state')
                ->where('id', $customerProfile->state)
                ->where('status', '0')
                ->value('selection_data');

            $address = collect([
                $customerProfile->address_line_1 ?? '',
                $customerProfile->address_line_2 ?? '',
                $customerProfile->postcode ?? '',
                $customerProfile->city ?? '',
                $stateName ?? ''
            ])->filter()->join(', ');
        }
        // If not found in CustomerProfile, try OtherProfile
        else {
            $otherProfile = OtherProfile::where('company_name', $invoice->company_name)
                ->where('status', '0')
                ->first();

            if ($otherProfile) {
                $stateName = Selection::where('selection_type', 'state')
                    ->where('id', $otherProfile->state)
                    ->where('status', '0')
                    ->value('selection_data');

                $address = collect([
                    $otherProfile->address_line_1 ?? '',
                    $otherProfile->address_line_2 ?? '',
                    $otherProfile->postcode ?? '',
                    $otherProfile->city ?? '',
                    $stateName ?? ''
                ])->filter()->join(', ');
            }
        }

        // Transform invoice items to match credit note item structure
        $items = $invoice->invoiceItems->map(function ($item) {
            // Calculate return balance based on remark and total_weight if remark_total is empty or zero
            $remark_total = $item->remark_total;
            if (empty($remark_total) || $remark_total == 0) {
                $total_weight = $item->total_weight ?? 0;
                $remark = $item->remark ?? '';
                
                if (strpos($remark, '916') !== false) {
                    $remark_total = $total_weight * 0.95;
                } elseif (strpos($remark, '835') !== false) {
                    $remark_total = $total_weight * 0.87;
                } elseif (strpos($remark, '750W') !== false) {
                    $remark_total = $total_weight * 0.78;
                } elseif (strpos($remark, '750R') !== false) {
                    $remark_total = $total_weight * 0.78;
                } elseif (strpos($remark, '750Y') !== false) {
                    $remark_total = $total_weight * 0.78;
                } elseif (strpos($remark, '375W') !== false) {
                    $remark_total = $total_weight * 0.40;
                } elseif (strpos($remark, '375R') !== false) {
                    $remark_total = $total_weight * 0.40;
                }
            }
            
            return [
                'reference_no' => $item->reference_no,
                'particulars' => $item->particulars,
                'weight' => $item->weight,
                'wastage' => $item->wastage,
                'total_weight' => $item->total_weight,
                'gold' => $item->gold,
                'workmanship' => $item->workmanship,
                'total' => $item->total,
                'subtotal' => $item->subtotal,
                'custom_reference' => $item->custom_reference,
                'item_type' => $item->item_type,
                'pure_gold' => $item->pure_gold,
                'remark' => $item->remark,
                'quantity' => $item->quantity,
                'pair' => $item->pair,
                'unit_price' => $item->unit_price,
                'remark_total' => $remark_total,
                'kt' => $item->kt,
            ];
        });

        return response()->json([
            'invoice_no' => $invoice->invoice_no,
            'original_invoice_uuid' => $invoice->invoice_uuid,
            'company_name' => $invoice->company_name,
            'address' => $address ?? '',
            'invoice_date' => $invoice->invoice_date,
            'goods_received_by' => $invoice->goods_received_by,
            'payment_received_by' => $invoice->payment_received_by,
            'items' => $items,
        ]);
    }
}
