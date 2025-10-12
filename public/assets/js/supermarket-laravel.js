/**
 * VIOLET MARELLA LIMITED - SUPERMARKET POS FUNCTIONALITY (Laravel Integration)
 * Point of Sale system with inventory management
 */

// Supermarket state and data
const SupermarketState = {
    products: [],
    cart: [],
    currentCustomer: null,
    currentSale: null,
    transactions: [],
    categories: [],
    pagination: {
        currentPage: 1,
        itemsPerPage: 20,
        totalItems: 0
    },
    payment: {
        method: null,
        amount: 0,
        change: 0
    }
};

/**
 * Initialize Supermarket POS
 */
function initializeSupermarket() {
    console.log('Initializing supermarket POS with Laravel backend...');

    // Load data from Laravel
    loadSupermarketData();

    // Initialize components
    initializeProductSearch();
    initializeCart();

    // Bind events
    bindSupermarketEvents();

    // Update displays
    updateProductGrid();
    updateTransactionsTable();
    updateStats();

    console.log('Supermarket POS initialized successfully');
}

/**
 * Load Supermarket Data from Laravel
 */
async function loadSupermarketData() {
    try {
        // Load products
        const response = await fetch('/supermarket/products/search');
        if (response.ok) {
            const data = await response.json();
            SupermarketState.products = data.data || [];
            SupermarketState.pagination.totalItems = data.total || 0;
        }

        // Load recent transactions
        const transactionsResponse = await fetch('/supermarket/sales');
        if (transactionsResponse.ok) {
            const transactionsData = await transactionsResponse.json();
            SupermarketState.transactions = transactionsData.data || [];
        }

        console.log('Loaded', SupermarketState.products.length, 'products from Laravel');
    } catch (error) {
        console.error('Error loading data:', error);
        // Fallback to static data if API fails
        loadFallbackData();
    }
}

/**
 * Load Fallback Data
 */
function loadFallbackData() {
    // Use the products already loaded in the Blade template
    const productCards = document.querySelectorAll('.product-card');
    SupermarketState.products = Array.from(productCards).map(card => {
        const productId = card.dataset.productId;
        const name = card.querySelector('.card-title')?.textContent || '';
        const priceText = card.querySelector('.price')?.textContent || '₦0';
        const price = parseFloat(priceText.replace(/[₦,]/g, '')) || 0;
        const stockText = card.querySelector('.stock')?.textContent || '0';
        const stock = parseInt(stockText.replace(/\D/g, '')) || 0;

        return {
            id: productId,
            name: name,
            price: price,
            stock: stock,
            category: 'general'
        };
    });

    console.log('Loaded', SupermarketState.products.length, 'products from DOM');
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
 * Bind Supermarket Events
 */
function bindSupermarketEvents() {
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
        const response = await fetch(`/supermarket/products/search?q=${encodeURIComponent(searchTerm)}`);
        if (response.ok) {
            const data = await response.json();
            SupermarketState.products = data.data || [];
            SupermarketState.pagination.totalItems = data.total || 0;
            updateProductGrid();
        }
    } catch (error) {
        console.error('Error searching products:', error);
        // Fallback to client-side search
        filterProductsLocally(searchTerm);
    }
}

/**
 * Filter Products Locally
 */
function filterProductsLocally(searchTerm) {
    const productCards = document.querySelectorAll('.product-card');
    const term = searchTerm.toLowerCase();

    productCards.forEach(card => {
        const name = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
        const category = card.querySelector('.card-text')?.textContent.toLowerCase() || '';

        if (name.includes(term) || category.includes(term)) {
            card.closest('.col-md-4, .col-lg-3').style.display = 'block';
        } else {
            card.closest('.col-md-4, .col-lg-3').style.display = 'none';
        }
    });
}

/**
 * Filter Products by Category
 */
async function filterProductsByCategory(categoryId) {
    if (!categoryId) {
        // Show all products
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.closest('.col-md-4, .col-lg-3').style.display = 'block';
        });
        return;
    }

    try {
        const response = await fetch(`/supermarket/products/search?category_id=${categoryId}`);
        if (response.ok) {
            const data = await response.json();
            SupermarketState.products = data.data || [];
            updateProductGrid();
        }
    } catch (error) {
        console.error('Error filtering products:', error);
    }
}

/**
 * Update Product Grid
 */
function updateProductGrid() {
    // The product grid is already rendered by Blade template
    // This function can be used to update it dynamically if needed
    console.log('Product grid updated with', SupermarketState.products.length, 'products');
}

/**
 * Add Product to Cart
 */
