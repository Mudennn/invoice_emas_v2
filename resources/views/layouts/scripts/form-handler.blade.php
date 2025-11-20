<script>
    /**
     * Form Handler - Shared JavaScript for Invoice/Credit/Debit/Refund Notes
     * 
     * This file contains shared functionality for handling form operations across
     * all note types (invoice, credit note, debit note, refund note).
     */

    let focButtonHandlerInitialized = false;

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

            // console.log(`Initializing FormHandler for ${this.formType}`);

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
                'self_billed_invoices': {
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

            // Simplified approach to find the items table
            // First try to find a table within a container with the form type ID
            // If not found, look for any table with the appropriate class
            // Finally, fall back to any table-responsive tbody
            this.itemsTable = this.findItemsTable();

            // Log information about what we found
            // console.log(`Found items table: ${this.itemsTable ? 'Yes' : 'No'}`);
            // console.log(`Found add item dropdown: ${this.addItemDropdown ? 'Yes' : 'No'}`);
            // console.log(`Found invoice details: ${this.invoiceDetails ? 'Yes' : 'No'}`);

            // Initialize tracking variables
            this.deletedItems = [];
            this.itemIndex = 0;

            // Initialize the form
            this.init();
        }

        /**
         * Find the items table using multiple fallback strategies
         * @returns {HTMLElement|null} - The items table element or null if not found
         */
        findItemsTable() {
            // Try several strategies to find the table

            // Strategy 1: Look for a table inside a container with a specific ID
            const formTypeMap = {
                'invoices': 'invoice-items',
                'self_billed_invoices': 'invoice-items',
                'credit_notes': 'credit_notes-items',
                'debit_notes': 'debit_notes-items',
                'refund_notes': 'refund_notes-items'
            };

            const containerId = formTypeMap[this.formType];
            if (containerId) {
                const container = document.getElementById(containerId);
                if (container) {
                    const table = container.querySelector('tbody');
                    if (table) return table;
                }
            }

            // Strategy 2: Look for a table with a class related to the form type
            const tableClass = this.formType.replace('_', '-') + '-table';
            const classTable = document.querySelector(`.${tableClass} tbody`);
            if (classTable) return classTable;

            // Strategy 3: Look for any table with items-table class
            const itemsTable = document.querySelector('.items-table tbody');
            if (itemsTable) return itemsTable;

            // Strategy 4: Find the first table-responsive tbody in the form
            const form = document.querySelector('form');
            if (form) {
                const tableInForm = form.querySelector('.table-responsive tbody');
                if (tableInForm) return tableInForm;
            }

            // Strategy 5: Last resort - any table-responsive tbody on the page
            return document.querySelector('.table-responsive tbody');
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
            // Set a flag to ensure we only set up dropdown handlers once
            if (!this.dropdownHandlersInitialized) {
                this.setupDropdownHandlers();
                this.dropdownHandlersInitialized = true;
            }

            // Setup FOC button click handlers
            this.setupFocButtonHandlers();

            // Setup event delegation for remove button
            this.setupRemoveButtonHandlers();

            // Perform actions when page loads
            this.initializeOnLoad();

            // Track last operation to prevent duplicates
            this.lastOperationId = null;

            // console.log(`Form handler initialized for ${this.formType}`);
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

            // Check item limit for button state - ensure this runs after DOM is ready
            setTimeout(() => {
                this.checkItemLimit();
            }, 500);
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
                // console.warn('Add item dropdown not found, skipping dropdown observer setup');
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

            observer.observe(this.addItemDropdown, {
                attributes: true
            });

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
                // console.warn('Add item dropdown not found, skipping dropdown handlers setup');
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

            // Remove any existing event listeners to prevent duplicates
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                // Clone and replace to remove all event listeners
                const newItem = item.cloneNode(true);
                item.parentNode.replaceChild(newItem, item);
            });

            // Handle dropdown menu item clicks
            document.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation(); // Prevent event bubbling

                    // console.log('Dropdown item clicked');

                    const itemType = e.target.getAttribute('data-item-type');
                    if (!itemType) {
                        console.error('Item type not found in dropdown item');
                        return;
                    }

                    // console.log(`Adding new item of type: ${itemType}`);

                    // Check if adding another item would exceed the limit
                    const existingItems = document.querySelectorAll(`.${this.itemClassName}`);
                    if (existingItems.length >= 8) {
                        alert('Maximum of 8 items allowed per form.');
                        return;
                    }

                    // Add the item only once
                    this.addNewItem(itemType);

                    // Check limit after adding
                    this.checkItemLimit();

                    // Close the dropdown menu after adding the item
                    const dropdownMenu = this.addItemDropdown.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                        dropdownMenu.classList.remove('show');
                    }

                    // If using Bootstrap 4+ dropdown, hide via jQuery
                    if (typeof $ !== 'undefined' && typeof $.fn.dropdown !== 'undefined') {
                        $(this.addItemDropdown).dropdown('hide');
                    }
                });
            });
        }

        /**
         * Setup FOC button click handlers
         */
        setupFocButtonHandlers() {
            if (focButtonHandlerInitialized) return; // Prevent multiple bindings
            focButtonHandlerInitialized = true;

            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('foc-btn')) {
                    const unitPriceInput = e.target.closest('.input-group').querySelector('.unit-price-input');
                    if (!unitPriceInput) {
                        // console.warn('FOC button: .unit-price-input not found in .input-group');
                        return;
                    }
                    unitPriceInput.value = 'FOC';
                    // console.log('FOC button clicked, set unit price to FOC');
                    // Trigger calculation update
                    const row = e.target.closest('tr');
                    this.calculateTotal(row);
                    // Also trigger input and change events to ensure all listeners fire
                    unitPriceInput.dispatchEvent(new Event('input', { bubbles: true }));
                    unitPriceInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }

        /**
         * Setup event delegation for remove button
         */
        setupRemoveButtonHandlers() {
            // Simplify the approach - use document-level event delegation instead
            // This avoids the need to find specific container elements
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-item')) {
                    const row = e.target.closest('tr');
                    if (!row) return;

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

                    // Update button state after removing an item
                    this.checkItemLimit();

                    // Update return balance total if ReturnBalanceCalculator exists
                    if (typeof ReturnBalanceCalculator !== 'undefined') {
                        setTimeout(() => {
                            ReturnBalanceCalculator.updateTotalReturnBalance();
                        }, 100);
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
            // Count all rows that are actual items (have data-item-id, even if empty for new items)
            // This is more reliable than counting by CSS class since existing items might have wrong classes
            const allItemRows = document.querySelectorAll('tbody tr[data-item-id]');
            const itemCount = allItemRows.length;

            // console.log(`checkItemLimit: Found ${itemCount} items with data-item-id attribute`);

            // Skip if addItemDropdown doesn't exist
            if (!this.addItemDropdown) {
                // console.warn('Add item dropdown not found, skipping limit check');
                return;
            }

            // Look for dropdown container or d-inline-block container (in case it was previously disabled)
            let dropdownContainer = this.addItemDropdown.closest('.dropdown');
            if (!dropdownContainer) {
                dropdownContainer = this.addItemDropdown.closest('.d-inline-block');
            }
            if (!dropdownContainer) {
                // Fallback: look for immediate parent
                dropdownContainer = this.addItemDropdown.parentElement;
            }
            
            if (!dropdownContainer) {
                // console.warn('Dropdown container not found, skipping limit check');
                return;
            }

            // console.log(`Found container with classes: ${dropdownContainer.className}`);

            if (itemCount >= 8) {
                // console.log('Disabling add item button - 8 or more items found');
                this.addItemDropdown.disabled = true;
                this.addItemDropdown.title = 'Maximum 8 items allowed';
                // Completely remove dropdown class to prevent dropdown from showing
                dropdownContainer.classList.remove('dropdown');
                dropdownContainer.classList.add('d-inline-block'); // Keep layout intact
            } else {
                // console.log('Enabling add item button - less than 8 items found');
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
            this.checkItemLimit();

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
            document.querySelectorAll('.gold-input, .quantity-input, .unit-price-input').forEach(input => {
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
                    placeholder: $(this).data('type') === 'reference' ? "Select Code" :
                        "Select Product",
                    width: '100%'
                });
            });
        }

        /**
         * Add a new item to the form
         * @param {string} itemType - The type of item to add
         */
        addNewItem(itemType) {
            // Find the items table again in case the DOM has changed
            if (!this.itemsTable) {
                this.itemsTable = this.findItemsTable();
            }

            if (!this.itemsTable) {
                console.error(`Items table not found for form type: ${this.formType}`);
                alert(`Could not find the items table. Please refresh the page and try again.`);
                return;
            }

            // Generate a unique ID for this operation to prevent duplicate additions
            const operationId = Date.now();
            if (this.lastOperationId === operationId) {
                // console.warn('Duplicate add operation detected and prevented');
                return;
            }
            this.lastOperationId = operationId;

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
                            <label class="small text-muted">Unit Price</label>
                            <div class="input-group">
                                <input type="text"
                                    name="items[${index}][unit_price]"
                                    value="${item?.unit_price || ''}"
                                    class="form-controll unit-price-input" 
                                    placeholder="Enter unit price"
                                    ${this.ro || ''}>
                                <button type="button" class="btn btn-outline-secondary foc-btn" ${this.ro || ''}>FOC</button>
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
                            <label class="small text-muted">KT</label>
                            <select name="items[${index}][kt]" 
                                class="form-control form-select kt-input" 
                                ${this.ro || ''}>
                                <option value="">Choose:</option>
                                <option value="916" ${(String(item?.kt) === '916') ? 'selected' : ''}>916</option>
                                <option value="835" ${(String(item?.kt) === '835') ? 'selected' : ''}>835</option>
                                <option value="750W" ${(String(item?.kt) === '750W') ? 'selected' : ''}>750W</option>
                                <option value="750R" ${(String(item?.kt) === '750R') ? 'selected' : ''}>750R</option>
                                <option value="750Y" ${(String(item?.kt) === '750Y') ? 'selected' : ''}>750Y</option>
                                <option value="375W" ${(String(item?.kt) === '375W') ? 'selected' : ''}>375W</option>
                                <option value="375R" ${(String(item?.kt) === '375R') ? 'selected' : ''}>375R</option>
                            </select>
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
                            <div class="input-group">
                                <input type="text"
                                    name="items[${index}][unit_price]"
                                    value="${item?.unit_price || ''}"
                                    class="form-controll unit-price-input" 
                                    placeholder="Enter unit price"
                                    ${this.ro || ''}>
                                <button type="button" class="btn btn-outline-secondary foc-btn" ${this.ro || ''}>FOC</button>
                            </div>
                        </div>
                        <div>
                            <label class="small text-muted">Pure Gold</label>
                            <select name="items[${index}][pure_gold]"
                                class="form-control form-select pure-gold-input"
                                ${this.ro || ''}>
                                <option value="">Choose:</option>
                                <option value="916" ${(item?.pure_gold === '916') ? 'selected' : ''}>916</option>
                                <option value="835" ${(item?.pure_gold === '835') ? 'selected' : ''}>835</option>
                                <option value="750W" ${(item?.pure_gold === '750W') ? 'selected' : ''}>750W</option>
                                <option value="750R" ${(item?.pure_gold === '750R') ? 'selected' : ''}>750R</option>
                                <option value="750Y" ${(item?.pure_gold === '750Y') ? 'selected' : ''}>750Y</option>
                                <option value="375W" ${(item?.pure_gold === '375W') ? 'selected' : ''}>375W</option>
                                <option value="375R" ${(item?.pure_gold === '375R') ? 'selected' : ''}>375R</option>
                            </select>
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
                    <textarea name="items[${index}][remark]" class="form-control" rows="2"
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
                // console.warn('Row not provided for total weight calculation');
                return;
            }

            const weightInput = row.querySelector('.weight-input');
            const wastageInput = row.querySelector('.wastage-input');
            const totalWeightInput = row.querySelector('input[name$="[total_weight]"]');

            if (!weightInput || !totalWeightInput) {
                // console.warn('Required inputs not found for total weight calculation');
                return;
            }

            const weight = parseFloat(weightInput.value) || 0;
            const wastage = parseFloat(wastageInput?.value) || 0;

            // Calculate total weight by directly adding weight and wastage
            const totalWeight = weight + (weight * (wastage / 100));
            totalWeightInput.value = ((Math.round((totalWeight + Number.EPSILON) * 100) / 100).toFixed(2));
            // totalWeightInput.value = totalWeight.toFixed(2);

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
                // console.warn('Row not provided for total calculation');
                return;
            }

            const totalInput = row.querySelector('.total-input');
            if (!totalInput) {
                // console.warn('Total input not found for calculation');
                return;
            }

            const itemType = row.getAttribute('data-item-type') || '';
            let total = 0;

            if (itemType.includes('with-gold')) {
                const goldInput = row.querySelector('.gold-input');
                const quantityInput = row.querySelector('.quantity-input');
                const unitPriceInput = row.querySelector('.unit-price-input');

                if (!goldInput || !quantityInput || !unitPriceInput) {
                    // console.warn('Gold input not found for gold item calculation');
                    return;
                }

                const goldPrice = parseFloat(goldInput.value) || 0;
                const quantity = parseFloat(quantityInput.value) || 0;
                let unitPrice = 0;

                // Handle workmanship - could be a number, "FOC", or empty
                if (unitPriceInput && unitPriceInput.value && unitPriceInput.value.toUpperCase() !== 'FOC') {
                    unitPrice = parseFloat(unitPriceInput.value) || 0;
                }

                total = (quantity * unitPrice ) + goldPrice ;
            } else {
                const quantityInput = row.querySelector('.quantity-input');
                const unitPriceInput = row.querySelector('.unit-price-input');

                if (!quantityInput || !unitPriceInput) {
                    // console.warn('Required inputs not found for non-gold item calculation');
                    return;
                }

                const quantity = parseFloat(quantityInput.value) || 0;
                let unitPrice = 0;
                if (unitPriceInput && unitPriceInput.value && unitPriceInput.value.toUpperCase() !== 'FOC') {
                    unitPrice = parseFloat(unitPriceInput.value) || 0;
                }

                // const quantity = parseFloat(quantityInput.value) || 0;
                // const unitPrice = parseFloat(unitPriceInput.value) || 0;
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
                // console.warn('No subtotal inputs found');
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
                                            ReturnBalanceCalculator.calculateReturnBalance(
                                                remarkInput);
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

            // Ensure item limit is checked after everything is loaded
            setTimeout(() => {
                this.checkItemLimit();
            }, 1000);
            
            // Also check when window is fully loaded
            window.addEventListener('load', () => {
                setTimeout(() => {
                    this.checkItemLimit();
                }, 500);
            });
        }
    }

    // Export the FormHandler class for use in other files
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = FormHandler;
    }
</script>
