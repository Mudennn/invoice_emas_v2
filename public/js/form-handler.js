/**
 * Form Handler - Shared JavaScript for Invoice/Credit/Debit/Refund Notes
 * 
 * This file contains shared functionality for handling form operations across
 * all note types (invoice, credit note, debit note, refund note).
 */

class FormHandler {
    /**
     * Initialize the form handler
     * @param {Object} config - Configuration object
     * @param {string} config.formType - The type of form ('invoice', 'credit_note', 'debit_note', 'refund_note')
     * @param {Array} config.products - Array of product objects
     * @param {string|null} config.readOnly - Read-only attribute if present
     * @param {Array} config.pair - Array of pair options
     * @param {Array|null} config.goldPrices - Array of gold prices (optional)
     */
    constructor(config) {
        // Store config
        this.config = config;
        this.formType = config.formType;
        
        console.log(`Initializing FormHandler for ${this.formType}`);
        
        this.products = config.products || [];
        this.ro = config.readOnly || '';
        this.pair = config.pair || [];
        this.goldPrices = config.goldPrices || [];
        
        // Form type specific settings
        this.noteTypeSingular = this.formType.replace('_', ' ');
        this.noteTypePlural = this.noteTypeSingular + 's';
        
        // Map form types to their CSS classes and item container IDs
        const formTypeMap = {
            'invoices': {
                itemClassName: 'invoice-item',
                itemsContainerId: 'invoice-items'
            },
            'credit_notes': {
                itemClassName: 'credit-note-item',
                itemsContainerId: 'credit_notes-items'
            },
            'debit_notes': {
                itemClassName: 'debit-note-item',
                itemsContainerId: 'debit_notes-items'
            },
            'refund_notes': {
                itemClassName: 'refund-note-item',
                itemsContainerId: 'refund_notes-items'
            }
        };
        
        // Set item class name based on form type
        this.itemClassName = formTypeMap[this.formType]?.itemClassName || 'form-item';
        
        // Get DOM elements - These may be null in some contexts
        this.addItemDropdown = document.getElementById('addItemDropdown');
        this.invoiceDetails = document.querySelector('.invoice-detailss');
        
        // Look for the items container using the mapped ID or fall back to a more generic selector
        const itemsContainerId = formTypeMap[this.formType]?.itemsContainerId;
        this.itemsTable = itemsContainerId ? 
            document.querySelector(`#${itemsContainerId} tbody`) : 
            document.querySelector('.table-responsive tbody');
            
        // Log information about what we found
        console.log(`Using container ID: ${itemsContainerId}`);
        console.log(`Found items table: ${this.itemsTable ? 'Yes' : 'No'}`);
        console.log(`Found add item dropdown: ${this.addItemDropdown ? 'Yes' : 'No'}`);
        console.log(`Found invoice details: ${this.invoiceDetails ? 'Yes' : 'No'}`);
        
        // Initialize tracking variables
        this.deletedItems = [];
        this.itemIndex = 0;
        
        // Initialize the form
        this.init();
    }
    
    /**
     * Initialize the form handler
     */
    init() {
        // Initialize item index for dynamic row creation
        this.initializeItemIndex();
        
        // Setup the observer for the dropdown (if it exists)
        this.setupDropdownObserver();
        
        // Initialize existing items if present
        this.initializeExistingItems();
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Handle dropdown menu item clicks for adding new items
        this.setupDropdownHandlers();
        
        // Setup FOC button click handlers
        this.setupFocButtonHandlers();
        
        // Setup event delegation for remove button
        this.setupRemoveButtonHandlers();
        
        // Perform actions when page loads
        this.initializeOnLoad();
        
        console.log(`Form handler initialized for ${this.formType}`);
    }
    
    /**
     * Initialize item index for dynamic row creation
     */
    initializeItemIndex() {
        // Get count of old items (if form was submitted with errors)
        const oldItems = window.oldItems || [];
        
        // Get count of existing items (if editing an existing record)
        const existingItemsCount = document.querySelectorAll(`.${this.itemClassName}`).length;
        
        // Set index to the maximum of these two values
        this.itemIndex = Math.max(
            oldItems.length || 0,
            existingItemsCount || 0
        );
    }
    
