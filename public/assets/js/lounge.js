/**
 * VIOLET MARELLA LIMITED - LOUNGE POS FUNCTIONALITY
 * Point of Sale system with inventory management
 */

// Lounge state and data
const SupermarketState = {
    products: [],
    cart: [],
    currentCustomer: null,
    currentSale: null,
    transactions: [],
    categories: ['groceries', 'beverages', 'snacks', 'household', 'personal-care'],
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

// Sample product data
const sampleProducts = [
    {
        id: 'GRO-001',
        name: 'Rice (50kg)',
        category: 'groceries',
        price: 35000,
        stock: 45,
        barcode: '1234567890123',
        image: 'fas fa-seedling',
        description: 'Premium quality rice'
    },
    {
        id: 'BEV-001',
        name: 'Coca Cola (35cl)',
        category: 'beverages',
        price: 200,
        stock: 120,
        barcode: '2345678901234',
        image: 'fas fa-bottle-water',
        description: 'Refreshing soft drink'
    },
    {
        id: 'SNK-001',
        name: 'Pringles Original',
        category: 'snacks',
        price: 1500,
        stock: 30,
        barcode: '3456789012345',
        image: 'fas fa-cookie',
        description: 'Crispy potato chips'
    },
    {
        id: 'HSH-001',
        name: 'Detergent (1kg)',
        category: 'household',
        price: 2500,
        stock: 25,
        barcode: '4567890123456',
        image: 'fas fa-spray-can',
        description: 'Washing powder'
    },
    {
        id: 'PER-001',
        name: 'Toothpaste',
        category: 'personal-care',
        price: 800,
        stock: 60,
        barcode: '5678901234567',
        image: 'fas fa-tooth',
        description: 'Fluoride toothpaste'
    }
];

// Sample transactions
const sampleTransactions = [
    {
        id: 'TXN-001',
        receiptNumber: 'RCP-001',
        timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000),
        items: 3,
        amount: 5200,
        paymentMethod: 'cash',
        cashier: 'John Doe'
    },
    {
        id: 'TXN-002',
        receiptNumber: 'RCP-002',
        timestamp: new Date(Date.now() - 4 * 60 * 60 * 1000),
        items: 2,
        amount: 3000,
        paymentMethod: 'card',
        cashier: 'Jane Smith'
    }
];

/**
 * Initialize Lounge POS
 */
function initializeSupermarket() {
    console.log('Initializing lounge POS...');
    
    // Load data
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
    
    console.log('Lounge POS initialized successfully');
}

/**
 * Load Lounge Data
 */
function loadSupermarketData() {
    SupermarketState.products = [...sampleProducts];
    SupermarketState.transactions = [...sampleTransactions];
    SupermarketState.pagination.totalItems = SupermarketState.products.length;
    
    console.log('Loaded', SupermarketState.products.length, 'products');
}

/**
 * Initialize Product Search
 */
