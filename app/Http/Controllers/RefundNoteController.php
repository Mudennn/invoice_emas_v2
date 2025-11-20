<?php

namespace App\Http\Controllers;

use App\Models\RefundNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RefundNoteFormRequest;
use App\Models\Invoice;
use App\Models\ProductDetail;
use App\Models\GoldPrice;
use App\Models\CustomerProfile;
use App\Models\OtherProfile;
use App\Models\Selection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\RefundNoteItem;

class RefundNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $refund_notes = RefundNote::with('invoice')
            ->select('refund_notes.*')
            ->where('refund_notes.status', '0')
            ->orderBy('refund_notes.created_at', 'desc')
            ->get();

        return view('refund_notes.index', compact('refund_notes'));
    }

    public function view($id)
    {
        $refund_note = RefundNote::with([
            'refundItems' => function ($query) {
                $query->where('refund_note_items.status', '0')
                ->leftJoin('selections', 'refund_note_items.pair', '=', 'selections.id')
                ->select(
                    'refund_note_items.*',
                    'selections.selection_data as s_pair'
                );
            },
            'invoice'
        ])->findOrFail($id);

        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'refund_reason')
            ->where('status', '0')
            ->get();

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        $ro = 'readonly';

        return view('refund_notes.view', compact('refund_note', 'reasons', 'ro', 'pair'));
    }

    public function create()
    {
        $refund_note = new RefundNote();

        // Generate next invoice number
        $lastRefundNote = RefundNote::orderBy('refund_note_no', 'desc')->first();
        $nextRefundNoteNo = $lastRefundNote ? $this->generateNextRefundNoteNo($lastRefundNote->refund_note_no) : 'RN0001';
        $refund_note->refund_note_no = $nextRefundNoteNo;

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

        return view('refund_notes.create', compact('refund_note', 'invoices', 'products', 'reasons', 'ro', 'pair'));
    }

    private function generateNextRefundNoteNo($lastRefundNoteNo): string
    {
        // Extract the numeric part
        $number = (int) substr($lastRefundNoteNo, 3);
        // Increment it
        $number++;
        // Format it back with leading zeros
        return 'RN' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    public function store(RefundNoteFormRequest $request)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Fetch the invoice_uuid from the selected invoice
            $invoice = Invoice::where('invoice_no', $request->invoice_no)->first();

            // Create credit note
            $refund_note = RefundNote::create([
                'refund_note_no' => $request->refund_note_no,
                'invoice_no' => $request->invoice_no,
                'original_invoice_uuid' => $invoice->invoice_uuid,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '04',
                'date' => $request->date,
                'note' => $request->note,
                'reason' => $request->reason,
                'status' => '0',
                'created_by' => $user->id,
            ]);



            // Calculate totals
            $subtotal = collect($request->items)->sum('total');

            // Create credit note items
            if ($request->has('items')) {
                foreach ($request->items as $index => $item) {
                    try {

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

                        $refund_note->refundItems()->create([
                            'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                            'particulars' => $item['particulars'],
                            'weight' => $item['weight'],
                            'wastage' => $item['wastage'],
                            'total_weight' => $item['total_weight'],
                            'gold' => $item['gold'] ?? null,
                            'workmanship' => str_contains($item['item_type'], 'with-gold') ? ($item['workmanship'] ?? null) : null,
                            'total' => $item['total'],
                            'subtotal' => $subtotal,
                            'remark' => $item['remark'] ?? null,
                            'custom_reference' => $item['custom_reference'],
                            'pure_gold' => $pureGold,
                            'item_type' => $item['item_type'],
                            'quantity' => $item['quantity'],
                            'pair' => $item['pair'],
                            'unit_price' => $item['unit_price'] ?? null,
                            'kt' => $kt,
                            'remark_total' => $item['remark_total'] ?? 0,
                            'status' => '0'
                        ]);
                    } catch (\Exception $itemError) {

                        throw $itemError;
                    }
                }
            }

            DB::commit();


            return redirect()
                ->route('refund_notes.index')
                ->with('success', 'Refund note created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating refund note: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $refund_note = RefundNote::with([
            'refundItems' => function ($query) {
                $query->where('status', '0');
            },
            'invoice'
        ])->findOrFail($id);

        $taxDetails = $refund_note->refundItems->first() ?? new RefundNoteItem();

        // Get the grand total from the first item
        $subtotal = $refund_note->refundItems->first()->subtotal ?? 0;

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

        return view('refund_notes.edit', compact('refund_note', 'invoices', 'products', 'reasons', 'ro', 'subtotal', 'taxDetails', 'pair'));
    }

    public function update(RefundNoteFormRequest $request, $id)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            $refund_note = RefundNote::findOrFail($id);

            // Fetch the invoice_uuid from the selected invoice
            $invoice = Invoice::where('invoice_no', $request->invoice_no)->first();

            // Update refund note
            $refund_note->update([
                'refund_note_no' => $request->refund_note_no,
                'invoice_no' => $request->invoice_no,
                'original_invoice_uuid' => $invoice->invoice_uuid,
                'date' => $request->date,
                'note' => $request->note,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '04',
                'reason' => $request->reason,
                'status' => '0',
                'updated_by' => $user->id
            ]);

            // Calculate totals
            $subtotal = collect($request->items)->sum('total');

            // Get existing item IDs for tracking
            $existingIds = $refund_note->refundItems()
                ->where('status', '0')
                ->pluck('id')
                ->toArray();

            $submittedIds = [];

            // Update or create refund note items
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
                        $refund_note->refundItems()
                            ->where('id', $item['id'])
                            ->where('status', '0')
                            ->update([
                                'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                                'particulars' => $item['particulars'],
                                'weight' => $item['weight'],
                                'wastage' => $item['wastage'],
                                'total_weight' => $item['total_weight'],
                                'gold' => $item['gold'] ?? null,
                                'workmanship' => str_contains($item['item_type'], 'with-gold') ? ($item['workmanship'] ?? null) : null,
                                'total' => $item['total'] ?? 0,
                                'subtotal' => $subtotal,
                                'remark' => $item['remark'] ?? null,
                                'custom_reference' => $item['custom_reference'],
                                'item_type' => $item['item_type'],
                                'quantity' => $item['quantity'],
                                'unit_price' => $item['unit_price'] ?? null,
                                'kt' => $kt,
                                'pair' => $item['pair'],
                                'remark_total' => $item['remark_total'] ?? 0,
                                'pure_gold' => $pureGold,
                            ]);

                        $submittedIds[] = $item['id'];
                    } else {
                        // Create new item
                        $refund_note->refundItems()->create([
                            'reference_no' => !empty($item['reference_no']) ? 'WO-' . ltrim($item['reference_no'], 'WO-') : null,
                            'particulars' => $item['particulars'],
                            'weight' => $item['weight'],
                            'wastage' => $item['wastage'],
                            'total_weight' => $item['total_weight'],
                            'gold' => $item['gold'] ?? null,
                            'workmanship' => str_contains($item['item_type'], 'with-gold') ? ($item['workmanship'] ?? null) : null,
                            'total' => $item['total'],
                            'subtotal' => $subtotal,
                            'remark' => $item['remark'] ?? null,
                            'custom_reference' => $item['custom_reference'],
                            'pure_gold' => $pureGold,
                            'item_type' => $item['item_type'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'] ?? null,
                            'kt' => $kt,
                            'pair' => $item['pair'],
                            'remark_total' => $item['remark_total'] ?? 0,
                            'status' => '0'
                        ]);
                    }

                    Log::info('Processed refund note item', [
                        'refund_note_id' => $refund_note->id,
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
                $refund_note->refundItems()
                    ->whereIn('id', $deletedItems)
                    ->delete();
            }

            DB::commit();

            return redirect()
                ->route('refund_notes.index')
                ->with('success', 'Refund note updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating refund note', [
                'refund_note_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating refund note: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $refund_note = RefundNote::with([
            'refundItems' => function ($query) {
                $query->where('status', '0');
            },
            'invoice'
        ])->findOrFail($id);

        $taxDetails = $refund_note->refundItems->first() ?? new RefundNoteItem();

        // Get the grand total from the first item (since all items have the same grand total)
        $subtotal = $refund_note->refundItems->first()->subtotal ?? 0;

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

        return view('refund_notes.show', compact(
            'refund_note',
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

        RefundNote::where('id', $id)
            ->update([
                'updated_by' => $user->id,
                'status' => '1'
            ]);

        return redirect()->route('refund_notes.index')
            ->with('success', 'Refund Note deleted successfully.');
    }

    // Get invoice details for refund note
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
                'unit_price' => $item->unit_price,
                'pair' => $item->pair,
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
