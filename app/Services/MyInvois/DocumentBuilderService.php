<?php

namespace App\Services\MyInvois;

use App\Models\CompanyProfile;
use App\Models\CreditNote;
use App\Models\CustomerProfile;
use App\Models\DebitNote;
use App\Models\Invoice;
use App\Models\RefundNote;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DocumentBuilderService
{
    /**
     * Build Invoice document in UBL 2.1 JSON format
     */
    public function buildInvoiceDocument(Invoice $invoice): array
    {
        Log::channel('myinvois')->info('Building invoice document', ['invoice_id' => $invoice->id]);

        $company = $this->getCompanyProfile();
        $customer = $this->getCustomerProfile($invoice->company_name);
        $items = $invoice->invoiceItems()->where('status', '0')->get();

        $document = [
            '_D' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            '_A' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            '_B' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            'Invoice' => [[
                'ID' => [['_' => $invoice->invoice_no]],
                'IssueDate' => [['_' => Carbon::parse($invoice->invoice_date)->format('Y-m-d')]],
                'IssueTime' => [['_' => Carbon::parse($invoice->invoice_date)->format('H:i:s').'Z']],
                'InvoiceTypeCode' => [
                    [
                        '_' => '01',
                        'listVersionID' => '1.0',
                    ],
                ],
                'DocumentCurrencyCode' => [['_' => $items->first()->currency_code ?? 'MYR']],

                // Issuer (Supplier/Company)
                'AccountingSupplierParty' => [$this->buildSupplierParty($company)],

                // Receiver (Customer/Buyer)
                'AccountingCustomerParty' => [$this->buildCustomerParty($customer)],

                // Line Items
                'InvoiceLine' => $this->buildInvoiceLines($items),

                // Totals
                'LegalMonetaryTotal' => [$this->calculateInvoiceTotals($items)],

                // Tax Total
                'TaxTotal' => [$this->calculateTaxTotal($items)],
            ]],
        ];

        return $document;
    }

    /**
     * Build Credit Note document in UBL 2.1 JSON format
     */
    public function buildCreditNoteDocument(CreditNote $creditNote): array
    {
        Log::channel('myinvois')->info('Building credit note document', ['credit_note_id' => $creditNote->id]);

        $company = $this->getCompanyProfile();
        $invoice = $creditNote->invoice;
        $customer = $this->getCustomerProfile($invoice->company_name);
        $items = $creditNote->creditItems;

        $document = [
            '_D' => 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2',
            '_A' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            '_B' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            'CreditNote' => [[
                'ID' => [['_' => $creditNote->credit_note_no]],
                'IssueDate' => [['_' => Carbon::parse($creditNote->date)->format('Y-m-d')]],
                'IssueTime' => [['_' => Carbon::parse($creditNote->date)->format('H:i:s').'Z']],
                'CreditNoteTypeCode' => [
                    [
                        '_' => '02',
                        'listVersionID' => '1.0',
                    ],
                ],
                'DocumentCurrencyCode' => [['_' => 'MYR']],

                // Reference to original invoice
                'BillingReference' => [[
                    'InvoiceDocumentReference' => [[
                        'ID' => [['_' => $invoice->invoice_no]],
                    ]],
                ]],

                // Issuer
                'AccountingSupplierParty' => [$this->buildSupplierParty($company)],

                // Receiver
                'AccountingCustomerParty' => [$this->buildCustomerParty($customer)],

                // Line Items
                'CreditNoteLine' => $this->buildCreditNoteLines($items),

                // Totals
                'LegalMonetaryTotal' => [$this->calculateCreditNoteTotals($items)],

                // Tax Total
                'TaxTotal' => [$this->calculateCreditNoteTaxTotal($items)],
            ]],
        ];

        return $document;
    }

    /**
     * Build Debit Note document in UBL 2.1 JSON format
     */
    public function buildDebitNoteDocument(DebitNote $debitNote): array
    {
        Log::channel('myinvois')->info('Building debit note document', ['debit_note_id' => $debitNote->id]);

        $company = $this->getCompanyProfile();
        $invoice = $debitNote->invoice;
        $customer = $this->getCustomerProfile($invoice->company_name);
        $items = $debitNote->debitItems;

        $document = [
            '_D' => 'urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2',
            '_A' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            '_B' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            'DebitNote' => [[
                'ID' => [['_' => $debitNote->debit_note_no]],
                'IssueDate' => [['_' => Carbon::parse($debitNote->date)->format('Y-m-d')]],
                'IssueTime' => [['_' => Carbon::parse($debitNote->date)->format('H:i:s').'Z']],
                'DebitNoteTypeCode' => [
                    [
                        '_' => '03',
                        'listVersionID' => '1.0',
                    ],
                ],
                'DocumentCurrencyCode' => [['_' => 'MYR']],

                // Reference to original invoice
                'BillingReference' => [[
                    'InvoiceDocumentReference' => [[
                        'ID' => [['_' => $invoice->invoice_no]],
                    ]],
                ]],

                // Issuer
                'AccountingSupplierParty' => [$this->buildSupplierParty($company)],

                // Receiver
                'AccountingCustomerParty' => [$this->buildCustomerParty($customer)],

                // Line Items
                'DebitNoteLine' => $this->buildDebitNoteLines($items),

                // Totals
                'RequestedMonetaryTotal' => [$this->calculateDebitNoteTotals($items)],

                // Tax Total
                'TaxTotal' => [$this->calculateDebitNoteTaxTotal($items)],
            ]],
        ];

        return $document;
    }

    /**
     * Build Refund Note document in UBL 2.1 JSON format
     */
    public function buildRefundNoteDocument(RefundNote $refundNote): array
    {
        Log::channel('myinvois')->info('Building refund note document', ['refund_note_id' => $refundNote->id]);

        $company = $this->getCompanyProfile();
        $invoice = $refundNote->invoice;
        $customer = $this->getCustomerProfile($invoice->company_name);
        $items = $refundNote->refundItems;

        $document = [
            '_D' => 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2',
            '_A' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            '_B' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            'CreditNote' => [[
                'ID' => [['_' => $refundNote->refund_note_no]],
                'IssueDate' => [['_' => Carbon::parse($refundNote->date)->format('Y-m-d')]],
                'IssueTime' => [['_' => Carbon::parse($refundNote->date)->format('H:i:s').'Z']],
                'CreditNoteTypeCode' => [
                    [
                        '_' => '04',
                        'listVersionID' => '1.0',
                    ],
                ],
                'DocumentCurrencyCode' => [['_' => 'MYR']],

                // Reference to original invoice
                'BillingReference' => [[
                    'InvoiceDocumentReference' => [[
                        'ID' => [['_' => $invoice->invoice_no]],
                    ]],
                ]],

                // Issuer
                'AccountingSupplierParty' => [$this->buildSupplierParty($company)],

                // Receiver
                'AccountingCustomerParty' => [$this->buildCustomerParty($customer)],

                // Line Items
                'CreditNoteLine' => $this->buildRefundNoteLines($items),

                // Totals
                'LegalMonetaryTotal' => [$this->calculateRefundNoteTotals($items)],

                // Tax Total
                'TaxTotal' => [$this->calculateRefundNoteTaxTotal($items)],
            ]],
        ];

        return $document;
    }

    /**
     * Build Supplier Party (Company/Issuer)
     */
    private function buildSupplierParty(CompanyProfile $company): array
    {
        return [
            'Party' => [[
                'IndustryClassificationCode' => [['_' => $company->msic_code]],
                'PartyIdentification' => [[
                    'ID' => [
                        [
                            '_' => $company->tin,
                            'schemeID' => 'TIN',
                        ],
                    ],
                ]],
                'PostalAddress' => [[
                    'CityName' => [['_' => $company->city]],
                    'PostalZone' => [['_' => $company->postcode]],
                    'CountrySubentityCode' => [['_' => $this->getStateCode($company->state)]],
                    'AddressLine' => [
                        ['Line' => [['_' => $company->address_line_1]]],
                        ['Line' => [['_' => $company->address_line_2]]],
                    ],
                    'Country' => [[
                        'IdentificationCode' => [
                            [
                                '_' => 'MYS',
                                'listID' => 'ISO3166-1',
                                'listAgencyID' => '6',
                            ],
                        ],
                    ]],
                ]],
                'PartyLegalEntity' => [[
                    'RegistrationName' => [['_' => $company->company_name]],
                ]],
                'Contact' => [[
                    'Telephone' => [['_' => $company->contact]],
                    'ElectronicMail' => [['_' => $company->email]],
                ]],
            ]],
        ];
    }

    /**
     * Build Customer Party (Buyer/Receiver)
     */
    private function buildCustomerParty(CustomerProfile $customer): array
    {
        return [
            'Party' => [[
                'PartyIdentification' => [[
                    'ID' => [
                        [
                            '_' => $customer->tin,
                            'schemeID' => 'TIN',
                        ],
                    ],
                ]],
                'PostalAddress' => [[
                    'CityName' => [['_' => $customer->city]],
                    'PostalZone' => [['_' => $customer->postcode]],
                    'CountrySubentityCode' => [['_' => $this->getStateCode($customer->state)]],
                    'AddressLine' => [
                        ['Line' => [['_' => $customer->address_line_1]]],
                        ['Line' => [['_' => $customer->address_line_2]]],
                    ],
                    'Country' => [[
                        'IdentificationCode' => [
                            [
                                '_' => 'MYS',
                                'listID' => 'ISO3166-1',
                                'listAgencyID' => '6',
                            ],
                        ],
                    ]],
                ]],
                'PartyLegalEntity' => [[
                    'RegistrationName' => [['_' => $customer->company_name]],
                ]],
                'Contact' => [[
                    'Telephone' => [['_' => $customer->contact_1 ?? '']],
                    'ElectronicMail' => [['_' => $customer->email_1 ?? '']],
                ]],
            ]],
        ];
    }

    /**
     * Build Invoice Lines
     */
    private function buildInvoiceLines($items): array
    {
        $lines = [];
        $lineNumber = 1;

        foreach ($items as $item) {
            $lines[] = [
                'ID' => [['_' => (string) $lineNumber]],
                'InvoicedQuantity' => [
                    [
                        '_' => $item->quantity ?? 1,
                        'unitCode' => 'EA', // EA = Each, can be made dynamic
                    ],
                ],
                'LineExtensionAmount' => [
                    [
                        '_' => number_format($item->subtotal ?? 0, 2, '.', ''),
                        'currencyID' => $item->currency_code ?? 'MYR',
                    ],
                ],
                'TaxTotal' => [[
                    'TaxAmount' => [
                        [
                            '_' => number_format($item->tax_amount ?? 0, 2, '.', ''),
                            'currencyID' => $item->currency_code ?? 'MYR',
                        ],
                    ],
                    'TaxSubtotal' => [[
                        'TaxableAmount' => [
                            [
                                '_' => number_format($item->subtotal ?? 0, 2, '.', ''),
                                'currencyID' => $item->currency_code ?? 'MYR',
                            ],
                        ],
                        'TaxAmount' => [
                            [
                                '_' => number_format($item->tax_amount ?? 0, 2, '.', ''),
                                'currencyID' => $item->currency_code ?? 'MYR',
                            ],
                        ],
                        'TaxCategory' => [[
                            'ID' => [['_' => $this->getTaxCategoryId($item->tax_type)]],
                            'TaxExemptionReason' => $item->tax_exemption ? [['_' => $item->tax_exemption]] : null,
                            'TaxScheme' => [[
                                'ID' => [
                                    [
                                        '_' => 'OTH',
                                        'schemeID' => 'UN/ECE 5153',
                                        'schemeAgencyID' => '6',
                                    ],
                                ],
                            ]],
                        ]],
                    ]],
                ]],
                'Item' => [[
                    'Description' => [['_' => $item->particulars ?? '']],
                    'CommodityClassification' => $item->classification ? [[
                        'ItemClassificationCode' => [
                            [
                                '_' => $item->classification,
                                'listID' => 'CLASS',
                            ],
                        ],
                    ]] : null,
                ]],
                'Price' => [[
                    'PriceAmount' => [
                        [
                            '_' => number_format($item->unit_price ?? 0, 2, '.', ''),
                            'currencyID' => $item->currency_code ?? 'MYR',
                        ],
                    ],
                ]],
            ];

            // Remove null values
            $lines[$lineNumber - 1] = array_filter($lines[$lineNumber - 1], function ($value) {
                return $value !== null;
            });

            $lineNumber++;
        }

        return $lines;
    }

    /**
     * Build Credit Note Lines
     */
    private function buildCreditNoteLines($items): array
    {
        // Similar to invoice lines but for credit notes
        return $this->buildInvoiceLines($items); // Simplified for now
    }

    /**
     * Build Debit Note Lines
     */
    private function buildDebitNoteLines($items): array
    {
        // Similar to invoice lines but for debit notes
        return $this->buildInvoiceLines($items); // Simplified for now
    }

    /**
     * Build Refund Note Lines
     */
    private function buildRefundNoteLines($items): array
    {
        // Similar to invoice lines but for refund notes
        return $this->buildInvoiceLines($items); // Simplified for now
    }

    /**
     * Calculate Invoice Totals
     */
    private function calculateInvoiceTotals($items): array
    {
        $lineExtension = $items->sum('subtotal');
        $taxExclusive = $lineExtension;
        $taxInclusive = $items->sum('grand_total');
        $payable = $taxInclusive;

        return [
            'LineExtensionAmount' => [
                [
                    '_' => number_format($lineExtension, 2, '.', ''),
                    'currencyID' => $items->first()->currency_code ?? 'MYR',
                ],
            ],
            'TaxExclusiveAmount' => [
                [
                    '_' => number_format($taxExclusive, 2, '.', ''),
                    'currencyID' => $items->first()->currency_code ?? 'MYR',
                ],
            ],
            'TaxInclusiveAmount' => [
                [
                    '_' => number_format($taxInclusive, 2, '.', ''),
                    'currencyID' => $items->first()->currency_code ?? 'MYR',
                ],
            ],
            'PayableAmount' => [
                [
                    '_' => number_format($payable, 2, '.', ''),
                    'currencyID' => $items->first()->currency_code ?? 'MYR',
                ],
            ],
        ];
    }

    /**
     * Calculate Tax Total
     */
    private function calculateTaxTotal($items): array
    {
        $taxAmount = $items->sum('tax_amount');

        return [
            'TaxAmount' => [
                [
                    '_' => number_format($taxAmount, 2, '.', ''),
                    'currencyID' => $items->first()->currency_code ?? 'MYR',
                ],
            ],
        ];
    }

    /**
     * Calculate Credit Note Totals
     */
    private function calculateCreditNoteTotals($items): array
    {
        return $this->calculateInvoiceTotals($items);
    }

    /**
     * Calculate Credit Note Tax Total
     */
    private function calculateCreditNoteTaxTotal($items): array
    {
        return $this->calculateTaxTotal($items);
    }

    /**
     * Calculate Debit Note Totals
     */
    private function calculateDebitNoteTotals($items): array
    {
        return $this->calculateInvoiceTotals($items);
    }

    /**
     * Calculate Debit Note Tax Total
     */
    private function calculateDebitNoteTaxTotal($items): array
    {
        return $this->calculateTaxTotal($items);
    }

    /**
     * Calculate Refund Note Totals
     */
    private function calculateRefundNoteTotals($items): array
    {
        return $this->calculateInvoiceTotals($items);
    }

    /**
     * Calculate Refund Note Tax Total
     */
    private function calculateRefundNoteTaxTotal($items): array
    {
        return $this->calculateTaxTotal($items);
    }

    /**
     * Get Company Profile
     */
    private function getCompanyProfile(): CompanyProfile
    {
        $companyId = config('myinvois.company.default_id');
        $company = CompanyProfile::find($companyId);

        if (! $company && config('myinvois.company.fallback_to_first')) {
            $company = CompanyProfile::where('status', '0')->first();
        }

        if (! $company) {
            throw new \Exception('Company profile not found');
        }

        return $company;
    }

    /**
     * Get Customer Profile
     */
    private function getCustomerProfile(string $companyName): CustomerProfile
    {
        $customer = CustomerProfile::where('company_name', $companyName)
            ->where('status', '0')
            ->first();

        if (! $customer) {
            throw new \Exception("Customer profile not found for: {$companyName}");
        }

        return $customer;
    }

    /**
     * Get State Code from state ID
     */
    private function getStateCode($stateId): string
    {
        // Map state IDs to Malaysian state codes
        // This should be implemented based on your selections table
        // For now, return a placeholder
        return '14'; // Default to Kuala Lumpur
    }

    /**
     * Get Tax Category ID based on tax type
     */
    private function getTaxCategoryId(?string $taxType): string
    {
        // Map tax types to category IDs
        // E = Exempt from Tax, S = Standard rated, Z = Zero rated
        return match ($taxType) {
            'exempt' => 'E',
            'zero' => 'Z',
            'standard' => 'S',
            default => 'S',
        };
    }
}
