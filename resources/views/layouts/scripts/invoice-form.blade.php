<script>
    /**
 * Invoice Form Specific JavaScript
 * 
 * This file initializes the FormHandler for invoice forms.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the old items from Laravel's response if they exist
    window.oldItems = typeof oldItems !== 'undefined' ? oldItems : [];
    
    // Create form handler instance with invoice-specific configuration
    const invoiceForm = new FormHandler({
        formType: 'invoices',
        products: window.products || [],
        readOnly: window.ro || '',
        pair: window.pair || [],
        goldPrices: window.goldPrices || []
    });
    
    // Any invoice-specific customizations can be added here
    
    // For example, add any additional tax calculations specific to invoices
    if (document.querySelector('.sst-input')) {
        document.querySelectorAll('.subtotal-input').forEach(input => {
            input.addEventListener('change', function() {
                const subtotal = parseFloat(this.value) || 0;
                const sst = subtotal * 0.08;
                const grandTotal = subtotal + sst;
                
                document.querySelectorAll('.sst-input').forEach(sstInput => {
                    sstInput.value = sst.toFixed(2);
                });
                
                document.querySelectorAll('.grand-total-input').forEach(totalInput => {
                    totalInput.value = grandTotal.toFixed(2);
                });
            });
        });
    }
    
    // Initialize the return balance calculator
    if (typeof ReturnBalanceCalculator !== 'undefined') {
        ReturnBalanceCalculator.init();
    }
}); 
</script>