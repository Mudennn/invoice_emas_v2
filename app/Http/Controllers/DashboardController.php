<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CustomerProfile;
use App\Models\OtherProfile;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $statistics = [
            'totalInvoices' => Invoice::where('status', '0')->count(),
            'totalPayments' => DB::table('payments')
                ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->where('payments.status', '0')
                ->where('invoices.status', '0')
                ->sum('payments.total_payment'),
            'totalCustomers' => CustomerProfile::where('status', '0')->count(),
            'totalOtherCustomers' => OtherProfile::count(),
        ];

        // Get payment data for the last 6 months
        $chartData = $this->getPaymentTrendsData();

        return view('dashboard.index', array_merge($statistics, ['chartData' => $chartData]));
    }

    private function getPaymentTrendsData(): array
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

        $totalPayment = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('payments.status', '0')
            ->where('invoices.status', '0')
            ->sum('payments.total_payment');

        // Create an array of the last 6 months
        $dates = [];
        $amounts = [];

        // Add current month's data
        $dates[] = Carbon::now()->format('M Y');
        $amounts[] = (float) $totalPayment;

        return [
            'dates' => $dates,
            'amounts' => $amounts
        ];
    }
}