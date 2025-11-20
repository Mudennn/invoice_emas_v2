<style>
    /* Import Google Font */
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&display=swap');

    html,
    body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100vh;
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .chinese-text {
        font-family: 'Noto Sans TC', sans-serif !important;
        font-weight: 500;
        font-size: 1.4rem !important;
        color: rgb(0, 0, 85) !important;
    }

    /* ------------------------------ */
    .report-title {
        font-size: 0.688rem !important;
        font-weight: 700 !important;
        color: black !important;
        margin-bottom: 8px !important;
    }

    /* ------------------------------ */

    .no-title {
        text-align: center !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .invoice-container {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        margin: 0 auto;
        width: 1150px;
        min-height: 100vh;
        background-color: #f8f9fa !important;
        padding: 20px;
        box-sizing: border-box;
        position: relative;
    }

    .page-break {
        page-break-before: always;
    }

    .invoice-details {
        width: 100%;
        position: relative;
        background-color: #f8f9fa !important;
        padding: 20px;
        box-sizing: border-box;
        min-height: calc(100vh - 40px);
    }

    .header {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    /* ------------------------------ */
    /* next page header */
    .continuation-header {
        margin-bottom: 20px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .continuation-header .company-name {
        font-weight: 700 !important;
        font-size: 1.2rem;
    }

    /* ------------------------------ */

    .company-header {
        width: 100%;
    }

    .company-header .company-name {
        font-weight: 700 !important;
        color: rgb(0, 0, 85) !important;
        font-size: 1.4rem !important;
        text-transform: uppercase;
    }

    .company-header p {
        font-size: 0.8rem !important;
        color: rgb(0, 0, 85) !important;
    }

    .customer-details {
        margin-bottom: 20px;
        width: 100%;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .customer-details p,
    .right-side p {
        color: black !important;
        font-size: 0.688rem !important;
    }

    .customer-details h6,
    .right-side h6 {
        font-weight: 700 !important;
        color: black !important;
        font-size: 0.688rem !important;
    }

    .customer-details .right-side .invoice-no span {
        color: red !important;
        font-size: 1rem !important;
    }

    .page-number {
        margin-top: 5px;
        font-weight: bold;
    }

    /* .customer-details .center-side {
        text-align: center;
    } */

    /* .customer-details .center-side p {
        color: rgb(0, 0, 85) !important;
        font-size: 0.8rem !important;
    } */

    /* .customer-details .center-side .company-address {
        width: 70%;
        margin: 0 auto;
    } */

    .customer-details .right-side {
        text-align: right;
        width: 32%;
    }

    .customer-details .left-side {
        width: 32%;
    }

    .invoice-items {
        width: 100%;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }

    .totals-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .invoice-table th,
    .invoice-table td,
    .totals-table td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 11px;
    }

    .invoice-table th {
        background-color: #e9ecef !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        font-weight: bold;
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .total {
        text-align: right !important;
        font-weight: bold;
        background-color: #e9ecef !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .total-data {
        text-align: right !important;
    }

    .no-text {
        text-align: center !important;
    }

    .signature-section {
        display: flex;
        justify-content: space-between;
        margin-top: 100px;
        width: 100%;
        page-break-inside: avoid;
    }

    .signature-section p {
        font-size: 0.688rem !important;
        color: black !important;
    }

    .signature-box {
        width: 30%;
    }

    .signature-line {
        border-top: 1px solid #000;
        padding-top: 5px;
        text-align: center;
    }

    @media print {

        /* Ensure fonts are embedded in print */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .chinese-text {
            font-family: 'Noto Sans TC', sans-serif !important;
            font-weight: 500;
        }

        html,
        body {
            margin: 0;
            padding: 5px;
            width: 100%;
            height: 100%;
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* .header .company-header {
            color: gold !important;
        }

        .header .company-header .company-name {
            color: gold !important;
        }

        .header .company-header p {
            color: gold !important;
        } */

        .company-header {
            width: 100%;
        }

        .invoice-container {
            padding: 20px;
            margin: 0;
            box-sizing: border-box;
            background-color: #f8f9fa !important;
            min-height: 100%;
            width: 100%;
        }

        .invoice-details {
            background-color: #f8f9fa !important;
            margin: 0;
            padding: 15px;
            min-height: 100%;
        }

        .page-break {
            page-break-before: always;
        }

        .invoice-table,
        .totals-table {
            page-break-inside: avoid;
        }

        .invoice-items {
            page-break-inside: avoid;
        }

        @page {
            size: A4 landscape;
            margin: 0cm;
        }
    }

    /* .print-preview {
        width: 29.7cm;
        min-height: 21cm;
        padding: 1.5cm;
        margin: 1cm auto;
        border: 1px solid #D3D3D3;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        transform-origin: top left;
    }

    .print-preview .invoice-container {
        padding: 0;
        margin: 0;
        width: 100%;
    }

    .toggle-preview {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        z-index: 9999;
    } */
</style>
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create toggle button
        const button = document.createElement('button');
        button.className = 'toggle-preview';
        button.textContent = 'Toggle Print Preview';
        document.body.appendChild(button);

        // Add click handler
        button.addEventListener('click', function() {
            const container = document.querySelector('.invoice-container');
            container.classList.toggle('print-preview');
        });
    });
</script> --}}
