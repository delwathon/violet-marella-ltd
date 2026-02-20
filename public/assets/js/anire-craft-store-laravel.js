/**
 * VIOLET MARELLA LIMITED - ANIRE CRAFT STORE POS FUNCTIONALITY (Laravel Integration)
 * Point of Sale system with inventory management
 */

// Anire Craft Store state and data
const LoungeState = {
    products: [],
    cart: [],
    currentCustomer: null,
    currentSale: null,
    transactions: [],
    categories: [],
    pagination: {
        currentPage: 1,
        itemsPerPage: 12,
        totalItems: 0
    },
    payment: {
        method: null,
        amount: 0,
        change: 0
    }
};

const AnireCraftStoreConfig = window.anireCraftStoreConfig || {};
const ANIRE_CRAFT_STORE_BASE_PATH = (AnireCraftStoreConfig.basePath || '/app/anire-craft-store').replace(/\/$/, '');

function buildAnireCraftStoreUrl(path) {
    return `${ANIRE_CRAFT_STORE_BASE_PATH}${path}`;
}

/**
 * Initialize Lounge POS
 */
function initializeLounge() {
    console.log('Initializing Anire Craft Store POS with Laravel backend...');

    // Initialize components
    initializeProductSearch();
    initializeCart();

    // Bind events
    bindLoungeEvents();

    // Update displays
    updateCartDisplay();
    updateCartSummary();

    console.log('Anire Craft Store POS initialized successfully');
}

/**
 * Initialize Product Search
 */
function initializeProductSearch() {
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');

    if (searchInput) {
        const debouncedSearch = debounce((value) => {
            searchProducts(value);
        }, 300);

        searchInput.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', (e) => {
            filterProductsByCategory(e.target.value);
        });
    }
}

/**
 * Initialize Cart
 */
function initializeCart() {
    updateCartDisplay();
    updateCartSummary();
}

/**
 * Bind Lounge Events
 */
function bindLoungeEvents() {
    // Add to cart buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart')) {
            const button = e.target.closest('.add-to-cart');
            const productId = button.dataset.productId;
            const quantityInput = button.closest('.product-card').querySelector('.quantity-input');
            const quantity = parseInt(quantityInput?.value) || 1;

            addToCart(productId, quantity);
        }
    });

    // Clear cart button
    const clearCartBtn = document.querySelector('.btn-outline-danger');
    if (clearCartBtn && clearCartBtn.textContent.includes('Clear All')) {
        clearCartBtn.addEventListener('click', clearCart);
    }

    // Checkout button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', processCheckout);
    }

    // Payment method buttons
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(btn => {
        btn.addEventListener('click', handlePaymentMethodSelect);
    });

    // Cash input for change calculation
    const amountReceived = document.getElementById('amountReceived');
    if (amountReceived) {
        amountReceived.addEventListener('input', calculateChange);
    }

    // Barcode scanner simulation
    document.addEventListener('keydown', handleBarcodeInput);
}

/**
 * Search Products
 */
async function searchProducts(searchTerm) {
    try {
        const response = await fetch(`${buildAnireCraftStoreUrl('/products/search')}?q=${encodeURIComponent(searchTerm)}`);
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                LoungeState.products = data.data || [];
                LoungeState.pagination.totalItems = data.total || 0;
                updateProductGrid(data.data);
            }
        }
    } catch (error) {
        console.error('Error searching products:', error);
        showNotification('Error searching products', 'error');
    }
}

/**
 * Filter Products by Category
 */
async function filterProductsByCategory(categoryId) {
    try {
        let url = buildAnireCraftStoreUrl('/products/search');
        if (categoryId) {
            url += `?category_id=${categoryId}`;
        }
        
        const response = await fetch(url);
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                LoungeState.products = data.data || [];
                updateProductGrid(data.data);
            }
        }
    } catch (error) {
        console.error('Error filtering products:', error);
        showNotification('Error filtering products', 'error');
    }
}

/**
 * Update Product Grid with AJAX data
 */
