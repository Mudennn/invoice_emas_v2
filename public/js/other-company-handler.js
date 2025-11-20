/**
 * Other Company Handler - Shared JavaScript for Invoice/Self-Billed Invoice Forms
 * 
 * This file contains functionality for handling the "Other" company selection
 * and showing the modal button for adding new company details.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the other company handler
    initOtherCompanyHandler();
    
    // Also handle the initial state on page load
    handleOtherCompanySelection();
});

/**
 * Initialize the other company handler
 */
function initOtherCompanyHandler() {
    // Find the company select element
    const companySelect = document.querySelector('.company-select2');
    
    if (!companySelect) {
        console.warn('Company select element not found');
        return;
    }
    
    // Add change event listener
    companySelect.addEventListener('change', handleOtherCompanySelection);
    
    // For Select2, we also need to listen to the select2:select event
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $(companySelect).on('select2:select', handleOtherCompanySelection);
    }
    
    // Setup save button handler for the modal
    setupSaveButtonHandler();
}

/**
 * Handle the company selection change
 */
function handleOtherCompanySelection() {
    const companySelect = document.querySelector('.company-select2');
    const otherCompanyBtn = document.getElementById('otherCompanyDetailsBtn');
    
    if (!companySelect || !otherCompanyBtn) {
        console.warn('Required elements not found for other company handling');
        return;
    }
    
    // Show or hide the button based on selection
    if (companySelect.value === 'Other') {
        otherCompanyBtn.style.display = 'block';
    } else {
        otherCompanyBtn.style.display = 'none';
    }
}

/**
 * Setup the save button handler for the modal
 */
function setupSaveButtonHandler() {
    const saveBtn = document.getElementById('saveCompanyBtn');
    
    if (!saveBtn) {
        console.warn('Save button not found');
        return;
    }
    
    saveBtn.addEventListener('click', function() {
        // Get the company name input
        const companyNameInput = document.getElementById('other_company_name');
        const companyNameError = document.getElementById('company-name-error');
        
        if (!companyNameInput) {
            console.error('Company name input not found');
            return;
        }
        
        // Validate the company name
        if (!companyNameInput.value.trim()) {
            if (companyNameError) {
                companyNameError.textContent = 'Company name is required';
            }
            return;
        }
        
        // Clear any previous error
        if (companyNameError) {
            companyNameError.textContent = '';
        }
        
        // Close the modal
        if (typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('otherCompanyModal'));
            if (modal) {
                modal.hide();
            }
        } else {
            // Fallback for older Bootstrap versions or if bootstrap is not defined
            $('#otherCompanyModal').modal('hide');
        }
    });
} 