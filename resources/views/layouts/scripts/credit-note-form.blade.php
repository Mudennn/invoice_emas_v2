<script>
    /**
 * Credit Note Form Specific JavaScript
 * 
 * This file initializes the FormHandler for credit note forms.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the old items from Laravel's response if they exist
    window.oldItems = typeof oldItems !== 'undefined' ? oldItems : [];
    
    // Create form handler instance with credit note-specific configuration
    const creditNoteForm = new FormHandler({
        formType: 'credit_notes',
        products: window.products || [],
        readOnly: window.ro || '',
        pair: window.pair || [],
        goldPrices: window.goldPrices || []
    });
    
    // Add event listener for invoice selection to update customer and billing address
    $('.invoice-select2').on('change', function() {
        const invoiceNo = $(this).val();
        if (invoiceNo) {
            // Fetch invoice details
            fetch(`/credit_notes/get-invoice-details/${invoiceNo}`)
                .then(response => response.json())
                .then(data => {
                    // Update customer name and address
                    document.getElementById('company_name').textContent = data.customer_name || data.company_name || '';
                    document.getElementById('customer_address').textContent = data.billing_address || data.address || '';
                    
                    // Make the invoice details section visible
                    const invoiceDetails = document.querySelector('.invoice-detailss');
                    if (invoiceDetails) {
                        invoiceDetails.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error fetching invoice details:', error);
                });
        } else {
            // Clear customer and address if no invoice selected
            document.getElementById('company_name').textContent = '';
            document.getElementById('customer_address').textContent = '';
            
            // Hide the invoice details section
            const invoiceDetails = document.querySelector('.invoice-detailss');
            if (invoiceDetails) {
                invoiceDetails.style.display = 'none';
            }
        }
    });
    
    // Trigger the change event if an invoice is already selected
    if ($('.invoice-select2').val()) {
        $('.invoice-select2').trigger('change');
    }
    
    // Initialize the return balance calculator
    if (typeof ReturnBalanceCalculator !== 'undefined') {
        ReturnBalanceCalculator.init();
    }
}); 
</script>