function initializeProductSearch() {
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (searchInput) {
        const debouncedSearch = VioletMarellaCommon.debounce((value) => {
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
function bindSupermarketEvents() {
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
 * Update Product Grid
 */
function updateProductGrid() {
    const productGrid = document.getElementById('productGrid');
    if (!productGrid) return;
    
    const filteredProducts = getFilteredProducts();
    const paginatedProducts = getPaginatedProducts(filteredProducts);
    
    if (paginatedProducts.length === 0) {
        productGrid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                <h5>No products found</h5>
                <p class="text-muted">Try adjusting your search or filters</p>
            </div>
        `;
        return;
    }
    
    productGrid.innerHTML = paginatedProducts.map(product => `
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="product-card" data-product-id="${product.id}">
                <div class="product-image">
                    <i class="${product.image} fa-2x text-primary"></i>
                </div>
                <div class="product-info">
                    <h6 class="product-name">${product.name}</h6>
                    <p class="product-price">${VioletMarellaCommon.formatCurrency(product.price)}</p>
                    <small class="text-muted">Stock: ${product.stock}</small>
                </div>
                <div class="product-actions">
                    <button class="btn btn-primary btn-sm w-100" onclick="addToCart('${product.id}')" ${product.stock === 0 ? 'disabled' : ''}>
                        <i class="fas fa-plus me-1"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    updatePagination(filteredProducts.length);
}

/**
 * Get Filtered Products
 */
function getFilteredProducts() {
    let filtered = [...SupermarketState.products];
    
    // Apply search filter
    const searchTerm = document.getElementById('productSearch')?.value.toLowerCase();
    if (searchTerm) {
        filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(searchTerm) ||
            product.barcode.includes(searchTerm) ||
            product.description.toLowerCase().includes(searchTerm)
        );
    }
    
    // Apply category filter
    const categoryFilter = document.getElementById('categoryFilter')?.value;
    if (categoryFilter) {
        filtered = filtered.filter(product => product.category === categoryFilter);
    }
    
    return filtered;
}

/**
 * Get Paginated Products
 */
function getPaginatedProducts(products) {
    const startIndex = (SupermarketState.pagination.currentPage - 1) * SupermarketState.pagination.itemsPerPage;
    const endIndex = startIndex + SupermarketState.pagination.itemsPerPage;
    return products.slice(startIndex, endIndex);
}

/**
 * Update Pagination
 */
function updatePagination(totalItems) {
    SupermarketState.pagination.totalItems = totalItems;
    const totalPages = Math.ceil(totalItems / SupermarketState.pagination.itemsPerPage);
    
    const paginationContainer = document.getElementById('productPagination');
    if (!paginationContainer) return;
    
    let paginationHTML = '';
    
    // Previous button
    paginationHTML += `
        <li class="page-item ${SupermarketState.pagination.currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${SupermarketState.pagination.currentPage - 1})">Previous</a>
        </li>
    `;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === SupermarketState.pagination.currentPage) {
            paginationHTML += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
        }
    }
    
    // Next button
    paginationHTML += `
        <li class="page-item ${SupermarketState.pagination.currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${SupermarketState.pagination.currentPage + 1})">Next</a>
        </li>
    `;
    
    paginationContainer.innerHTML = paginationHTML;
}

/**
 * Change Page
 */
function changePage(page) {
    const totalPages = Math.ceil(SupermarketState.pagination.totalItems / SupermarketState.pagination.itemsPerPage);
    
    if (page < 1 || page > totalPages) return;
    
    SupermarketState.pagination.currentPage = page;
    updateProductGrid();
}

/**
 * Search Products
 */
function searchProducts(searchTerm) {
    SupermarketState.pagination.currentPage = 1;
    updateProductGrid();
}

/**
 * Filter Products by Category
 */
function filterProductsByCategory(category) {
    SupermarketState.pagination.currentPage = 1;
    updateProductGrid();
}

/**
 * Add Product to Cart
 */
function addToCart(productId) {
    const product = SupermarketState.products.find(p => p.id === productId);
    if (!product || product.stock === 0) return;
    
    const existingItem = SupermarketState.cart.find(item => item.productId === productId);
    
    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
            existingItem.total = existingItem.quantity * existingItem.price;
        } else {
            VioletMarellaCommon.showNotification('Insufficient stock', 'warning');
            return;
        }
    } else {
        SupermarketState.cart.push({
            productId: productId,
            name: product.name,
            price: product.price,
            quantity: 1,
            total: product.price
        });
    }
    
    updateCartDisplay();
    updateCartSummary();
    VioletMarellaCommon.showNotification(`${product.name} added to cart`, 'success');
}

/**
 * Update Cart Display
 */
function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    if (!cartItems) return;
    
    if (SupermarketState.cart.length === 0) {
        cartItems.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">Cart is empty</p>
                <small>Start scanning or selecting products</small>
            </div>
        `;
        return;
    }
    
    cartItems.innerHTML = SupermarketState.cart.map(item => `
        <div class="cart-item" data-product-id="${item.productId}">
            <div class="d-flex justify-content-between align-items-center">
                <div class="item-info">
                    <h6 class="mb-1">${item.name}</h6>
                    <small class="text-muted">${VioletMarellaCommon.formatCurrency(item.price)} each</small>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart('${item.productId}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="quantity-controls">
                    <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('${item.productId}', -1)">-</button>
                    <span class="quantity mx-2">${item.quantity}</span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('${item.productId}', 1)">+</button>
                </div>
                <strong>${VioletMarellaCommon.formatCurrency(item.total)}</strong>
            </div>
        </div>
    `).join('');
}

/**
 * Update Cart Summary
 */
function updateCartSummary() {
    const subtotal = SupermarketState.cart.reduce((sum, item) => sum + item.total, 0);
    const tax = subtotal * 0.075; // 7.5% VAT
    const total = subtotal + tax;
    
    document.getElementById('cartSubtotal').textContent = VioletMarellaCommon.formatCurrency(subtotal);
    document.getElementById('cartTax').textContent = VioletMarellaCommon.formatCurrency(tax);
    document.getElementById('cartTotal').textContent = VioletMarellaCommon.formatCurrency(total);
    
    // Enable/disable checkout button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.disabled = SupermarketState.cart.length === 0;
    }
}

/**
 * Remove Item from Cart
 */
function removeFromCart(productId) {
    SupermarketState.cart = SupermarketState.cart.filter(item => item.productId !== productId);
    updateCartDisplay();
    updateCartSummary();
}

/**
 * Update Item Quantity
 */
function updateQuantity(productId, change) {
    const item = SupermarketState.cart.find(item => item.productId === productId);
    const product = SupermarketState.products.find(p => p.id === productId);
    
    if (!item || !product) return;
    
    const newQuantity = item.quantity + change;
    
    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    if (newQuantity > product.stock) {
        VioletMarellaCommon.showNotification('Insufficient stock', 'warning');
        return;
    }
    
    item.quantity = newQuantity;
    item.total = item.quantity * item.price;
    
    updateCartDisplay();
    updateCartSummary();
}

/**
 * Clear Cart
 */
function clearCart() {
    if (SupermarketState.cart.length === 0) return;
    
    if (confirm('Are you sure you want to clear the cart?')) {
        SupermarketState.cart = [];
        updateCartDisplay();
        updateCartSummary();
        VioletMarellaCommon.showNotification('Cart cleared', 'info');
    }
}

/**
 * Apply Discount
 */
function applyDiscount() {
    const discountPercent = prompt('Enter discount percentage (0-100):');
    
    if (discountPercent === null) return;
    
    const discount = parseFloat(discountPercent);
    if (isNaN(discount) || discount < 0 || discount > 100) {
        VioletMarellaCommon.showNotification('Invalid discount percentage', 'error');
        return;
    }
    
    const subtotal = SupermarketState.cart.reduce((sum, item) => sum + item.total, 0);
    const discountAmount = subtotal * (discount / 100);
    
    document.getElementById('cartDiscount').textContent = `-${VioletMarellaCommon.formatCurrency(discountAmount)}`;
    document.getElementById('discountRow').style.display = 'flex';
    
    // Recalculate total
    const tax = subtotal * 0.075;
    const total = subtotal + tax - discountAmount;
    document.getElementById('cartTotal').textContent = VioletMarellaCommon.formatCurrency(total);
    
    VioletMarellaCommon.showNotification(`${discount}% discount applied`, 'success');
}

/**
 * Process Checkout
 */
function processCheckout() {
    if (SupermarketState.cart.length === 0) return;
    
    const total = calculateTotal();
    document.getElementById('paymentTotal').textContent = VioletMarellaCommon.formatCurrency(total);
    
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    paymentModal.show();
}

/**
 * Calculate Total
 */
function calculateTotal() {
    const subtotal = SupermarketState.cart.reduce((sum, item) => sum + item.total, 0);
    const tax = subtotal * 0.075;
    const discountElement = document.getElementById('cartDiscount');
    const discount = discountElement ? parseFloat(discountElement.textContent.replace(/[₦,]/g, '')) || 0 : 0;
    
    return subtotal + tax - discount;
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
    const total = calculateTotal();
    const change = amountReceived - total;
    
    document.getElementById('changeAmount').textContent = VioletMarellaCommon.formatCurrency(Math.max(0, change));
    
    const completeBtn = document.getElementById('completePaymentBtn');
    completeBtn.disabled = amountReceived < total;
}

/**
 * Complete Payment
 */
function completePayment() {
    const total = calculateTotal();
    const method = SupermarketState.payment.method;
    
    if (!method) {
        VioletMarellaCommon.showNotification('Please select a payment method', 'warning');
        return;
    }
    
    // Create transaction
    const transaction = {
        id: `TXN-${Date.now()}`,
        receiptNumber: generateReceiptNumber(),
        timestamp: new Date(),
        items: SupermarketState.cart.length,
        amount: total,
        paymentMethod: method,
        cashier: 'Current User', // In real app, get from current user
        products: [...SupermarketState.cart]
    };
    
    // Add to transactions
    SupermarketState.transactions.unshift(transaction);
    
    // Update product stock
    SupermarketState.cart.forEach(item => {
        const product = SupermarketState.products.find(p => p.id === item.productId);
        if (product) {
            product.stock -= item.quantity;
        }
    });
    
    // Clear cart
    SupermarketState.cart = [];
    SupermarketState.payment = { method: null, amount: 0, change: 0 };
    
    // Update displays
    updateCartDisplay();
    updateCartSummary();
    updateProductGrid();
    updateTransactionsTable();
    updateStats();
    
    // Close modal
    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
    paymentModal.hide();
    
    // Show success message
    VioletMarellaCommon.showNotification(`Transaction completed - Receipt: ${transaction.receiptNumber}`, 'success');
    
    // Reset discount
    document.getElementById('discountRow').style.display = 'none';
    
    // Print receipt (simulation)
    setTimeout(() => {
        if (confirm('Print receipt?')) {
            printReceipt(transaction);
        }
    }, 1000);
}

/**
 * Generate Receipt Number
 */
function generateReceiptNumber() {
    const date = new Date();
    const dateStr = date.toISOString().slice(0, 10).replace(/-/g, '');
    const timeStr = date.getTime().toString().slice(-4);
    return `RCP-${dateStr}-${timeStr}`;
}

/**
 * Print Receipt
 */
function printReceipt(transaction) {
    const receiptContent = `
        VIOLET MARELLA LIMITED
        Mini Lounge
        ========================
        Receipt: ${transaction.receiptNumber}
        Date: ${VioletMarellaCommon.formatDate(transaction.timestamp, 'datetime')}
        Cashier: ${transaction.cashier}
        
        ITEMS:
        ${transaction.products.map(item => 
            `${item.name} x${item.quantity} - ${VioletMarellaCommon.formatCurrency(item.total)}`
        ).join('\n')}
        
        ========================
        Total: ${VioletMarellaCommon.formatCurrency(transaction.amount)}
        Payment: ${transaction.paymentMethod.toUpperCase()}
        
        Thank you for shopping with us!
    `;
    
    console.log('Receipt:', receiptContent);
    VioletMarellaCommon.showNotification('Receipt printed successfully', 'success');
}

/**
 * Update Transactions Table
 */
function updateTransactionsTable() {
    const transactionsTable = document.getElementById('transactionsTable');
    if (!transactionsTable) return;
    
    const recentTransactions = SupermarketState.transactions.slice(0, 10);
    
    transactionsTable.innerHTML = recentTransactions.map(transaction => `
        <tr>
            <td><strong>${transaction.receiptNumber}</strong></td>
            <td>${VioletMarellaCommon.formatDate(transaction.timestamp, 'time')}</td>
            <td>${transaction.items}</td>
            <td>${VioletMarellaCommon.formatCurrency(transaction.amount)}</td>
            <td>
                <span class="badge bg-${getPaymentMethodColor(transaction.paymentMethod)}">
                    ${transaction.paymentMethod.toUpperCase()}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="viewTransaction('${transaction.id}')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="reprintReceipt('${transaction.id}')">
                    <i class="fas fa-print"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

/**
 * Get Payment Method Color
 */
function getPaymentMethodColor(method) {
    const colors = {
        'cash': 'success',
        'card': 'primary',
        'transfer': 'info',
        'split': 'warning'
    };
    return colors[method] || 'secondary';
}

/**
 * Update Statistics
 */
function updateStats() {
    const today = new Date().toDateString();
    const todayTransactions = SupermarketState.transactions.filter(t => 
        t.timestamp.toDateString() === today
    );
    
    const todaySales = todayTransactions.reduce((sum, t) => sum + t.amount, 0);
    const totalCustomers = todayTransactions.length;
    const totalProducts = SupermarketState.products.reduce((sum, p) => sum + p.stock, 0);
    
    // Update stat cards
    const statCards = [
        { selector: '.stat-card:nth-child(1) .stat-value', value: VioletMarellaCommon.formatCurrency(todaySales).replace('₦', '₦') },
        { selector: '.stat-card:nth-child(2) .stat-value', value: todayTransactions.length },
        { selector: '.stat-card:nth-child(3) .stat-value', value: totalProducts.toLocaleString() },
        { selector: '.stat-card:nth-child(4) .stat-value', value: totalCustomers }
    ];
    
    statCards.forEach(card => {
        const element = document.querySelector(card.selector);
        if (element) {
            element.textContent = card.value;
        }
    });
}

/**
 * Handle Barcode Input
 */
function handleBarcodeInput(event) {
    // Simulate barcode scanner input (when Enter is pressed in search field)
    if (event.key === 'Enter' && event.target.id === 'productSearch') {
        const barcode = event.target.value;
        const product = SupermarketState.products.find(p => p.barcode === barcode);
        
        if (product) {
            addToCart(product.id);
            event.target.value = '';
        } else {
            VioletMarellaCommon.showNotification('Product not found', 'warning');
        }
    }
}

/**
 * Scan Barcode
 */
function scanBarcode() {
    VioletMarellaCommon.showNotification('Barcode scanner activated. Enter barcode in search field.', 'info');
    document.getElementById('productSearch').focus();
}

/**
 * Open Cash Drawer
 */
function openCashDrawer() {
    VioletMarellaCommon.showNotification('Cash drawer opened', 'info');
    console.log('Cash drawer opened');
}

/**
 * Start Sale
 */
function startSale() {
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
    
    VioletMarellaCommon.showNotification(`Sale started for ${SupermarketState.currentCustomer.name}`, 'success');
}

/**
 * View Transaction Details
 */
function viewTransaction(transactionId) {
    const transaction = SupermarketState.transactions.find(t => t.id === transactionId);
    if (!transaction) return;
    
    const details = `
        Receipt: ${transaction.receiptNumber}
        Date: ${VioletMarellaCommon.formatDate(transaction.timestamp, 'datetime')}
        Items: ${transaction.items}
        Amount: ${VioletMarellaCommon.formatCurrency(transaction.amount)}
        Payment: ${transaction.paymentMethod.toUpperCase()}
        Cashier: ${transaction.cashier}
        
        Products:
        ${transaction.products?.map(p => `${p.name} x${p.quantity}`).join('\n') || 'N/A'}
    `;
    
    alert(details); // In production, use a proper modal
}

/**
 * Reprint Receipt
 */
function reprintReceipt(transactionId) {
    const transaction = SupermarketState.transactions.find(t => t.id === transactionId);
    if (transaction) {
        printReceipt(transaction);
    }
}

/**
 * View All Transactions
 */
function viewAllTransactions() {
    window.location.href = 'reports.html?type=transactions';
}

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeSupermarket, 100);
});

// Export lounge functions for global access
window.VioletMarellaSupermarket = {
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    applyDiscount,
    processCheckout,
    completePayment,
    startSale,
    scanBarcode,
    openCashDrawer,
    viewTransaction,
    reprintReceipt,
    viewAllTransactions,
    changePage
};