    /**
     * Initialize existing items if present
     */
    initializeExistingItems() {
        // Initialize Select2 for existing rows
        this.initializeSelect2ForExistingRows();
        
        // Check if invoice is already selected
        this.checkInitialInvoice();
        
        // Calculate totals for existing items
        this.calculateInitialTotals();
        
        // Check item limit for button state
        this.checkItemLimit();
    }
    
    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Initialize Select2 for invoice dropdown
        this.initializeInvoiceSelect2();
        
        // Setup calculation listeners for inputs
        this.setupCalculationListeners();
    }
    
    /**
     * Setup dropdown observer to prevent showing when disabled
     */
    setupDropdownObserver() {
        if (!this.addItemDropdown) {
            console.warn('Add item dropdown not found, skipping dropdown observer setup');
            return;
        }
        
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'disabled' && this.addItemDropdown.disabled) {
                    const dropdownMenu = this.addItemDropdown.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                        dropdownMenu.classList.remove('show');
                    }
                }
            });
        });
        
        observer.observe(this.addItemDropdown, { attributes: true });
        
        // Add click handler to prevent dropdown when disabled
        this.addItemDropdown.addEventListener('click', (e) => {
            if (this.addItemDropdown.disabled) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
    
    /**
     * Initialize Select2 for invoice dropdown
     */
    initializeInvoiceSelect2() {
        $('.invoice-select2').select2({
            placeholder: "Select Invoice",
            width: '100%'
        }).on('change', (e) => {
            const invoiceNo = e.target.value;
            if (invoiceNo) {
                this.loadInvoiceDetails(invoiceNo);
            } else {
                this.clearInvoiceDetails();
            }
        });
    }
    
    /**
     * Setup event handlers for the dropdown menu items
     */
    setupDropdownHandlers() {
        if (!this.addItemDropdown) {
            console.warn('Add item dropdown not found, skipping dropdown handlers setup');
            return;
        }
        
        // Add click handler to prevent dropdown when disabled
        this.addItemDropdown.addEventListener('click', (e) => {
            if (this.addItemDropdown.disabled) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
        
        // Handle dropdown menu item clicks
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                
                console.log('Dropdown item clicked');
                
                const itemType = e.target.getAttribute('data-item-type');
                if (!itemType) {
                    console.error('Item type not found in dropdown item');
                    return;
                }
                
                console.log(`Adding new item of type: ${itemType}`);
                
                // Check if adding another item would exceed the limit
                const existingItems = document.querySelectorAll(`.${this.itemClassName}`);
                if (existingItems.length >= 8) {
                    alert('Maximum of 8 items allowed per form.');
                    return;
                }
                
                this.addNewItem(itemType);
                
                // Check limit after adding
                this.checkItemLimit();
            });
        });
    }
    
    /**
     * Setup FOC button click handlers
     */
    setupFocButtonHandlers() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('foc-btn')) {
                const workmanshipInput = e.target.closest('.input-group').querySelector('.workmanship-input');
                workmanshipInput.value = 'FOC';
                
                // Trigger calculation update
                const row = e.target.closest('tr');
                this.calculateTotal(row);
            }
        });
    }
    
    /**
     * Setup event delegation for remove button
     */
    setupRemoveButtonHandlers() {
        // Find the container element based on the form type
        const formTypeMap = {
            'invoices': 'invoice-items',
            'credit_notes': 'credit_notes-items',
            'debit_notes': 'debit_notes-items',
            'refund_notes': 'refund_notes-items'
        };
        
        const containerId = formTypeMap[this.formType];
        if (!containerId) {
            console.error(`Could not find container ID for form type: ${this.formType}`);
            return;
        }
        
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container element not found with ID: ${containerId}`);
            return;
        }
        
        // Event delegation for remove button
        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item')) {
                const row = e.target.closest('tr');
                const itemId = row.getAttribute('data-item-id');

                // If the item has an ID (exists in database), add it to deleted items
                if (itemId) {
                    this.deletedItems.push(itemId);
                    const deletedItemsInput = document.getElementById('deleted_items');
                    if (deletedItemsInput) {
                        deletedItemsInput.value = JSON.stringify(this.deletedItems);
                    }
                }

                // Only destroy Select2 if this is a single item type (not multiple)
                if (!row.getAttribute('data-item-type')?.includes('multiple')) {
                    const select2Input = row.querySelector('.select2-input');
                    if (select2Input && $(select2Input).hasClass('select2-hidden-accessible')) {
                        $(select2Input).select2('destroy');
                    }
                }

                row.remove();
                this.updateDisplayTotals();
                
                // Update button state after removing an item (with small delay to ensure DOM is updated)
                setTimeout(() => {
                    this.checkItemLimit();
                }, 10);
                
                // Update return balance total if ReturnBalanceCalculator exists
                if (typeof ReturnBalanceCalculator !== 'undefined') {
                    setTimeout(() => {
                        ReturnBalanceCalculator.updateTotalReturnBalance();
                    }, 100);
                }
            }
        });
        
        // For backward compatibility, also attach to document in case container is not found or missing
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item')) {
                const row = e.target.closest('tr');
                if (!row.closest(`#${containerId}`)) { // Only handle if not already handled by container
                    const itemId = row.getAttribute('data-item-id');
    
                    // If the item has an ID (exists in database), add it to deleted items
                    if (itemId) {
                        this.deletedItems.push(itemId);
                        const deletedItemsInput = document.getElementById('deleted_items');
                        if (deletedItemsInput) {
                            deletedItemsInput.value = JSON.stringify(this.deletedItems);
                        }
                    }
    
                    // Only destroy Select2 if this is a single item type (not multiple)
                    if (!row.getAttribute('data-item-type')?.includes('multiple')) {
                        const select2Input = row.querySelector('.select2-input');
                        if (select2Input && $(select2Input).hasClass('select2-hidden-accessible')) {
                            $(select2Input).select2('destroy');
                        }
                    }
    
                    row.remove();
                    this.updateDisplayTotals();
                    
                    // Update button state after removing an item (with small delay to ensure DOM is updated)
                    setTimeout(() => {
                        this.checkItemLimit();
                    }, 10);
                    
                    // Update return balance total if ReturnBalanceCalculator exists
                    if (typeof ReturnBalanceCalculator !== 'undefined') {
                        setTimeout(() => {
                            ReturnBalanceCalculator.updateTotalReturnBalance();
                        }, 100);
                    }
                }
            }
        });
    }
    
    /**
     * Initialize Select2 for existing rows
     */
    initializeSelect2ForExistingRows() {
        document.querySelectorAll(`.${this.itemClassName}`).forEach(row => {
            this.initializeSelect2ForRow(row);
        });
    }
    
    /**
     * Check if an invoice is already selected on page load
     */
    checkInitialInvoice() {
        if ($('.invoice-select2').val()) {
            this.loadInvoiceDetails($('.invoice-select2').val());
        }
    }
    
    /**
     * Calculate initial totals for existing items
     */
    calculateInitialTotals() {
        document.querySelectorAll(`.${this.itemClassName}`).forEach(row => {
            this.calculateTotalWeight(row);
            this.calculateTotal(row);
        });
        
        this.updateDisplayTotals();
    }
    
    /**
     * Check item limit and update button state
     */
    checkItemLimit() {
        const existingItems = document.querySelectorAll(`.${this.itemClassName}`);
        
        // Skip if addItemDropdown doesn't exist
        if (!this.addItemDropdown) {
            console.warn('Add item dropdown not found, skipping limit check');
            return;
        }
        
        const dropdownContainer = this.addItemDropdown.closest('.dropdown');
        if (!dropdownContainer) {
            console.warn('Dropdown container not found, skipping limit check');
            return;
        }
        
        console.log(`Checking item limit: ${existingItems.length} items found with class '${this.itemClassName}'`);
        
        if (existingItems.length >= 8) {
            console.log('Disabling add item button - 8 or more items');
            this.addItemDropdown.disabled = true;
            this.addItemDropdown.title = 'Maximum 8 items allowed';
            // Completely remove dropdown class to prevent dropdown from showing
            dropdownContainer.classList.remove('dropdown');
            dropdownContainer.classList.add('d-inline-block'); // Keep layout intact
        } else {
            console.log('Enabling add item button - less than 8 items');
            this.addItemDropdown.disabled = false;
            this.addItemDropdown.title = '';
            // Restore dropdown class
            dropdownContainer.classList.add('dropdown');
            dropdownContainer.classList.remove('d-inline-block');
        }
    }
    
    /**
     * Handle item removal
     * @param {Event} event - The click event
     */
    handleItemRemoval(event) {
        const row = event.target.closest('tr');
        const itemId = row.getAttribute('data-item-id');
        
        // If the item has an ID (exists in database), add it to deleted items
        if (itemId) {
            this.deletedItems.push(itemId);
            document.getElementById('deleted_items').value = JSON.stringify(this.deletedItems);
        }
        
        // Destroy the Select2 instance if it exists
        $(row).find('.select2-input').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        
        // Remove the row
        row.remove();
        
        // Update totals and button state
        this.updateDisplayTotals();
        setTimeout(() => {
            this.checkItemLimit();
        }, 10);
        
        // Update return balance total if ReturnBalanceCalculator exists
        if (typeof ReturnBalanceCalculator !== 'undefined') {
            setTimeout(() => {
                ReturnBalanceCalculator.updateTotalReturnBalance();
            }, 100);
        }
    }
    
    /**
     * Setup calculation listeners for inputs
     */
    setupCalculationListeners() {
        // Weight and wastage calculations
        document.querySelectorAll('.weight-input, .wastage-input').forEach(input => {
            input.removeEventListener('input', this.weightInputHandler);
            input.addEventListener('input', (e) => {
                this.calculateTotalWeight(e.target.closest('tr'));
            });
        });
        
        // Price calculations
        document.querySelectorAll('.gold-input, .quantity-input, .unit-price-input, .workmanship-input').forEach(input => {
            input.removeEventListener('input', this.priceInputHandler);
            input.addEventListener('input', (e) => {
                this.calculateTotal(e.target.closest('tr'));
            });
        });
        
        // Handle reference and particulars synchronization
        this.setupProductSyncHandlers();
    }
    
    /**
     * Setup handlers for syncing product reference and particulars
     */
    setupProductSyncHandlers() {
        // Add flags to prevent infinite loop
        this.isUpdatingReference = false;
        this.isUpdatingParticulars = false;
        
        // Handle reference number change for all types
        $(document).on('change', '.select2-input[data-type="reference"]', (e) => {
            if (this.isUpdatingReference) return;
            
            const selectedOption = e.target.options[e.target.selectedIndex];
            const index = $(e.target).data('index');
            const row = $(e.target).closest('tr');
            const particularsInput = row.find('[name$="[particulars]"]');
            
            if (selectedOption && selectedOption.dataset.name) {
                if (particularsInput.hasClass('select2-input')) {
                    // For single items, update the select2
                    this.isUpdatingParticulars = true;
                    particularsInput.val(selectedOption.dataset.name).trigger('change');
                    this.isUpdatingParticulars = false;
                } else {
                    // For multiple items, update the text input
                    particularsInput.val(selectedOption.dataset.name);
                }
            }
        });
        
        // Handle particulars change (only for single items)
        $(document).on('change', '.select2-input[data-type="particulars"]', (e) => {
            if (this.isUpdatingParticulars) return;
            
            const selectedOption = e.target.options[e.target.selectedIndex];
            const index = $(e.target).data('index');
            const referenceSelect = $(`.select2-input[data-type="reference"][data-index="${index}"]`);
            
            if (selectedOption && selectedOption.dataset.code) {
                this.isUpdatingReference = true;
                referenceSelect.val(selectedOption.dataset.code).trigger('change');
                this.isUpdatingReference = false;
            }
        });
    }
    
    /**
     * Initialize Select2 for a specific row
     * @param {HTMLElement} row - The row element
     */
    initializeSelect2ForRow(row) {
        $(row).find('.select2-input').each(function() {
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
            $(this).select2({
                placeholder: $(this).data('type') === 'reference' ? "Select Code" : "Select Product",
                width: '100%'
            });
        });
    }
    
    /**
     * Add a new item to the form
     * @param {string} itemType - The type of item to add
     */
    addNewItem(itemType) {
        if (!this.itemsTable) {
            console.error(`Items table not found for form type: ${this.formType}`);
            return;
        }
        
        try {
            const row = this.createItemRow(this.itemIndex++, null, itemType);
            this.itemsTable.insertAdjacentHTML('beforeend', row);
            
            const newRow = this.itemsTable.querySelector('tr:last-child');
            if (newRow) {
                this.initializeSelect2ForRow(newRow);
                this.setupCalculationListeners();
    
                // If ReturnBalanceCalculator exists, ensure it's triggered for new items when remarks are added
                if (typeof ReturnBalanceCalculator !== 'undefined') {
                    const remarkInput = newRow.querySelector('.remark-input');
                    if (remarkInput) {
                        // Add event listener for the remark input
                        remarkInput.addEventListener('input', function() {
                            ReturnBalanceCalculator.calculateReturnBalance(this);
                        });
                    }
                }
            } else {
                console.error('Failed to find newly added row');
            }
        } catch (error) {
            console.error('Error adding new item:', error);
        }
    }
    
    /**
     * Get the product details cell HTML
     * @param {number} index - The index of the item
     * @param {Object|null} item - The item data, if any
     * @param {string} itemType - The type of item
     * @returns {string} - HTML for the cell
     */
    getProductDetailsCell(index, item, itemType) {
        const isMultipleItem = itemType?.includes('multiple');
        
        return `
            <td>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex flex-column gap-2">
                        <label class="small text-muted">Reference No</label>
                        <div class="d-flex flex-column gap-2">
                            <input type="text" 
                                name="items[${index}][reference_no]" 
                                class="form-control reference-input" 
                                value="${item?.reference_no || ''}"
                                placeholder="Enter Reference No"
                                ${this.ro || ''}>
                            <input type="text" 
                                name="items[${index}][custom_reference]" 
                                class="form-control custom-reference-input" 
                                value="${item?.custom_reference || ''}"
                                placeholder="Custom Reference"
                                ${this.ro || ''}>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <label class="small text-muted">Particulars</label>
                        ${isMultipleItem ? `
                            <input type="text" 
                                name="items[${index}][particulars]" 
                                class="form-control particulars-input"
                                value="${item?.particulars || ''}" 
                                placeholder="Enter particulars"
                                ${this.ro || ''}>
                        ` : `
                            <select name="items[${index}][particulars]"
                                class="form-control form-select product-name-select select2-input"
                                data-type="particulars" data-index="${index}"
                                ${this.ro || ''}>
                                <option value="">Select Product</option>
                                ${this.products?.map(product => `
                                    <option value="${product.name}" 
                                        data-code="${product.code}"
                                        ${(item?.particulars === product.name) ? 'selected' : ''}>
                                        ${product.name}
                                    </option>
                                `).join('')}
                            </select>
                        `}
                    </div>
                </div>
            </td>
        `;
    }
    
    /**
     * Get the weight details cell HTML
     * @param {number} index - The index of the item
     * @param {Object|null} item - The item data, if any
     * @returns {string} - HTML for the cell
     */
    getWeightDetailsCell(index, item) {
        return `
            <td>
                <div class="d-flex flex-column gap-2">
                    <div>
                        <label class="small text-muted">Weight</label>
                        <input type="number" step="0.01"
                            name="items[${index}][weight]"
                            value="${item?.weight || ''}"
                            class="form-control weight-input" 
                            ${this.ro || ''}>
                    </div>
                    <div>
                        <label class="small text-muted">Wastage</label>
                        <input type="number" step="0.01"
                            name="items[${index}][wastage]"
                            value="${item?.wastage || ''}"
                            class="form-control wastage-input" 
                            ${this.ro || ''}>
                    </div>
                    <div>
                        <label class="small text-muted">Total Weight</label>
                        <input type="number" step="0.01"
                            name="items[${index}][total_weight]"
                            value="${item?.total_weight || ''}"
                            class="form-control" readonly>
                    </div>
                </div>
            </td>
        `;
    }
    
    /**
     * Get the price details cell HTML
     * @param {number} index - The index of the item
     * @param {Object|null} item - The item data, if any
     * @param {string} itemType - The type of item
     * @returns {string} - HTML for the cell
     */
    getPriceDetailsCell(index, item, itemType) {
        return `
            <td>
                <div class="d-flex flex-column gap-2">
                    ${itemType && itemType.includes('with-gold') ? `
                        <div class="d-flex gap-2 align-items-end">
                            <div class="d-flex flex-column gap-2">
                                <label class="small text-muted">Quantity</label>
                                <input type="number" step="0.01"
                                    name="items[${index}][quantity]" 
                                    class="form-control quantity-input" 
                                    placeholder="Enter quantity"
                                    value="${item?.quantity || ''}" 
                                    ${this.ro || ''}>
                            </div>
                            <div class="d-flex flex-column gap-2 w-50">
                                <label class="small text-muted">Pair</label>
                                    <select name="items[${index}][pair]" class="form-control form-select" ${this.ro || ''}>
                                        <option value="">Choose:</option>
                                        ${this.pair?.map(p => `
                                            <option value="${p.id}" ${item?.pair === p.id ? 'selected' : ''}>
                                                ${p.selection_data}
                                            </option>
                                        `).join('')}
                                    </select>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <label class="small text-muted">Gold Price</label>
                            <input type="number" step="0.01"
                                name="items[${index}][gold]" 
                                class="form-control gold-input" 
                                placeholder="Enter gold price"
                                value="${item?.gold || ''}" 
                                ${this.ro || ''}>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <label class="small text-muted">Workmanship</label>
                            <div class="input-group">
                                <input type="text"
                                    name="items[${index}][workmanship]"
                                    value="${item?.workmanship || ''}"
                                    class="form-controll workmanship-input" 
                                    placeholder="Enter value or FOC"
                                    ${this.ro || ''}>
                                <button type="button" class="btn btn-outline-secondary foc-btn" ${this.ro || ''}>FOC</button>
                            </div>
                        </div>
                    ` : `
                        <div class="d-flex gap-2 align-items-end">
                            <div class="d-flex flex-column gap-2">
                                <label class="small text-muted">Quantity</label>
                                <input type="number" step="0.01"
                                    name="items[${index}][quantity]" 
                                    class="form-control quantity-input" 
                                    placeholder="Enter quantity"
                                    value="${item?.quantity || ''}" 
                                    ${this.ro || ''}>
                            </div>
                            <div class="d-flex flex-column gap-2 w-50">
                                <label class="small text-muted">Pair</label>
                                    <select name="items[${index}][pair]" class="form-control form-select" ${this.ro || ''}>
                                        <option value="">Choose:</option>
                                        ${this.pair?.map(p => `
                                            <option value="${p.id}" ${item?.pair === p.id ? 'selected' : ''}>
                                                ${p.selection_data}
                                            </option>
                                        `).join('')}
                                    </select>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <label class="small text-muted">Unit Price</label>
                            <input type="number" step="0.01"
                                name="items[${index}][unit_price]" 
                                class="form-control unit-price-input" 
                                placeholder="Enter unit price"
                                value="${item?.unit_price || ''}" 
                                ${this.ro || ''}>
                        </div>
                    `}
                </div>
            </td>
        `;
    }
    
    /**
     * Create an HTML row for an item
     * @param {number} index - The index of the item
     * @param {Object|null} item - The item data, if any
     * @param {string} itemType - The type of item
     * @returns {string} - HTML for the row
     */
    createItemRow(index, item = null, itemType = 'single-with-gold') {
        return `
            <tr class="${this.itemClassName}" data-item-id="${item?.id || ''}" data-item-type="${itemType}">
                <input type="hidden" name="items[${index}][id]" value="${item?.id || ''}">
                <input type="hidden" name="items[${index}][invoice_id]" value="${item?.invoice_id || ''}">
                <input type="hidden" name="items[${index}][item_type]" value="${itemType}">
                ${this.getProductDetailsCell(index, item, itemType)}
                ${this.getWeightDetailsCell(index, item)}
                ${this.getPriceDetailsCell(index, item, itemType)}
                <td>
                    <input type="number" step="0.01" name="items[${index}][total]"
                        value="${item?.total || ''}"
                        class="form-control total-input" readonly>
                    <input type="hidden" name="items[${index}][remark_total]"
                        value="${item?.remark_total || '0.00'}"
                        class="remark-total-input">
                </td>
                <td>
                    <textarea name="items[${index}][remark]" class="form-control remark-input" rows="2"
                        style="min-height: 60px; resize: vertical;" ${this.ro || ''}>${item?.remark || ''}</textarea>
                </td>
                ${(!this.ro) ? `
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                    </td>
                ` : ''}
            </tr>
        `;
    }
    
    /**
     * Calculate total weight for a row
     * @param {HTMLElement} row - The row element
     */
    calculateTotalWeight(row) {
        if (!row) {
            console.warn('Row not provided for total weight calculation');
            return;
        }
        
        const weightInput = row.querySelector('.weight-input');
        const wastageInput = row.querySelector('.wastage-input');
        const totalWeightInput = row.querySelector('input[name$="[total_weight]"]');
        
        if (!weightInput || !totalWeightInput) {
            console.warn('Required inputs not found for total weight calculation');
            return;
        }
        
        const weight = parseFloat(weightInput.value) || 0;
        const wastage = parseFloat(wastageInput?.value) || 0;
        
        // Calculate total weight by directly adding weight and wastage
        const totalWeight = weight + (weight * (wastage / 100));
        totalWeightInput.value = (Math.round(totalWeight * 100) / 100).toFixed(2);
        
        // Trigger return balance calculation if it exists
        if (typeof calculateReturnBalance === 'function') {
            const remarkInput = row.querySelector('.remark-input');
            if (remarkInput) {
                calculateReturnBalance(remarkInput);
            }
        }
        
        this.calculateTotal(row);
    }
    
    /**
     * Calculate total price for a row
     * @param {HTMLElement} row - The row element
     */
    calculateTotal(row) {
        if (!row) {
            console.warn('Row not provided for total calculation');
            return;
        }
        
        const totalInput = row.querySelector('.total-input');
        if (!totalInput) {
            console.warn('Total input not found for calculation');
            return;
        }
        
        const itemType = row.getAttribute('data-item-type') || '';
        let total = 0;
        
        if (itemType.includes('with-gold')) {
            const goldInput = row.querySelector('.gold-input');
            const workmanshipInput = row.querySelector('.workmanship-input');
            
            if (!goldInput) {
                console.warn('Gold input not found for gold item calculation');
                return;
            }
            
            const goldPrice = parseFloat(goldInput.value) || 0;
            let workmanship = 0;
            
            // Handle workmanship - could be a number, "FOC", or empty
            if (workmanshipInput && workmanshipInput.value && workmanshipInput.value.toUpperCase() !== 'FOC') {
                workmanship = parseFloat(workmanshipInput.value) || 0;
            }
            
            total = goldPrice + workmanship;
        } else {
            const quantityInput = row.querySelector('.quantity-input');
            const unitPriceInput = row.querySelector('.unit-price-input');
            
            if (!quantityInput || !unitPriceInput) {
                console.warn('Required inputs not found for non-gold item calculation');
                return;
            }
            
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            total = quantity * unitPrice;
        }
        
        totalInput.value = total.toFixed(2);
        this.updateDisplayTotals();
    }
    
    /**
     * Update the display totals in the footer
     */
    updateDisplayTotals() {
        const totals = Array.from(document.querySelectorAll('.total-input'))
            .map(input => parseFloat(input.value) || 0);
        const subtotal = totals.reduce((sum, value) => sum + value, 0);
        
        const subtotalInputs = document.querySelectorAll('.subtotal-input');
        if (subtotalInputs.length > 0) {
            subtotalInputs.forEach(input => {
                input.value = subtotal.toFixed(2);
            });
        } else {
            console.warn('No subtotal inputs found');
        }
    }
    
    /**
     * Load invoice details from the server
     * @param {string} invoice_no - The invoice number
     */
    loadInvoiceDetails(invoice_no) {
        // Check if we're in edit mode by looking for an ID
        const isEditMode = document.querySelector('input[name="id"]')?.value;
        const oldItems = window.oldItems || [];
        
        fetch(`/${this.formType}/get-invoice-details/${invoice_no}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update the customer information in the form
                const companyNameElement = document.getElementById('company_name');
                if (companyNameElement) {
                    companyNameElement.textContent = data.company_name || '';
                }
                
                const customerAddressElement = document.getElementById('customer_address');
                if (customerAddressElement) {
                    customerAddressElement.textContent = data.address || '';
                }
                
                // Make the invoice details section visible
                if (this.invoiceDetails) {
                    this.invoiceDetails.style.display = 'block';
                }
                
                // Only update items if we're not in edit mode and there's no old input data
                if (!isEditMode && (!oldItems || oldItems.length === 0)) {
                    // Clear existing items
                    this.itemsTable.innerHTML = '';
                    
                    // Add invoice items
                    if (data.items && data.items.length > 0) {
                        data.items.forEach((item, index) => {
                            // Copy tax details from the first item to the tax details section
                            if (index === 0) {
                                this.populateTaxDetails(item);
                            }
                            
                            const newItem = {
                                ...item,
                                itemType: item.item_type || 'single-with-gold'
                            };
                            const row = this.createItemRow(index, newItem, newItem.itemType);
                            this.itemsTable.insertAdjacentHTML('beforeend', row);
                        });
                        
                        // Update item index for future additions
                        this.itemIndex = data.items.length;
                        
                        // Initialize Select2 for all new rows
                        document.querySelectorAll(`.${this.itemClassName}`).forEach(row => {
                            if (!row.querySelector('.particulars-input')) { // Only for single items
                                this.initializeSelect2ForRow(row);
                            }
                        });
                        
                        // Setup event listeners for new rows
                        this.setupCalculationListeners();
                        
                        // Update totals
                        this.updateDisplayTotals();
                        
                        // Trigger return balance calculations if ReturnBalanceCalculator exists
                        if (typeof ReturnBalanceCalculator !== 'undefined') {
                            setTimeout(() => {
                                document.querySelectorAll('.remark-input').forEach(remarkInput => {
                                    if (remarkInput.value.trim()) {
                                        ReturnBalanceCalculator.calculateReturnBalance(remarkInput);
                                    }
                                });
                            }, 500);
                        }
                    }
                }
            })
            .catch(error => {
                console.error(`Error loading invoice details: ${error}`);
                this.clearInvoiceDetails();
            });
    }
    
    /**
     * Clear all invoice details from the display
     */
    clearInvoiceDetails() {
        // Clear all displayed customer information
        const companyNameElement = document.getElementById('company_name');
        if (companyNameElement) {
            companyNameElement.textContent = '';
        }
        
        const customerAddressElement = document.getElementById('customer_address');
        if (customerAddressElement) {
            customerAddressElement.textContent = '';
        }
        
        // Hide the invoice details section
        if (this.invoiceDetails) {
            this.invoiceDetails.style.display = 'none';
        }
        
        // Check if we're in edit mode
        const isEditMode = document.querySelector('input[name="id"]')?.value;
        
        // Only clear items if we're not in edit mode
        if (!isEditMode && this.itemsTable) {
            this.itemsTable.innerHTML = '';
            this.itemIndex = 0;
            this.updateDisplayTotals();
        }
    }
    
    /**
     * Populate tax details from invoice item
     * @param {Object} item - The item data
     */
    populateTaxDetails(item) {
        const taxFields = [
            'currency_code',
            'classification',
            'tax_type',
            'tax_rate',
            'tax_amount',
            'tax_exemption',
            'amount_tax_exemption'
        ];
        
        taxFields.forEach(field => {
            const input = document.querySelector(`input[name="${field}"]`);
            if (input && item[field]) {
                input.value = item[field];
            }
        });
    }
    
    /**
     * Initialize operations to perform on page load
     */
    initializeOnLoad() {
        // When page loads, check if an invoice is already selected
        const invoiceSelect = document.querySelector('.invoice-select2');
        if (invoiceSelect && invoiceSelect.value) {
            this.loadInvoiceDetails(invoiceSelect.value);
        }
        
        // Calculate initial values for existing items
        if (document.querySelectorAll(`.${this.itemClassName}`).length > 0) {
            document.querySelectorAll(`.${this.itemClassName}`).forEach(row => {
                this.calculateTotalWeight(row);
                this.calculateTotal(row);
            });
            
            this.updateDisplayTotals();
        }
    }
}

// Export the FormHandler class for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormHandler;
} 