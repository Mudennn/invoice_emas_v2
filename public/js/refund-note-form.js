/**
 * Refund Note Form Specific JavaScript
 * 
 * This file initializes the FormHandler for refund note forms.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the old items from Laravel's response if they exist
    window.oldItems = typeof oldItems !== 'undefined' ? oldItems : [];
    
    // Create form handler instance with refund note-specific configuration
    const refundNoteForm = new FormHandler({
        formType: 'refund_notes',
        products: window.products || [],
        readOnly: window.ro || '',
        pair: window.pair || [],
        goldPrices: window.goldPrices || []
    });
    
    // Initialize the return balance calculator
    if (typeof ReturnBalanceCalculator !== 'undefined') {
        ReturnBalanceCalculator.init();
    }
}); 