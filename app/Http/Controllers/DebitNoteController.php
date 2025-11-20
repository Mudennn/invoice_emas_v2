<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DebitNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\DebitNoteFormRequest;
use App\Models\Invoice;
use App\Models\ProductDetail;
use App\Models\GoldPrice;
use App\Models\CustomerProfile;
use App\Models\OtherProfile;
use App\Models\Selection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\DebitNoteItem;

class DebitNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $debit_notes = DebitNote::with('invoice')
            ->select('debit_notes.*')
            ->where('debit_notes.status', '0')
            ->orderBy('debit_notes.created_at', 'desc')
            ->get();

        return view('debit_notes.index', compact('debit_notes'));
    }

    public function view($id)
    {
        $debit_note = DebitNote::with([
            'debitItems' => function ($query) {
                $query->where('debit_note_items.status', '0')
                    ->leftJoin('selections', 'debit_note_items.pair', '=', 'selections.id')
                    ->select(
                        'debit_note_items.*',
                        'selections.selection_data as s_pair'
                    );
            },
            'invoice'
        ])->findOrFail($id);

        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'debit_reason')
            ->where('status', '0')
            ->get();

        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        $ro = 'readonly';

        return view('debit_notes.view', compact('debit_note', 'reasons', 'ro', 'pair'));
    }

    public function create()
    {
        $debit_note = new DebitNote();

        // Generate next invoice number
        $lastDebitNote = DebitNote::orderBy('debit_note_no', 'desc')->first();
        $nextDebitNoteNo = $lastDebitNote ? $this->generateNextDebitNoteNo($lastDebitNote->debit_note_no) : 'DN0001';
        $debit_note->debit_note_no = $nextDebitNoteNo;

        $invoices = Invoice::where('status', '0')->get();
        $products = ProductDetail::where('status', '0')->get();
        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'debit_reason')
            ->where('status', '0')
            ->get();
        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        $ro = '';

        return view('debit_notes.create', compact('debit_note', 'invoices', 'products', 'reasons', 'ro', 'pair'));
    }

    public function generateNextDebitNoteNo($lastDebitNoteNo): string
    {
        // Extract the numeric part
        $number = (int) substr($lastDebitNoteNo, 3);
        // Increment it
        $number++;
        // Format it back with leading zeros
        return 'DN' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    public function store(DebitNoteFormRequest $request)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Fetch the invoice_uuid from the selected invoice
            $invoice = Invoice::where('invoice_no', $request->invoice_no)->first();

            // Create debit note
            $debit_note = DebitNote::create([
                'debit_note_no' => $request->debit_note_no,
                'invoice_no' => $request->invoice_no,
                'original_invoice_uuid' => $invoice->invoice_uuid,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '03',
                'date' => $request->date,
                'note' => $request->note,
                'reason' => $request->reason,
                'status' => '0',
                'created_by' => $user->id
            ]);

            // Calculate totals
            $subtotal = collect($request->items)->sum('total');

            // Create debit note items
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
                        
                        $debit_note->debitItems()->create([
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
                            'remark_total' => $item['remark_total'] ?? 0,
                            'kt' => $kt,
                            'status' => '0',
                            'created_by' => $user->id,
                        ]);
                    } catch (\Exception $itemError) {
                        throw $itemError;
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('debit_notes.index')
                ->with('success', 'Debit note created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating debit note: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $debit_note = DebitNote::with([
            'debitItems' => function ($query) {
                $query->where('status', '0');
            },
            'invoice'
        ])->findOrFail($id);

        // Get the grand total from the first item (since all items have the same grand total)
        $subtotal = $debit_note->debitItems->first()->subtotal ?? 0;

        $taxDetails = $debit_note->debitItems->first() ?? new DebitNoteItem();

        $invoices = Invoice::where('status', '0')->get();
        $products = ProductDetail::where('status', '0')->get();
        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'debit_reason')
            ->where('status', '0')
            ->get();
        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();

        $ro = '';

        return view('debit_notes.edit', compact('debit_note', 'invoices', 'products', 'reasons', 'ro', 'subtotal', 'taxDetails', 'pair'));
    }

    public function update(DebitNoteFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $debit_note = DebitNote::findOrFail($id);

            // Fetch the invoice_uuid from the selected invoice
            $invoice = Invoice::where('invoice_no', $request->invoice_no)->first();

            // Update debit note
            $debit_note->update([
                'debit_note_no' => $request->debit_note_no,
                'invoice_no' => $request->invoice_no,
                'original_invoice_uuid' => $invoice->invoice_uuid,
                // 'einvoice_version' => 1.1,
                // 'type_code' => '03',
                'date' => $request->date,
                'note' => $request->note,
                'reason' => $request->reason,
                'status' => '0',
                'updated_by' => $user->id
            ]);

            // Calculate totals
            $subtotal = collect($request->items)->sum('total');

            // Get existing item IDs for tracking
            $existingIds = $debit_note->debitItems()
                ->where('status', '0')
                ->pluck('id')
                ->toArray();

            $submittedIds = [];


            // Update or create debit note items
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
                        $debit_note->debitItems()
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
                                'pure_gold' => $pureGold,
                                'item_type' => $item['item_type'],
                                'quantity' => $item['quantity'],
                                'pair' => $item['pair'],
                                'unit_price' => $item['unit_price'] ?? null,
                                'remark_total' => $item['remark_total'] ?? 0,
                                'kt' => $kt,
                            ]);

                        $submittedIds[] = $item['id'];
                    } else {
                        // Create new item
                        $debit_note->debitItems()->create([
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
                            'remark_total' => $item['remark_total'] ?? 0,
                            'kt' => $kt,
                            'status' => '0'
                        ]);
                    }

                    Log::info('Processed debit note item', [
                        'debit_note_id' => $debit_note->id,
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
                $debit_note->debitItems()
                    ->whereIn('id', $deletedItems)
                    ->delete();
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Debit note updated successfully',
                    'redirect' => route('debit_notes.index')
                ]);
            }

            return redirect()
                ->route('debit_notes.index')
                ->with('success', 'Debit note updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating debit note', [
                'debit_note_id' => $id,
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
                ->with('error', 'Error updating debit note: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $debit_note = DebitNote::with([
            'debitItems' => function ($query) {
                $query->where('status', '0');
            },
            'invoice'
        ])->findOrFail($id);

        // Get the grand total from the first item (since all items have the same grand total)
        $subtotal = $debit_note->debitItems->first()->subtotal ?? 0;

        $taxDetails = $debit_note->debitItems->first() ?? new DebitNoteItem();

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
        $reasons = Selection::select('id', 'selection_data')
            ->where('selection_type', 'debit_reason')
            ->where('status', '0')
            ->get();
        $pair = Selection::select('id', 'selection_data')
            ->where('selection_type', 'pair')
            ->where('status', '0')
            ->get();
        $ro = 'readonly'; // Changed to readonly for show view

        return view('debit_notes.show', compact(
            'debit_note',
            'allCompanies',
            'ro',
            'products',
            'states',
            'subtotal',
            'taxDetails',
            'reasons',
            'invoices',
            'pair'
        ));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        DebitNote::where('id', $id)
            ->update([
                'updated_by' => $user->id,
                'status' => '1'
            ]);

        return redirect()->route('debit_notes.index')
            ->with('success', 'Debit Note deleted successfully.');
    }

    // Get invoice details for debit note
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

        // Transform invoice items to match debit note item structure
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
                'remark' => $item->remark,
                'custom_reference' => $item->custom_reference,
                'pure_gold' => $item->pure_gold,
                'item_type' => $item->item_type,
                'quantity' => $item->quantity,
                'pair' => $item->pair,
                'unit_price' => $item->unit_price,
                'kt' => $item->kt,
                'remark_total' => $remark_total,
            ];
        });

        return response()->json([
            'invoice_no' => $invoice->invoice_no,
            'company_name' => $invoice->company_name,
            'address' => $address ?? '',
            'invoice_date' => $invoice->invoice_date,
            'goods_received_by' => $invoice->goods_received_by,
            'payment_received_by' => $invoice->payment_received_by,
            'items' => $items
        ]);
    }
}