function updateProductGrid(products) {
    const productGrid = document.getElementById('productGrid');
    if (!productGrid) return;

    if (!products || products.length === 0) {
        productGrid.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products found</h5>
                <p class="text-muted">Try adjusting your search or filters.</p>
            </div>
        `;
        return;
    }

    const productsHtml = products.map(product => {
        const hasImage = product.image && product.image !== '';
        const stockDisplay = product.track_stock 
            ? `Stock: ${product.stock_quantity}` 
            : '<i class="fas fa-infinity text-success"></i>';
        
        return `
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="product-card card h-100" data-product-id="${product.id}">
                    <div class="card-body d-flex flex-column">
                        <div class="product-image mb-2">
                            ${hasImage 
                                ? `<img src="/storage/${product.image}" class="img-fluid rounded" alt="${product.name}">`
                                : `<div class="placeholder-image bg-light rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <i class="fas fa-image text-muted fa-2x"></i>
                                   </div>`
                            }
                        </div>
                        <h6 class="card-title">${product.name}</h6>
                        <p class="card-text text-muted small">${product.category ? product.category.name : 'N/A'}</p>
                        <div class="product-info">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="price fw-bold">₦${parseFloat(product.price).toFixed(2)}</span>
                                <span class="stock text-muted small">${stockDisplay}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control form-control-sm quantity-input" value="1" min="1" max="${product.track_stock ? product.stock_quantity : 999}">
                                <button class="btn btn-primary btn-sm add-to-cart" data-product-id="${product.id}">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    productGrid.innerHTML = `<div class="row">${productsHtml}</div>`;
    
    console.log('Product grid updated with', products.length, 'products');
}

/**
 * Add Product to Cart
 */
async function addToCart(productId, quantity = 1) {
    try {
        const response = await fetch(buildAnireCraftStoreUrl('/cart/add'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });

        const data = await response.json();
        
        if (response.ok && data.success) {
            updateCartDisplay();
            updateCartSummary();
            showNotification(`${data.product_name || 'Product'} added to cart`, 'success');
        } else {
            showNotification(data.message || 'Error adding to cart', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Error adding product to cart', 'error');
    }
}

/**
 * Update Cart Display
 */
function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    if (!cartItems) return;

    // Get cart from Laravel session via AJAX
    fetch(buildAnireCraftStoreUrl('/cart'))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cart && data.cart.length > 0) {
                cartItems.innerHTML = data.cart.map(item => `
                    <div class="cart-item" data-product-id="${item.product_id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="item-info">
                                <h6 class="mb-1">${item.name}</h6>
                                <small class="text-muted">₦${parseFloat(item.price).toFixed(2)} each</small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart('${item.product_id}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="quantity-controls">
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('${item.product_id}', -1)">-</button>
                                <span class="quantity mx-2">${item.quantity}</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('${item.product_id}', 1)">+</button>
                            </div>
                            <strong>₦${parseFloat(item.total_price).toFixed(2)}</strong>
                        </div>
                    </div>
                `).join('');
            } else {
                cartItems.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Cart is empty</p>
                        <small>Start scanning or selecting products</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading cart:', error);
        });
}

/**
 * Update Cart Summary
 */
function updateCartSummary() {
    // Get cart summary from Laravel
    fetch(buildAnireCraftStoreUrl('/cart/summary'))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cartItemCount').textContent = data.count || 0;
                document.getElementById('cartSubtotal').textContent = `₦${parseFloat(data.subtotal || 0).toFixed(2)}`;
                document.getElementById('cartTax').textContent = `₦${parseFloat(data.tax_amount || 0).toFixed(2)}`;
                document.getElementById('cartTotal').textContent = `₦${parseFloat(data.total || 0).toFixed(2)}`;

                // Enable/disable checkout button
                const checkoutBtn = document.getElementById('checkoutBtn');
                if (checkoutBtn) {
                    checkoutBtn.disabled = (data.count || 0) === 0;
                }
            }
        })
        .catch(error => {
            console.error('Error loading cart summary:', error);
        });
}

/**
 * Remove Item from Cart
 */
