@extends('layouts.main')

@section('content')
    <h1 class="mt-4">Dashboard</h1>

    <div class="dashboard-content-box">
        <div class="main-dashboard-container">
            <!-- Total Customers Card -->
            <div class="main-dashboard-card">
                <span class="material-symbols-outlined customer-icon">
                    group
                </span>
                <h2 class="text-gray-500 text-sm">Total Customers</h2>
                <p>{{ ($totalCustomers ?? 0) + ($totalOtherCustomers ?? 0) }}</p>
            </div>

            <!-- Total Invoices Card -->
            <div class="main-dashboard-card">
                <span class="material-symbols-outlined invoice-icon">
                    contract_edit
                </span>
                <h2 class="text-gray-500 text-sm">Total Invoices</h2>
                <p>{{ $totalInvoices ?? 0 }}</p>
            </div>

            <!-- Total Payments Card -->
            <div class="main-dashboard-card">
                <span class="material-symbols-outlined payment-icon">
                    payments
                </span>
                <h2 class="text-gray-500 text-sm">Total Payments Made</h2>
                <p>RM {{ number_format($totalPayments ?? 0, 2) }}</p>
            </div>

            <!-- Balance Payments Card -->
            {{-- <div class="main-dashboard-card">
                <span class="material-symbols-outlined balance-icon">
                    wallet
                </span>
                <h2 class="text-gray-500 text-sm">Payment Voucher</h2>
                <p>RM {{ number_format($paymentVoucher ?? 0, 2) }}</p>
            </div> --}}
            {{--  <h2 class="text-gray-500 text-sm">Pending Payments</h2>
                <p>RM {{ number_format($balancePayments ?? 0, 2) }}</p> --}}

        </div>

        <!-- Payment Trends Chart -->
        <div class="chart-dashboard-card">
            <h2>Payment Trends</h2>
            @apexchartsScripts
            <div id="paymentTrendsChart"></div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var options = {
                    series: [{
                        name: 'Total Payments',
                        data: @json($chartData['amounts'])
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    grid: {
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    xaxis: {
                        categories: @json($chartData['dates']),
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return 'RM ' + value.toFixed(2)
                            }
                        }
                    },
                    colors: ['#7b64e3'],
                    title: {
                        text: 'Monthly Payment Trends',
                        align: 'left'
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return 'RM ' + value.toFixed(2)
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#paymentTrendsChart"), options);
                chart.render();
            });
        </script>
    @endpush
@endsection
