<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\CustomerProfile;
use App\Models\CompanyProfile;
use App\Models\Is;
use App\Models\BalanceAdjustment;
use App\Models\CreditNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        // Get all unique company names from customer_profiles
        $customerCompanies = CustomerProfile::where('status', '0')
            ->pluck('company_name')
            ->unique()
            ->values()
            ->all();

        return view('reports.index', compact('customerCompanies'));
    }

    public function getSalesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'companies' => 'nullable|array',
        ]);

        // Get all unique company names from customer_profiles
        $allCustomerCompanies = CustomerProfile::where('status', '0')
            ->pluck('company_name')
            ->unique()
            ->values();

        // Filter companies based on selection (if provided)
        $selectedCompanies = $request->input('companies', []);
        if (!empty($selectedCompanies)) {
            $customerCompanies = $allCustomerCompanies->filter(function ($company) use ($selectedCompanies) {
                return in_array($company, $selectedCompanies);
            })->values();
        } else {
            $customerCompanies = $allCustomerCompanies;
        }

        // First, get invoices with aggregated data from items
        $invoiceData = DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->select([
                'invoices.id',
                'invoices.invoice_no as document_no',
                'invoices.company_name',
                DB::raw('SUM(invoice_items.total) as total_amount'),
                DB::raw('SUM(invoice_items.gold) as total_gold_price'),
                DB::raw('SUM(COALESCE(invoice_items.remark_total, 0)) as total_remark_total'),
                DB::raw('SUM(invoice_items.workmanship) as total_workmanship'),
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.unit_price) as total_unit_price'),
                DB::raw("'Invoice' as document_type")
            ])
            ->where('invoices.status', '0')
            ->where('invoice_items.status', '0')
            ->whereBetween('invoices.invoice_date', [$request->start_date, $request->end_date])
            ->groupBy('invoices.id', 'invoices.invoice_no', 'invoices.company_name');

        // Get credit notes with aggregated data from items
        $creditNoteData = DB::table('credit_notes')
            ->join('credit_note_items', 'credit_notes.id', '=', 'credit_note_items.credit_note_id')
            ->join('invoices', 'credit_notes.invoice_no', '=', 'invoices.invoice_no')
            ->select([
                'credit_notes.id',
                'credit_notes.credit_note_no as document_no',
                'invoices.company_name',
                DB::raw('SUM(credit_note_items.total) * -1 as total_amount'),
                DB::raw('SUM(credit_note_items.gold) * -1 as total_gold_price'),
                DB::raw('SUM(COALESCE(credit_note_items.remark_total, 0)) * -1 as total_remark_total'),
                DB::raw('SUM(credit_note_items.workmanship) * -1 as total_workmanship'),
                DB::raw('SUM(credit_note_items.quantity) * -1 as total_quantity'),
                DB::raw('SUM(credit_note_items.unit_price) * -1 as total_unit_price'),
                DB::raw("'Credit Note' as document_type")
            ])
            ->where('credit_notes.status', '0')
            ->where('credit_note_items.status', '0')
            ->whereBetween('credit_notes.date', [$request->start_date, $request->end_date])
            ->groupBy('credit_notes.id', 'credit_notes.credit_note_no', 'invoices.company_name');

        // Combine invoices and credit notes
        $combinedData = $invoiceData->union($creditNoteData)
            ->orderBy('document_no')
            ->get();

        // Convert to collection and add dynamic columns
        $salesReport = $combinedData->map(function ($document) use ($customerCompanies) {
            // Create a new object with the required properties
            $report = (object) [
                'invoice_no' => $document->document_no,
                'document_type' => $document->document_type,
                'amount' => $document->total_amount,
                'gold_price' => $document->total_gold_price,
                'remark_total' => $document->total_remark_total,
                'workmanship' => $document->total_workmanship,
                'quantity' => $document->total_quantity,
                'unit_price' => $document->total_unit_price,
            ];

            // Check if company is a customer
            $isCustomer = $customerCompanies->contains($document->company_name);

            // Add customer company columns
            foreach ($customerCompanies as $company) {
                $safeCompanyName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($company));
                $safeCompanyName = rtrim($safeCompanyName, '_');
                $report->$safeCompanyName = ($document->company_name === $company) ? $document->total_amount : 0;
            }

            // Add others column
            $report->others = $isCustomer ? 0 : $document->total_amount;

            return $report;
        });

        return view('reports.index', compact('salesReport', 'customerCompanies'))
            ->with('allCustomerCompanies', $allCustomerCompanies);
    }

    public function printSalesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'companies' => 'nullable|array',
        ]);

        // Get all unique company names from customer_profiles
        $allCustomerCompanies = CustomerProfile::where('status', '0')
            ->pluck('company_name')
            ->unique()
            ->values();

        // Filter companies based on selection (if provided)
        $selectedCompanies = $request->input('companies', []);
        if (!empty($selectedCompanies)) {
            $customerCompanies = $allCustomerCompanies->filter(function ($company) use ($selectedCompanies) {
                return in_array($company, $selectedCompanies);
            })->values();
        } else {
            $customerCompanies = $allCustomerCompanies;
        }

        // First, get invoices with aggregated data from items
        $invoiceData = DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->select([
                'invoices.id',
                'invoices.invoice_no as document_no',
                'invoices.company_name',
                DB::raw('SUM(invoice_items.total) as total_amount'),
                DB::raw('SUM(invoice_items.gold) as total_gold_price'),
                DB::raw('SUM(COALESCE(invoice_items.remark_total, 0)) as total_remark_total'),
                DB::raw('SUM(invoice_items.workmanship) as total_workmanship'),
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.unit_price) as total_unit_price'),
                DB::raw("'Invoice' as document_type")
            ])
            ->where('invoices.status', '0')
            ->where('invoice_items.status', '0')
            ->whereBetween('invoices.invoice_date', [$request->start_date, $request->end_date])
            ->groupBy('invoices.id', 'invoices.invoice_no', 'invoices.company_name');

        // Get credit notes with aggregated data from items
        $creditNoteData = DB::table('credit_notes')
            ->join('credit_note_items', 'credit_notes.id', '=', 'credit_note_items.credit_note_id')
            ->join('invoices', 'credit_notes.invoice_no', '=', 'invoices.invoice_no')
            ->select([
                'credit_notes.id',
                'credit_notes.credit_note_no as document_no',
                'invoices.company_name',
                DB::raw('SUM(credit_note_items.total) * -1 as total_amount'),
                DB::raw('SUM(credit_note_items.gold) * -1 as total_gold_price'),
                DB::raw('SUM(COALESCE(credit_note_items.remark_total, 0)) * -1 as total_remark_total'),
                DB::raw('SUM(credit_note_items.workmanship) * -1 as total_workmanship'),
                DB::raw('SUM(credit_note_items.quantity) * -1 as total_quantity'),
                DB::raw('SUM(credit_note_items.unit_price) * -1 as total_unit_price'),
                DB::raw("'Credit Note' as document_type")
            ])
            ->where('credit_notes.status', '0')
            ->where('credit_note_items.status', '0')
            ->whereBetween('credit_notes.date', [$request->start_date, $request->end_date])
            ->groupBy('credit_notes.id', 'credit_notes.credit_note_no', 'invoices.company_name');

        // Combine invoices and credit notes
        $combinedData = $invoiceData->union($creditNoteData)
            ->orderBy('document_no')
            ->get();

        // Convert to collection and add dynamic columns
        $salesReport = $combinedData->map(function ($document) use ($customerCompanies) {
            // Create a new object with the required properties
            $report = (object) [
                'invoice_no' => $document->document_no,
                'document_type' => $document->document_type,
                'amount' => $document->total_amount,
                'gold_price' => $document->total_gold_price,
                'remark_total' => $document->total_remark_total,
                'workmanship' => $document->total_workmanship,
                'quantity' => $document->total_quantity,
                'unit_price' => $document->total_unit_price,
            ];

            // Check if company is a customer
            $isCustomer = $customerCompanies->contains($document->company_name);

            // Add customer company columns
            foreach ($customerCompanies as $company) {
                $safeCompanyName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($company));
                $safeCompanyName = rtrim($safeCompanyName, '_');
                $report->$safeCompanyName = ($document->company_name === $company) ? $document->total_amount : 0;
            }

            // Add others column
            $report->others = $isCustomer ? 0 : $document->total_amount;

            return $report;
        });

        // Get company profile for header
        $ourCompany = CompanyProfile::first();

        return view('reports.print', compact('salesReport', 'customerCompanies', 'ourCompany'))
            ->with('startDate', $request->start_date)
            ->with('endDate', $request->end_date);
    }

    // ------------------------------------------------------------------------------------------------

    // Company Sales Report
    public function companyReport()
    {
        // Get companies from both customer_profiles and others
        $customerCompanies = CustomerProfile::where('status', '0')
            ->pluck('company_name')
            ->toArray();
            
        $otherCompanies = DB::table('others')
            ->where('status', '0')
            ->pluck('company_name')
            ->toArray();

        // Merge and get unique companies
        $companies = collect(array_merge($customerCompanies, $otherCompanies))
            ->unique()
            ->values()
            ->all();

        return view('reports.company', compact('companies'));
    }

    public function getCompanySalesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'company' => 'required|string',
        ]);

        // Get invoice data
        $invoiceData = Invoice::query()
            ->select([
                'invoices.id',
                'invoices.invoice_no as document_no',
                'invoices.invoice_date as document_date',
                DB::raw('(SELECT subtotal FROM invoice_items WHERE invoice_id = invoices.id LIMIT 1) as subtotal'),
                DB::raw('(SELECT COALESCE(SUM(total_payment), 0) FROM payments WHERE invoice_id = invoices.id AND status = "0") as total_payment'),
                DB::raw('((SELECT subtotal FROM invoice_items WHERE invoice_id = invoices.id LIMIT 1) - COALESCE((SELECT SUM(total_payment) FROM payments WHERE invoice_id = invoices.id AND status = "0"), 0)) as balance'),
                DB::raw("'Invoice' as document_type"),
                DB::raw('NULL as amount')
            ])
            ->where('invoices.status', '0')
            ->where('invoices.company_name', $request->company)
            ->whereBetween('invoices.invoice_date', [$request->start_date, $request->end_date]);

        // Get credit note data
        $creditNoteData = CreditNote::query()
            ->join('invoices', 'credit_notes.invoice_no', '=', 'invoices.invoice_no')
            ->select([
                'credit_notes.id',
                'credit_notes.credit_note_no as document_no',
                'credit_notes.date as document_date',
                DB::raw('(SELECT subtotal FROM credit_note_items WHERE credit_note_id = credit_notes.id LIMIT 1) * -1 as subtotal'),
                DB::raw('0 as total_payment'),
                DB::raw('(SELECT subtotal FROM credit_note_items WHERE credit_note_id = credit_notes.id LIMIT 1) * -1 as balance'),
                DB::raw("'Credit Note' as document_type"),
                DB::raw('(SELECT subtotal FROM credit_note_items WHERE credit_note_id = credit_notes.id LIMIT 1) as amount')
            ])
            ->where('credit_notes.status', '0')
            ->where('invoices.company_name', $request->company)
            ->whereBetween('credit_notes.date', [$request->start_date, $request->end_date]);

        // Combine invoice and credit note data
        $salesReport = $invoiceData->union($creditNoteData)
            ->orderBy('document_date')
            ->orderBy('document_no')
            ->get();

        // Get companies from both customer_profiles and others
        $customerCompanies = CustomerProfile::where('status', '0')
            ->pluck('company_name')
            ->toArray();

        $otherCompanies = DB::table('others')
            ->where('status', '0')
            ->pluck('company_name')
            ->toArray();

        // Merge and get unique companies
        $companies = collect(array_merge($customerCompanies, $otherCompanies))
            ->unique()
            ->values()
            ->all();

        return view('reports.company', compact('salesReport', 'companies'));
    }

    public function printCompanySalesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'company' => 'required|string',
        ]);

        // Get invoice data
        $invoiceData = Invoice::query()
            ->select([
                'invoices.id',
                'invoices.invoice_no as document_no',
                'invoices.invoice_date as document_date',
                DB::raw('(SELECT subtotal FROM invoice_items WHERE invoice_id = invoices.id LIMIT 1) as subtotal'),
                DB::raw('(SELECT COALESCE(SUM(total_payment), 0) FROM payments WHERE invoice_id = invoices.id AND status = "0") as total_payment'),
                DB::raw('((SELECT subtotal FROM invoice_items WHERE invoice_id = invoices.id LIMIT 1) - COALESCE((SELECT SUM(total_payment) FROM payments WHERE invoice_id = invoices.id AND status = "0"), 0)) as balance'),
                DB::raw("'Invoice' as document_type"),
                DB::raw('NULL as amount')
            ])
            ->where('invoices.status', '0')
            ->where('invoices.company_name', $request->company)
            ->whereBetween('invoices.invoice_date', [$request->start_date, $request->end_date]);

        // Get credit note data
        $creditNoteData = CreditNote::query()
            ->join('invoices', 'credit_notes.invoice_no', '=', 'invoices.invoice_no')
            ->select([
                'credit_notes.id',
                'credit_notes.credit_note_no as document_no',
                'credit_notes.date as document_date',
                DB::raw('(SELECT subtotal FROM credit_note_items WHERE credit_note_id = credit_notes.id LIMIT 1) * -1 as subtotal'),
                DB::raw('0 as total_payment'),
                DB::raw('(SELECT subtotal FROM credit_note_items WHERE credit_note_id = credit_notes.id LIMIT 1) * -1 as balance'),
                DB::raw("'Credit Note' as document_type"),
                DB::raw('(SELECT subtotal FROM credit_note_items WHERE credit_note_id = credit_notes.id LIMIT 1) as amount')
            ])
            ->where('credit_notes.status', '0')
            ->where('invoices.company_name', $request->company)
            ->whereBetween('credit_notes.date', [$request->start_date, $request->end_date]);

        // Combine invoice and credit note data
        $salesReport = $invoiceData->union($creditNoteData)
            ->orderBy('document_date')
            ->orderBy('document_no')
            ->get();

        // Get company profile for header
        $ourCompany = CompanyProfile::first();

        return view('reports.company_print', compact('salesReport', 'ourCompany'))
            ->with('startDate', $request->start_date)
            ->with('endDate', $request->end_date)
            ->with('companyName', $request->company);
    }

    // ------------------------------------------------------------------------------------------------

    // IS Report
    public function isReport()
    {
        // For IS Report, only show Habib Jewelry Manufacturing Sdn Bhd as combined option
        $companies = ['Habib Jewelry Manufacturing Sdn Bhd'];

        return view('reports.is_report', compact('companies'));
    }

    public function getIsReport(Request $request)
    {
        $request->validate([
            'company' => 'required|string',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $startDate = sprintf('%04d-%02d-01', $request->year, $request->month);
        $endDate = date('Y-m-t', strtotime($startDate));

        // Calculate balance carried forward from previous months
        $previousMonthEndDate = date('Y-m-d', strtotime($startDate . ' -1 day'));
        $carryForwardBalance = $this->calculateCarryForwardBalance($previousMonthEndDate);

        // Get IS data - convert weight to decimal for consistency
        $isData = Is::query()
            ->select([
                'is.is_no as document_no',
                'is.is_date as document_date',
                DB::raw('CAST(is.weight as DECIMAL(10,2)) as weight'),
                DB::raw('NULL as remark_total'),
                DB::raw("'IS' as document_type")
            ])
            ->where('is.status', '0')
            ->where('is.company_name', 'LIKE', '%Habib Jewelry Manufacturing%')
            ->whereBetween('is.is_date', [$startDate, $endDate]);

        // Get invoice data with remark_total - only include invoices with remark_total > 0
        $invoiceData = DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->select([
                'invoices.invoice_no as document_no',
                'invoices.invoice_date as document_date',
                DB::raw('NULL as weight'),
                DB::raw('SUM(CAST(COALESCE(invoice_items.remark_total, 0) as DECIMAL(10,2))) as remark_total'),
                DB::raw("'Invoice' as document_type")
            ])
            ->where('invoices.status', '0')
            ->where('invoice_items.status', '0')
            ->where('invoices.company_name', 'LIKE', '%Habib%')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->groupBy('invoices.id', 'invoices.invoice_no', 'invoices.invoice_date')
            ->havingRaw('SUM(CAST(COALESCE(invoice_items.remark_total, 0) as DECIMAL(10,2))) > 0');

        // Combine both queries and order by date
        $combinedData = $isData->union($invoiceData)
            ->orderBy('document_date')
            ->orderBy('document_no')
            ->get();

        // Calculate running balance starting with carry forward
        $runningBalance = $carryForwardBalance;
        $isReport = collect();

        // Check if there's a balance adjustment for this month
        $balanceAdjustment = BalanceAdjustment::where('company_name', 'LIKE', '%Habib Jewelry Manufacturing%')
            ->where('effective_date', $startDate)
            ->where('status', 1)
            ->first();

        // Add balance carried forward entry if there's a previous balance
        if ($carryForwardBalance != 0) {
            // If this month has a balance adjustment, show it as IN amount
            if ($balanceAdjustment) {
                $isReport->push((object) [
                    'document_date' => $startDate,
                    'document_no' => 'Balance B/F',
                    'document_type' => 'Balance',
                    'in_weight' => $balanceAdjustment->adjustment_amount,
                    'out_weight' => 0,
                    'balance' => $balanceAdjustment->adjustment_amount
                ]);
                // Reset running balance to start from the adjustment amount
                $runningBalance = $balanceAdjustment->adjustment_amount;
            } else {
                $isReport->push((object) [
                    'document_date' => $startDate,
                    'document_no' => 'Balance B/F',
                    'document_type' => 'Balance',
                    'in_weight' => 0,
                    'out_weight' => 0,
                    'balance' => $carryForwardBalance
                ]);
            }
        }

        // Process current month data
        $currentMonthReport = $combinedData->map(function ($item) use (&$runningBalance) {
            $inAmount = $item->document_type === 'IS' ? (float)($item->weight ?? 0) : 0;
            $outAmount = $item->document_type === 'Invoice' ? (float)($item->remark_total ?? 0) : 0;
            
            $runningBalance += $inAmount - $outAmount;
            
            return (object) [
                'document_date' => $item->document_date,
                'document_no' => $item->document_no,
                'document_type' => $item->document_type,
                'in_weight' => $inAmount,
                'out_weight' => $outAmount,
                'balance' => $runningBalance
            ];
        });

        // Merge balance carried forward with current month data
        $isReport = $isReport->merge($currentMonthReport);

        // For dropdown, only show Habib Jewelry Manufacturing
        $companies = ['Habib Jewelry Manufacturing Sdn Bhd'];

        return view('reports.is_report', compact('isReport', 'companies'));
    }

    public function printIsReport(Request $request)
    {
        $request->validate([
            'company' => 'required|string',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $startDate = sprintf('%04d-%02d-01', $request->year, $request->month);
        $endDate = date('Y-m-t', strtotime($startDate));

        // Calculate balance carried forward from previous months
        $previousMonthEndDate = date('Y-m-d', strtotime($startDate . ' -1 day'));
        $carryForwardBalance = $this->calculateCarryForwardBalance($previousMonthEndDate);

        // Get IS data - convert weight to decimal for consistency
        $isData = Is::query()
            ->select([
                'is.is_no as document_no',
                'is.is_date as document_date',
                DB::raw('CAST(is.weight as DECIMAL(10,2)) as weight'),
                DB::raw('NULL as remark_total'),
                DB::raw("'IS' as document_type")
            ])
            ->where('is.status', '0')
            ->where('is.company_name', 'LIKE', '%Habib Jewelry Manufacturing%')
            ->whereBetween('is.is_date', [$startDate, $endDate]);

        // Get invoice data with remark_total - show all invoices
        $invoiceData = DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->select([
                'invoices.invoice_no as document_no',
                'invoices.invoice_date as document_date',
                DB::raw('NULL as weight'),
                DB::raw('SUM(CAST(COALESCE(invoice_items.remark_total, 0) as DECIMAL(10,2))) as remark_total'),
                DB::raw("'Invoice' as document_type")
            ])
            ->where('invoices.status', '0')
            ->where('invoice_items.status', '0')
            ->where('invoices.company_name', 'LIKE', '%Habib%')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->groupBy('invoices.id', 'invoices.invoice_no', 'invoices.invoice_date')
            ->havingRaw('SUM(CAST(COALESCE(invoice_items.remark_total, 0) as DECIMAL(10,2))) > 0');

        // Combine both queries and order by date
        $combinedData = $isData->union($invoiceData)
            ->orderBy('document_date')
            ->orderBy('document_no')
            ->get();

        // Calculate running balance starting with carry forward
        $runningBalance = $carryForwardBalance;
        $isReport = collect();

        // Check if there's a balance adjustment for this month
        $balanceAdjustment = BalanceAdjustment::where('company_name', 'LIKE', '%Habib Jewelry Manufacturing%')
            ->where('effective_date', $startDate)
            ->where('status', 1)
            ->first();

        // Add balance carried forward entry if there's a previous balance
        if ($carryForwardBalance != 0) {
            // If this month has a balance adjustment, show it as IN amount
            if ($balanceAdjustment) {
                $isReport->push((object) [
                    'document_date' => $startDate,
                    'document_no' => 'Balance B/F',
                    'document_type' => 'Balance',
                    'in_weight' => $balanceAdjustment->adjustment_amount,
                    'out_weight' => 0,
                    'balance' => $balanceAdjustment->adjustment_amount
                ]);
                // Reset running balance to start from the adjustment amount
                $runningBalance = $balanceAdjustment->adjustment_amount;
            } else {
                $isReport->push((object) [
                    'document_date' => $startDate,
                    'document_no' => 'Balance B/F',
                    'document_type' => 'Balance',
                    'in_weight' => 0,
                    'out_weight' => 0,
                    'balance' => $carryForwardBalance
                ]);
            }
        }

        // Process current month data
        $currentMonthReport = $combinedData->map(function ($item) use (&$runningBalance) {
            $inAmount = $item->document_type === 'IS' ? (float)($item->weight ?? 0) : 0;
            $outAmount = $item->document_type === 'Invoice' ? (float)($item->remark_total ?? 0) : 0;
            
            $runningBalance += $inAmount - $outAmount;
            
            return (object) [
                'document_date' => $item->document_date,
                'document_no' => $item->document_no,
                'document_type' => $item->document_type,
                'in_weight' => $inAmount,
                'out_weight' => $outAmount,
                'balance' => $runningBalance
            ];
        });

        // Merge balance carried forward with current month data
        $isReport = $isReport->merge($currentMonthReport);

        // Get company profile for header
        $ourCompany = CompanyProfile::first();

        return view('reports.is_report_print', compact('isReport', 'ourCompany'))
            ->with('year', $request->year)
            ->with('month', $request->month)
            ->with('companyName', $request->company);
    }

    private function calculateCarryForwardBalance($endDate)
    {
        // Get the most recent balance adjustment before or on the end date
        $latestAdjustment = BalanceAdjustment::where('company_name', 'LIKE', '%Habib Jewelry Manufacturing%')
            ->where('effective_date', '<=', $endDate)
            ->where('status', 1)
            ->orderBy('effective_date', 'desc')
            ->first();

        // If no adjustment found, use the original calculation (starting from 0)
        if (!$latestAdjustment) {
            // Get all IS data up to end date for all Habib Jewelry Manufacturing variations
            $totalIS = Is::query()
                ->where('is.status', '0')
                ->where('is.company_name', 'LIKE', '%Habib Jewelry Manufacturing%')
                ->where('is.is_date', '<=', $endDate)
                ->sum(DB::raw('CAST(weight as DECIMAL(10,2))'));

            // Get all invoice remark_total up to end date for all Habib Jewelry Manufacturing variations
            $totalInvoiceRemark = DB::table('invoices')
                ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->where('invoices.status', '0')
                ->where('invoice_items.status', '0')
                ->where('invoices.company_name', 'LIKE', '%Habib%')
                ->where('invoices.invoice_date', '<=', $endDate)
                ->sum(DB::raw('CAST(COALESCE(invoice_items.remark_total, 0) as DECIMAL(10,2))'));

            return (float)($totalIS ?? 0) - (float)($totalInvoiceRemark ?? 0);
        }

        // Start with the adjustment amount as base balance
        $baseBalance = (float)$latestAdjustment->adjustment_amount;
        $adjustmentDate = $latestAdjustment->effective_date->format('Y-m-d');

        // Get all IS data from the adjustment date up to end date
        $totalIS = Is::query()
            ->where('is.status', '0')
            ->where('is.company_name', 'LIKE', '%Habib Jewelry Manufacturing%')
            ->where('is.is_date', '>=', $adjustmentDate)
            ->where('is.is_date', '<=', $endDate)
            ->sum(DB::raw('CAST(weight as DECIMAL(10,2))'));

        // Get all invoice remark_total from the adjustment date up to end date
        $totalInvoiceRemark = DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.status', '0')
            ->where('invoice_items.status', '0')
            ->where('invoices.company_name', 'LIKE', '%Habib%')
            ->where('invoices.invoice_date', '>=', $adjustmentDate)
            ->where('invoices.invoice_date', '<=', $endDate)
            ->sum(DB::raw('CAST(COALESCE(invoice_items.remark_total, 0) as DECIMAL(10,2))'));

        return $baseBalance + (float)($totalIS ?? 0) - (float)($totalInvoiceRemark ?? 0);
    }

    // ------------------------------------------------------------------------------------------------

    // Balance Adjustment Management Form
    public function balanceAdjustments()
    {
        $adjustments = BalanceAdjustment::where('status', 1)
            ->orderBy('effective_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.balance_adjustments.index', compact('adjustments'));
    }

    public function createBalanceAdjustment()
    {
        return view('reports.balance_adjustments.create');
    }

    public function storeBalanceAdjustment(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'company_name' => 'required|string|max:255',
            'effective_date' => 'required|date',
            'adjustment_amount' => 'required|numeric|min:0',
            'adjustment_type' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000'
        ]);

        BalanceAdjustment::create([
            'company_name' => $request->company_name,
            'effective_date' => $request->effective_date,
            'adjustment_amount' => $request->adjustment_amount,
            'adjustment_type' => $request->adjustment_type,
            'description' => $request->description,
            'status' => 1,
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        return redirect()->route('reports.balance-adjustments')
            ->with('success', 'Balance adjustment created successfully.');
    }

    public function editBalanceAdjustment($id)
    {
        $adjustment = BalanceAdjustment::findOrFail($id);
        return view('reports.balance_adjustments.edit', compact('adjustment'));
    }

    public function updateBalanceAdjustment(Request $request, $id)
    {
        $user = Auth::user();
        $request->validate([
            'company_name' => 'required|string|max:255',
            'effective_date' => 'required|date',
            'adjustment_amount' => 'required|numeric|min:0',
            'adjustment_type' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000'
        ]);

        $adjustment = BalanceAdjustment::findOrFail($id);
        $adjustment->update([
            'company_name' => $request->company_name,
            'effective_date' => $request->effective_date,
            'adjustment_amount' => $request->adjustment_amount,
            'adjustment_type' => $request->adjustment_type,
            'description' => $request->description,
            'updated_by' => $user->id
        ]);

        return redirect()->route('reports.balance-adjustments')
            ->with('success', 'Balance adjustment updated successfully.');
    }

    public function destroyBalanceAdjustment($id)
    {
        $user = Auth::user();
        $adjustment = BalanceAdjustment::findOrFail($id);
        $adjustment->update([
            'status' => 0,
            'updated_by' => $user->id
        ]);

        return redirect()->route('reports.balance-adjustments')
            ->with('success', 'Balance adjustment deactivated successfully.');
    }
}