async function removeFromCart(productId) {
    try {
        const response = await fetch(buildAnireCraftStoreUrl(`/cart/remove/${productId}`), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        
        if (response.ok && data.success) {
            updateCartDisplay();
            updateCartSummary();
            showNotification('Item removed from cart', 'info');
        } else {
            showNotification(data.message || 'Error removing item', 'error');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showNotification('Error removing item from cart', 'error');
    }
}

/**
 * Update Item Quantity
 */
async function updateQuantity(productId, change) {
    try {
        const response = await fetch(buildAnireCraftStoreUrl('/cart/update'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: change
            })
        });

        const data = await response.json();
        
        if (response.ok && data.success) {
            updateCartDisplay();
            updateCartSummary();
        } else {
            showNotification(data.message || 'Error updating quantity', 'error');
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        showNotification('Error updating quantity', 'error');
    }
}

/**
 * Clear Cart
 */
async function clearCart() {
    if (!confirm('Are you sure you want to clear the cart?')) return;

    try {
        const response = await fetch(buildAnireCraftStoreUrl('/cart/clear'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        
        if (response.ok && data.success) {
            updateCartDisplay();
            updateCartSummary();
            showNotification('Cart cleared', 'info');
        } else {
            showNotification(data.message || 'Error clearing cart', 'error');
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        showNotification('Error clearing cart', 'error');
    }
}

/**
 * Process Checkout
 */
function processCheckout() {
    // Get cart summary first
    fetch(buildAnireCraftStoreUrl('/cart/summary'))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count > 0) {
                const total = data.total || 0;
                document.getElementById('paymentTotal').textContent = `₦${parseFloat(total).toFixed(2)}`;
                document.getElementById('paymentItems').textContent = data.count || 0;
                document.getElementById('paymentSubtotal').textContent = `₦${parseFloat(data.subtotal || 0).toFixed(2)}`;
                document.getElementById('paymentTax').textContent = `₦${parseFloat(data.tax_amount || 0).toFixed(2)}`;

                // Reset payment modal state
                clearSelectedCustomer();
                LoungeState.payment.method = null;
                document.querySelectorAll('.payment-method').forEach(btn => {
                    btn.classList.remove('active', 'btn-success', 'btn-primary', 'btn-info', 'btn-warning');
                    const method = btn.dataset.method;
                    if (method === 'cash') btn.classList.add('btn-outline-success');
                    else if (method === 'card') btn.classList.add('btn-outline-primary');
                    else if (method === 'transfer') btn.classList.add('btn-outline-info');
                    else if (method === 'mobile_money') btn.classList.add('btn-outline-warning');
                });
                document.getElementById('cashInput').style.display = 'none';
                document.getElementById('amountReceived').value = '';
                document.getElementById('changeAmount').textContent = '₦0';
                document.getElementById('completePaymentBtn').disabled = true;

                const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
                paymentModal.show();
            } else {
                showNotification('Cart is empty', 'warning');
            }
        })
        .catch(error => {
            console.error('Error getting cart summary:', error);
            showNotification('Error processing checkout', 'error');
        });
}

/**
 * Handle Payment Method Selection
 */
function handlePaymentMethodSelect(event) {
    const method = event.currentTarget.getAttribute('data-method');
    LoungeState.payment.method = method;

    // Remove active class from all buttons
    document.querySelectorAll('.payment-method').forEach(btn => {
        btn.classList.remove('active', 'btn-success', 'btn-primary', 'btn-info', 'btn-warning');
        const btnMethod = btn.dataset.method;
        if (btnMethod === 'cash') btn.classList.add('btn-outline-success');
        else if (btnMethod === 'card') btn.classList.add('btn-outline-primary');
        else if (btnMethod === 'transfer') btn.classList.add('btn-outline-info');
        else if (btnMethod === 'mobile_money') btn.classList.add('btn-outline-warning');
    });

    // Add active class to selected button
    event.currentTarget.classList.remove('btn-outline-success', 'btn-outline-primary', 'btn-outline-info', 'btn-outline-warning');
    event.currentTarget.classList.add('active');

    if (method === 'cash') {
        event.currentTarget.classList.add('btn-success');
    } else if (method === 'card') {
        event.currentTarget.classList.add('btn-primary');
    } else if (method === 'transfer') {
        event.currentTarget.classList.add('btn-info');
    } else if (method === 'mobile_money') {
        event.currentTarget.classList.add('btn-warning');
    }

    // Show/hide cash input
    const cashInput = document.getElementById('cashInput');
    const completeBtn = document.getElementById('completePaymentBtn');

    if (method === 'cash') {
        cashInput.style.display = 'block';
        completeBtn.disabled = true;
        // Focus on amount input
        setTimeout(() => {
            document.getElementById('amountReceived').focus();
        }, 100);
    } else {
        cashInput.style.display = 'none';
        completeBtn.disabled = false;
    }
}

/**
 * Calculate Change
 */
function calculateChange() {
    const amountReceived = parseFloat(document.getElementById('amountReceived').value) || 0;
    const totalText = document.getElementById('paymentTotal').textContent;
    const total = parseFloat(totalText.replace(/[₦,]/g, '')) || 0;
    const change = amountReceived - total;

    document.getElementById('changeAmount').textContent = `₦${Math.max(0, change).toFixed(2)}`;

    const completeBtn = document.getElementById('completePaymentBtn');
    completeBtn.disabled = amountReceived < total;
}

/**
 * Search Customers (for payment modal)
 */
async function searchCustomers() {
    const searchTerm = document.getElementById('customerSearchInput').value;
    
    if (searchTerm.length < 2) {
        showNotification('Please enter at least 2 characters to search', 'warning');
        return;
    }
    
    try {
        const response = await fetch(`${buildAnireCraftStoreUrl('/customers/search/query')}?search=${encodeURIComponent(searchTerm)}`);
        const data = await response.json();
        
        const resultsDiv = document.getElementById('customerSearchResults');
        
        if (data.success && data.customers.length > 0) {
            resultsDiv.innerHTML = data.customers.map(customer => `
                <a href="#" class="list-group-item list-group-item-action" 
                   onclick="selectCustomer(${customer.id}, '${customer.first_name} ${customer.last_name}', '${customer.phone}', '${customer.email || ''}'); return false;">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${customer.first_name} ${customer.last_name}</strong>
                            <br>
                            <small class="text-muted">${customer.phone} ${customer.email ? '• ' + customer.email : ''}</small>
                        </div>
                        <span class="badge bg-primary">${customer.customer_type}</span>
                    </div>
                </a>
            `).join('');
            resultsDiv.style.display = 'block';
        } else {
            resultsDiv.innerHTML = `
                <div class="list-group-item text-center text-muted">
                    No customers found
                </div>
            `;
            resultsDiv.style.display = 'block';
        }
    } catch (error) {
        console.error('Error searching customers:', error);
        showNotification('Failed to search customers', 'error');
    }
}

/**
 * Select Customer
 */
function selectCustomer(id, name, phone, email) {
    LoungeState.currentCustomer = { id, name, phone, email };
    
    document.getElementById('selectedCustomerId').value = id;
    document.getElementById('selectedCustomerName').textContent = name;
    document.getElementById('selectedCustomerDetails').textContent = `${phone} ${email ? '• ' + email : ''}`;
    document.getElementById('selectedCustomerDisplay').style.display = 'block';
    document.getElementById('customerSearchResults').style.display = 'none';
    document.getElementById('customerSearchInput').value = '';
}

/**
 * Clear Selected Customer
 */
function clearSelectedCustomer() {
    LoungeState.currentCustomer = null;
    document.getElementById('selectedCustomerId').value = '';
    document.getElementById('selectedCustomerDisplay').style.display = 'none';
    document.getElementById('customerSearchInput').value = '';
    document.getElementById('customerSearchResults').style.display = 'none';
}

/**
 * Quick Customer Creation
 */
async function saveQuickCustomer() {
    const data = {
        first_name: document.getElementById('quickFirstName').value,
        last_name: document.getElementById('quickLastName').value,
        phone: document.getElementById('quickPhone').value,
        email: document.getElementById('quickEmail').value,
        customer_type: document.getElementById('quickCustomerType').value
    };
    
    if (!data.first_name || !data.phone) {
        alert('Please fill in required fields');
        return;
    }
    
    try {
        const response = await fetch(buildAnireCraftStoreUrl('/customers/quick/create'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('quickCustomerModal'));
            modal.hide();
            
            // Reset form
            document.getElementById('quickCustomerForm').reset();
            
            // Show success message
            showNotification('Customer created successfully', 'success');
            
            // Optionally, select this customer in POS
            // selectCustomer(result.customer);
        } else {
            showNotification(result.message || 'Failed to create customer', 'error');
        }
    } catch (error) {
        console.error('Error creating customer:', error);
        showNotification('Error creating customer', 'error');
    }
}

/**
 * View Low Stock Items
 */
async function viewLowStock() {
    const modal = new bootstrap.Modal(document.getElementById('lowStockModal'));
    modal.show();
    
    try {
        const response = await fetch(buildAnireCraftStoreUrl('/products/low-stock/list'));
        const data = await response.json();
        
        const tbody = document.getElementById('lowStockTableBody');
        
        if (data.success && data.products.length > 0) {
            tbody.innerHTML = data.products.map(product => `
                <tr>
                    <td>
                        <strong>${product.name}</strong>
                        <br><small class="text-muted">${product.category?.name || 'N/A'}</small>
                    </td>
                    <td>${product.sku}</td>
                    <td>
                        <span class="badge bg-danger">${product.stock_quantity}</span>
                    </td>
                    <td>${product.minimum_stock_level}</td>
                    <td>
                        <a href="${buildAnireCraftStoreUrl(`/products/${product.id}/edit`)}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-edit"></i> Restock
                        </a>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <br>No low stock items
                    </td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Error loading low stock:', error);
        document.getElementById('lowStockTableBody').innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger">
                    Error loading low stock items
                </td>
            </tr>
        `;
    }
}

/**
 * View Daily Sales Report
 */
async function viewDailySales() {
    const modal = new bootstrap.Modal(document.getElementById('dailySalesModal'));
    modal.show();
    
    try {
        const response = await fetch(buildAnireCraftStoreUrl('/daily-report'));
        const data = await response.json();
        
        if (data.success) {
            const report = data.report;
            
            document.getElementById('dailySalesContent').innerHTML = `
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-dark">
                            <div class="card-body text-center">
                                <h3>₦${parseFloat(report.total_sales).toFixed(2)}</h3>
                                <p class="mb-0">Total Sales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-dark">
                            <div class="card-body text-center">
                                <h3>${report.total_transactions}</h3>
                                <p class="mb-0">Transactions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-dark">
                            <div class="card-body text-center">
                                <h3>${report.total_items}</h3>
                                <p class="mb-0">Items Sold</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h3>₦${parseFloat(report.average_transaction).toFixed(2)}</h3>
                                <p class="mb-0">Avg Transaction</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Payment Methods</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Count</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${Object.entries(report.payment_methods).map(([method, stats]) => `
                                    <tr>
                                        <td>${method.toUpperCase()}</td>
                                        <td>${stats.count}</td>
                                        <td>₦${parseFloat(stats.total).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Hourly Sales</h6>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Hour</th>
                                        <th>Count</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${Object.entries(report.hourly_sales).map(([hour, stats]) => `
                                        <tr>
                                            <td>${hour}</td>
                                            <td>${stats.count}</td>
                                            <td>₦${parseFloat(stats.total).toFixed(2)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading daily sales:', error);
        document.getElementById('dailySalesContent').innerHTML = `
            <div class="text-center text-danger">
                Error loading sales report
            </div>
        `;
    }
}

/**
 * Complete Payment
 */
async function completePayment() {
    const method = LoungeState.payment.method;

    if (!method) {
        showNotification('Please select a payment method', 'warning');
        return;
    }

    const totalText = document.getElementById('paymentTotal').textContent;
    const total = parseFloat(totalText.replace(/[₦,]/g, '')) || 0;
    const amountReceived = method === 'cash' ? parseFloat(document.getElementById('amountReceived').value) || 0 : total;
    const customerId = document.getElementById('selectedCustomerId')?.value || null;

    try {
        const completeBtn = document.getElementById('completePaymentBtn');
        completeBtn.disabled = true;
        completeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        const response = await fetch(buildAnireCraftStoreUrl('/checkout'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_method: method,
                amount_paid: amountReceived,
                customer_id: customerId
            })
        });

        const data = await response.json();
        
        if (response.ok && data.success) {
            // Clear payment state
            LoungeState.payment = { method: null, amount: 0, change: 0 };
            LoungeState.currentCustomer = null;
            
            // Reset payment form
            document.querySelectorAll('.payment-method').forEach(btn => {
                btn.classList.remove('active', 'btn-success', 'btn-primary', 'btn-info', 'btn-warning');
                const btnMethod = btn.dataset.method;
                if (btnMethod === 'cash') btn.classList.add('btn-outline-success');
                else if (btnMethod === 'card') btn.classList.add('btn-outline-primary');
                else if (btnMethod === 'transfer') btn.classList.add('btn-outline-info');
                else if (btnMethod === 'mobile_money') btn.classList.add('btn-outline-warning');
            });
            document.getElementById('cashInput').style.display = 'none';
            document.getElementById('amountReceived').value = '';
            document.getElementById('changeAmount').textContent = '₦0';
            clearSelectedCustomer();
            
            // Update displays
            updateCartDisplay();
            updateCartSummary();

            // Close modal
            const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            if (paymentModal) {
                paymentModal.hide();
            }

            // Show success message
            showNotification(`Transaction completed - Receipt: ${data.receipt_number}`, 'success');

            // Reload page to update stats and transactions
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showNotification(data.message || 'Error processing payment', 'error');
            completeBtn.disabled = false;
            completeBtn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Payment';
        }
    } catch (error) {
        console.error('Error completing payment:', error);
        showNotification('Error processing payment', 'error');
        const completeBtn = document.getElementById('completePaymentBtn');
        completeBtn.disabled = false;
        completeBtn.innerHTML = '<i class="fas fa-check me-2"></i>Complete Payment';
    }
}

/**
 * Print Receipt
 */
function printReceipt(saleId) {
    window.open(buildAnireCraftStoreUrl(`/sales/${saleId}/receipt`), '_blank');
}

/**
 * Update Transactions Table
 */
function updateTransactionsTable() {
    const transactionsTable = document.querySelector('#transactionsTable tbody');
    if (!transactionsTable) return;

    // The transactions are already loaded by Blade template
    console.log('Transactions table updated');
}

/**
 * Update Statistics
 */
function updateStats() {
    // Statistics are already loaded by Blade template
    console.log('Statistics updated');
}

/**
 * Handle Barcode Input
 */
function handleBarcodeInput(event) {
    // Simulate barcode scanner input (when Enter is pressed in search field)
    if (event.key === 'Enter' && event.target.id === 'productSearch') {
        const barcode = event.target.value;
        // Search for product by barcode
        searchProducts(barcode);
    }
}

/**
 * Utility Functions
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    // Simple notification system
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';

    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeLounge, 100);
});

// Export Anire Craft Store functions for global access
window.VioletMarellaAnireCraftStore = {
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    processCheckout,
    completePayment,
    searchCustomers,
    selectCustomer,
    clearSelectedCustomer,
    scanBarcode: () => {
        showNotification('Barcode scanner activated. Enter barcode in search field.', 'info');
        document.getElementById('productSearch').focus();
    },
    openCashDrawer: () => {
        showNotification('Cash drawer opened', 'info');
    },
    startSale: () => {
        const form = document.getElementById('customerForm');
        const customerName = document.getElementById('customerName')?.value;
        const customerPhone = document.getElementById('customerPhone')?.value;
        const isRegular = document.getElementById('isRegularCustomer')?.checked;

        LoungeState.currentCustomer = {
            name: customerName || 'Walk-in Customer',
            phone: customerPhone,
            isRegular: isRegular
        };

        const modal = bootstrap.Modal.getInstance(document.getElementById('newSaleModal'));
        if (modal) {
            modal.hide();
        }

        showNotification(`Sale started for ${LoungeState.currentCustomer.name}`, 'success');
    }
};
window.VioletMarellaLounge = window.VioletMarellaAnireCraftStore;
