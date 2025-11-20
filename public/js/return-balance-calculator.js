/**
 * Return Balance Calculator - Shared JavaScript
 * 
 * This file contains shared functionality for handling return balance calculations
 * for all note types (invoice, credit note, debit note, refund note).
 */

// Global return balance calculation functions
const ReturnBalanceCalculator = {
    // Initialize the return balance calculator
    init: function() {
        // Add event listener for remarks field to handle special calculations
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('remark-input')) {
                ReturnBalanceCalculator.calculateReturnBalance(e.target);
            }
        });
        
        // Initial calculation for existing remarks
        document.querySelectorAll('.remark-input').forEach(remarkInput => {
            if (remarkInput.value.trim()) {
                // Wait for other initial calculations to complete
                setTimeout(() => {
                    ReturnBalanceCalculator.calculateReturnBalance(remarkInput);
                }, 500);
            }
        });
        
        // Add event listener for weight-related fields to recalculate return balance
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('weight-input') || e.target.classList.contains('wastage-input')) {
                // Use setTimeout to ensure total weight is calculated first
                setTimeout(() => {
                    const row = e.target.closest('tr');
                    const remarkInput = row.querySelector('.remark-input');
                    if (remarkInput) {
                        ReturnBalanceCalculator.calculateReturnBalance(remarkInput);
                    }
                }, 100);
            }
        });
        
        // Add event listener for the remove button to update total return balance
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                // Wait a bit for the row to be removed
                setTimeout(() => {
                    ReturnBalanceCalculator.updateTotalReturnBalance();
                }, 100);
            }
        });
        
        // Set up invoice selection handler for all forms
        this.setupInvoiceSelectionHandler();
        
        console.log('Return balance calculator initialized');
    },
    
    // Setup handler for invoice selection
    setupInvoiceSelectionHandler: function() {
        // Add handler for invoice selection dropdown changes
        $('.invoice-select2').on('change', function() {
            // Wait for invoice details to load
            setTimeout(() => {
                // Recalculate return balances for all remarks
                document.querySelectorAll('.remark-input').forEach(remarkInput => {
                    if (remarkInput.value.trim()) {
                        ReturnBalanceCalculator.calculateReturnBalance(remarkInput);
                    }
                });
            }, 1000); // Wait for 1 second to ensure data has loaded
        });
    },
    
    // Calculate return balance based on remarks
    calculateReturnBalance: function(remarkInput) {
        const row = remarkInput.closest('tr');
        const remarkText = remarkInput.value.trim();
        const totalWeightInput = row.querySelector('input[name$="[total_weight]"]');
        const totalWeight = parseFloat(totalWeightInput.value) || 0;
        
        let returnBalance = 0;
        
        // Check for gold purity indicators in the remarks
        if (remarkText.includes('916')) {
            returnBalance = totalWeight * 0.95;
        } else if (remarkText.includes('835')) {
            returnBalance = totalWeight * 0.87;
        } else if (remarkText.includes('750')) {
            returnBalance = totalWeight * 0.78;
        } else if (remarkText.includes('375')) {
            returnBalance = totalWeight * 0.40;
        }
        
        // Update the return balance in the row if it exists
        const returnBalanceInput = row.querySelector('input[name$="[return_balance]"]');
        if (returnBalanceInput) {
            returnBalanceInput.value = returnBalance.toFixed(2);
        } else {
            // Create return balance input if it doesn't exist
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = totalWeightInput.name.replace('[total_weight]', '[return_balance]');
            hiddenInput.value = returnBalance.toFixed(2);
            hiddenInput.className = 'return-balance-input';
            row.appendChild(hiddenInput);
        }
        
        // Add or update the remark_total field which gets saved to the database
        const remarkTotalInput = row.querySelector('input[name$="[remark_total]"]');
        if (remarkTotalInput) {
            remarkTotalInput.value = returnBalance.toFixed(2);
        } else {
            // Create remark_total input if it doesn't exist
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = totalWeightInput.name.replace('[total_weight]', '[remark_total]');
            hiddenInput.value = returnBalance.toFixed(2);
            row.appendChild(hiddenInput);
        }
        
        // Update total return balance
        ReturnBalanceCalculator.updateTotalReturnBalance();
    },
    
    // Update the total return balance display
    updateTotalReturnBalance: function() {
        const returnBalances = Array.from(document.querySelectorAll('.return-balance-input'))
            .map(input => parseFloat(input.value) || 0);
        const totalReturnBalance = returnBalances.reduce((sum, value) => sum + value, 0);
        
        // Update the existing return balance row
        const totalReturnBalanceInput = document.querySelector('.total-return-balance-input');
        if (totalReturnBalanceInput) {
            totalReturnBalanceInput.value = totalReturnBalance.toFixed(2);
            
            // Also update the remark_total on each item if needed for validation/consistency
            document.querySelectorAll('.remark-total-input').forEach(input => {
                // Make sure the value is consistent with the individual return balance calculations
                const row = input.closest('tr');
                const returnBalanceInput = row.querySelector('.return-balance-input');
                if (returnBalanceInput) {
                    input.value = returnBalanceInput.value;
                }
            });
        }
    },
    
    // Manually force recalculation of all return balances - useful after item removal
    recalculateAll: function() {
        document.querySelectorAll('.remark-input').forEach(remarkInput => {
            if (remarkInput.value.trim()) {
                ReturnBalanceCalculator.calculateReturnBalance(remarkInput);
            }
        });
        ReturnBalanceCalculator.updateTotalReturnBalance();
    }
};

// Export the calculator for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ReturnBalanceCalculator;
} 