async function addToCart(productId, quantity = 1) {
    try {
        const response = await fetch('/supermarket/cart/add', {
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

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                updateCartDisplay();
                updateCartSummary();
                showNotification(`${data.product_name || 'Product'} added to cart`, 'success');
            } else {
                showNotification(data.message || 'Error adding to cart', 'error');
            }
        } else {
            throw new Error('Failed to add to cart');
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
    fetch('/supermarket/cart')
        .then(response => response.json())
        .then(data => {
            if (data.cart && data.cart.length > 0) {
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
    fetch('/supermarket/cart/summary')
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
        const response = await fetch(`/supermarket/cart/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                updateCartDisplay();
                updateCartSummary();
                showNotification('Item removed from cart', 'info');
            }
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
        const response = await fetch('/supermarket/cart/update', {
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

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                updateCartDisplay();
                updateCartSummary();
            } else {
                showNotification(data.message || 'Error updating quantity', 'error');
            }
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
        const response = await fetch('/supermarket/cart/clear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                updateCartDisplay();
                updateCartSummary();
                showNotification('Cart cleared', 'info');
            }
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
    fetch('/supermarket/cart/summary')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count > 0) {
                const total = data.total || 0;
                document.getElementById('paymentTotal').textContent = `₦${parseFloat(total).toFixed(2)}`;
                document.getElementById('paymentItems').textContent = data.count || 0;
                document.getElementById('paymentSubtotal').textContent = `₦${parseFloat(data.subtotal || 0).toFixed(2)}`;
                document.getElementById('paymentTax').textContent = `₦${parseFloat(data.tax_amount || 0).toFixed(2)}`;

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
    SupermarketState.payment.method = method;

    // Remove active class from all buttons
    document.querySelectorAll('.payment-method').forEach(btn => {
        btn.classList.remove('active');
    });

    // Add active class to selected button
    event.currentTarget.classList.add('active');

    // Show/hide cash input
    const cashInput = document.getElementById('cashInput');
    const completeBtn = document.getElementById('completePaymentBtn');

    if (method === 'cash') {
        cashInput.style.display = 'block';
        completeBtn.disabled = true;
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
 * Complete Payment
 */
async function completePayment() {
    const method = SupermarketState.payment.method;

    if (!method) {
        showNotification('Please select a payment method', 'warning');
        return;
    }

    const amountReceived = method === 'cash' ? parseFloat(document.getElementById('amountReceived').value) || 0 : 0;

    try {
        const response = await fetch('/supermarket/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_method: method,
                amount_paid: amountReceived,
                customer_id: null // Can be added from customer form
            })
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                // Clear cart
                SupermarketState.payment = { method: null, amount: 0, change: 0 };
                updateCartDisplay();
                updateCartSummary();
                updateTransactionsTable();
                updateStats();

                // Close modal
                const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                paymentModal.hide();

                // Show success message
                showNotification(`Transaction completed - Receipt: ${data.receipt_number}`, 'success');

                // Print receipt (simulation)
                setTimeout(() => {
                    if (confirm('Print receipt?')) {
                        printReceipt(data.sale);
                    }
                }, 1000);
            } else {
                showNotification(data.message || 'Error processing payment', 'error');
            }
        }
    } catch (error) {
        console.error('Error completing payment:', error);
        showNotification('Error processing payment', 'error');
    }
}

/**
 * Print Receipt
 */
function printReceipt(sale) {
    const receiptContent = `
        VIOLET MARELLA LIMITED
        Mini Supermarket
        ========================
        Receipt: ${sale.receipt_number}
        Date: ${new Date(sale.sale_date).toLocaleString()}
        Cashier: ${sale.staff?.full_name || 'Current User'}

        ITEMS:
        ${sale.sale_items?.map(item =>
            `${item.product_name} x${item.quantity} - ₦${parseFloat(item.total_price).toFixed(2)}`
        ).join('\n') || 'N/A'}

        ========================
        Subtotal: ₦${parseFloat(sale.subtotal).toFixed(2)}
        Tax: ₦${parseFloat(sale.tax_amount).toFixed(2)}
        Total: ₦${parseFloat(sale.total_amount).toFixed(2)}
        Payment: ${sale.payment_method.toUpperCase()}

        Thank you for shopping with us!
    `;

    console.log('Receipt:', receiptContent);
    showNotification('Receipt printed successfully', 'success');
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
        event.target.value = '';
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
    setTimeout(initializeSupermarket, 100);
});

// Export supermarket functions for global access
window.VioletMarellaSupermarket = {
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    processCheckout,
    completePayment,
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

        SupermarketState.currentCustomer = {
            name: customerName || 'Walk-in Customer',
            phone: customerPhone,
            isRegular: isRegular
        };

        const modal = bootstrap.Modal.getInstance(document.getElementById('newSaleModal'));
        modal.hide();

        showNotification(`Sale started for ${SupermarketState.currentCustomer.name}`, 'success');
    }
};
