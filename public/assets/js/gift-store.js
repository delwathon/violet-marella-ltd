/**
 * VIOLET MARELLA LIMITED - GIFT STORE FUNCTIONALITY
 * Gift store management JavaScript functionality
 */

// Gift Store state and data
const GiftStoreState = {
    products: [],
    categories: [],
    currentFilter: 'all',
    currentSearch: '',
    selectedProducts: [],
    pagination: {
        currentPage: 1,
        itemsPerPage: 10,
        totalItems: 0
    }
};

// Sample product data
const sampleProducts = [
    {
        id: 'VGB-001',
        name: "Valentine's Gift Box",
        description: 'Luxury gift packaging with assorted chocolates and flowers',
        category: 'seasonal',
        categoryName: 'Seasonal Items',
        price: 5500,
        stock: 15,
        minStock: 20,
        supplier: 'Gift Suppliers Ltd',
        image: 'fas fa-gift',
        color: 'text-danger',
        status: 'low_stock',
        lastUpdated: new Date('2024-01-15'),
        sold: 45
    },
    {
        id: 'BCS-002',
        name: 'Birthday Card Set',
        description: 'Assorted birthday cards with beautiful designs',
        category: 'cards',
        categoryName: 'Greeting Cards',
        price: 1200,
        stock: 45,
        minStock: 30,
        supplier: 'Card Masters',
        image: 'fas fa-birthday-cake',
        color: 'text-warning',
        status: 'in_stock',
        lastUpdated: new Date('2024-01-20'),
        sold: 128
    },
    {
        id: 'AF-003',
        name: 'Anniversary Flowers',
        description: 'Fresh flower arrangements for special occasions',
        category: 'flowers',
        categoryName: 'Flowers & Plants',
        price: 8500,
        stock: 0,
        minStock: 10,
        supplier: 'Flower Paradise',
        image: 'fas fa-heart',
        color: 'text-danger',
        status: 'out_of_stock',
        lastUpdated: new Date('2024-01-10'),
        sold: 67
    },
    {
        id: 'TBC-004',
        name: 'Teddy Bear Collection',
        description: 'Soft plush toys in various sizes and colors',
        category: 'toys',
        categoryName: 'Toys & Games',
        price: 3200,
        stock: 28,
        minStock: 15,
        supplier: 'Toy World Inc',
        image: 'fas fa-teddy-bear',
        color: 'text-info',
        status: 'in_stock',
        lastUpdated: new Date('2024-01-18'),
        sold: 89
    },
    {
        id: 'WC-005',
        name: 'Wedding Cards Premium',
        description: 'Elegant wedding invitation cards',
        category: 'cards',
        categoryName: 'Greeting Cards',
        price: 2500,
        stock: 8,
        minStock: 20,
        supplier: 'Card Masters',
        image: 'fas fa-ring',
        color: 'text-warning',
        status: 'low_stock',
        lastUpdated: new Date('2024-01-22'),
        sold: 34
    },
    {
        id: 'CC-006',
        name: 'Chocolate Collection',
        description: 'Premium assorted chocolates in gift boxes',
        category: 'seasonal',
        categoryName: 'Seasonal Items',
        price: 4200,
        stock: 35,
        minStock: 25,
        supplier: 'Sweet Treats Co',
        image: 'fas fa-cookie-bite',
        color: 'text-brown',
        status: 'in_stock',
        lastUpdated: new Date('2024-01-25'),
        sold: 78
    }
];

// Categories data
const categories = [
    { id: 'seasonal', name: 'Seasonal Items', count: 0, color: 'primary' },
    { id: 'cards', name: 'Greeting Cards', count: 0, color: 'info' },
    { id: 'flowers', name: 'Flowers & Plants', count: 0, color: 'success' },
    { id: 'toys', name: 'Toys & Games', count: 0, color: 'warning' },
    { id: 'accessories', name: 'Accessories', count: 0, color: 'secondary' }
];

/**
 * Initialize Gift Store
 */
function initializeGiftStore() {
    console.log('Initializing gift store...');
    
    // Load products and categories
    loadGiftStoreData();
    
    // Initialize search and filters
    initializeSearchAndFilters();
    
    // Bind events
    bindGiftStoreEvents();
    
    // Update display
    updateProductTable();
    updateStatsCards();
    updateCategoriesSection();
    updateLowStockAlerts();
    
    console.log('Gift store initialized successfully');
}

/**
 * Load Gift Store Data
 */
function loadGiftStoreData() {
    // In production, this would fetch from an API
    GiftStoreState.products = [...sampleProducts];
    GiftStoreState.categories = [...categories];
    
    // Update category counts
    updateCategoryCounts();
    
    console.log('Loaded', GiftStoreState.products.length, 'products');
}

/**
 * Update Category Counts
 */
function updateCategoryCounts() {
    GiftStoreState.categories.forEach(category => {
        category.count = GiftStoreState.products.filter(p => p.category === category.id).length;
    });
}

/**
 * Initialize Search and Filters
 */
function initializeSearchAndFilters() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Debounced search
        const debouncedSearch = VioletMarellaCommon.debounce((value) => {
            GiftStoreState.currentSearch = value;
            filterAndDisplayProducts();
        }, 300);
        
        searchInput.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }
}

/**
 * Bind Gift Store Events
 */
function bindGiftStoreEvents() {
    // Product form submission
    const addProductForm = document.getElementById('addProductForm');
    if (addProductForm) {
        addProductForm.addEventListener('submit', handleAddProduct);
    }
    
    // Bulk actions
    const selectAllCheckbox = document.getElementById('selectAllProducts');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', handleSelectAll);
    }
    
    // Category filters
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach(item => {
        item.addEventListener('click', handleCategoryFilter);
    });
}

/**
 * Update Product Table
 */
function updateProductTable() {
    const tableBody = document.querySelector('#inventoryTable tbody');
    if (!tableBody) return;
    
    const filteredProducts = getFilteredProducts();
    const paginatedProducts = getPaginatedProducts(filteredProducts);
    
    if (paginatedProducts.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <div>No products found</div>
                    <small class="text-muted">Try adjusting your search or filters</small>
                </td>
            </tr>
        `;
        return;
    }
    
    tableBody.innerHTML = paginatedProducts.map(product => `
        <tr data-product-id="${product.id}">
            <td>
                <div class="d-flex align-items-center">
                    <div class="form-check me-3">
                        <input class="form-check-input product-checkbox" type="checkbox" value="${product.id}">
                    </div>
                    <div class="product-image me-3">
                        <i class="${product.image} ${product.color}"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">${product.name}</div>
                        <small class="text-muted">${product.description}</small>
                    </div>
                </div>
            </td>
            <td><code>${product.id}</code></td>
            <td>${product.categoryName}</td>
            <td>
                <span class="badge ${getStockBadgeClass(product.stock, product.minStock)}">
                    ${product.stock}
                </span>
            </td>
            <td>${VioletMarellaCommon.formatCurrency(product.price)}</td>
            <td>
                <span class="badge ${getStatusBadgeClass(product.status)}">
                    ${getStatusText(product.status)}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editProduct('${product.id}')" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="restockProduct('${product.id}')" title="Restock">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="btn btn-outline-info" onclick="viewProductDetails('${product.id}')" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteProduct('${product.id}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    // Update pagination
    updatePagination(filteredProducts.length);
}

/**
 * Get Filtered Products
 */
function getFilteredProducts() {
    let filtered = [...GiftStoreState.products];
    
    // Apply search filter
    if (GiftStoreState.currentSearch) {
        const search = GiftStoreState.currentSearch.toLowerCase();
        filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(search) ||
            product.description.toLowerCase().includes(search) ||
            product.id.toLowerCase().includes(search) ||
            product.categoryName.toLowerCase().includes(search)
        );
    }
    
    // Apply category filter
    if (GiftStoreState.currentFilter !== 'all') {
        filtered = filtered.filter(product => product.category === GiftStoreState.currentFilter);
    }
    
    return filtered;
}

/**
 * Get Paginated Products
 */
function getPaginatedProducts(products) {
    const startIndex = (GiftStoreState.pagination.currentPage - 1) * GiftStoreState.pagination.itemsPerPage;
    const endIndex = startIndex + GiftStoreState.pagination.itemsPerPage;
    return products.slice(startIndex, endIndex);
}

/**
 * Get Stock Badge Class
 */
function getStockBadgeClass(stock, minStock) {
    if (stock === 0) return 'bg-danger';
    if (stock <= minStock) return 'bg-warning';
    return 'bg-success';
}

/**
 * Get Status Badge Class
 */
function getStatusBadgeClass(status) {
    const classes = {
        'in_stock': 'bg-success',
        'low_stock': 'bg-warning',
        'out_of_stock': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

/**
 * Get Status Text
 */
function getStatusText(status) {
    const texts = {
        'in_stock': 'In Stock',
        'low_stock': 'Low Stock',
        'out_of_stock': 'Out of Stock'
    };
    return texts[status] || 'Unknown';
}

/**
 * Update Stats Cards
 */
function updateStatsCards() {
    const stats = calculateStats();
    
    const statCards = [
        { selector: '.stat-card:nth-child(1) .stat-value', value: stats.totalProducts },
        { selector: '.stat-card:nth-child(2) .stat-value', value: stats.lowStockItems },
        { selector: '.stat-card:nth-child(3) .stat-value', value: stats.outOfStockItems },
        { selector: '.stat-card:nth-child(4) .stat-value', value: VioletMarellaCommon.formatCurrency(stats.monthlyRevenue) }
    ];
    
    statCards.forEach(card => {
        const element = document.querySelector(card.selector);
        if (element) {
            element.textContent = card.value;
        }
    });
}

/**
 * Calculate Statistics
 */
function calculateStats() {
    const products = GiftStoreState.products;
    
    return {
        totalProducts: products.length,
        lowStockItems: products.filter(p => p.stock > 0 && p.stock <= p.minStock).length,
        outOfStockItems: products.filter(p => p.stock === 0).length,
        monthlyRevenue: products.reduce((sum, p) => sum + (p.sold * p.price), 0)
    };
}

/**
 * Update Categories Section
 */
function updateCategoriesSection() {
    const categoriesContainer = document.querySelector('.category-item').parentNode;
    if (!categoriesContainer) return;
    
    categoriesContainer.innerHTML = GiftStoreState.categories.map(category => `
        <div class="category-item" data-category="${category.id}">
            <div class="d-flex justify-content-between align-items-center">
                <span>${category.name}</span>
                <span class="badge bg-${category.color}">${category.count}</span>
            </div>
        </div>
    `).join('');
}

/**
 * Update Low Stock Alerts
 */
function updateLowStockAlerts() {
    const alertsContainer = document.querySelector('.alert-item').parentNode;
    if (!alertsContainer) return;
    
    const lowStockProducts = GiftStoreState.products.filter(p => 
        p.stock <= p.minStock && p.stock > 0
    );
    
    const outOfStockProducts = GiftStoreState.products.filter(p => p.stock === 0);
    
    const alerts = [
        ...lowStockProducts.map(p => ({
            icon: 'fas fa-exclamation-triangle text-warning',
            title: p.name,
            message: `Only ${p.stock} items left`,
            type: 'warning'
        })),
        ...outOfStockProducts.map(p => ({
            icon: 'fas fa-times-circle text-danger',
            title: p.name,
            message: 'Out of stock',
            type: 'danger'
        }))
    ];
    
    if (alerts.length === 0) {
        alertsContainer.innerHTML = `
            <div class="text-center py-3">
                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                <div>All products are well stocked!</div>
            </div>
        `;
        return;
    }
    
    alertsContainer.innerHTML = alerts.slice(0, 5).map(alert => `
        <div class="alert-item">
            <div class="d-flex align-items-center">
                <i class="${alert.icon} me-2"></i>
                <div class="flex-grow-1">
                    <div class="fw-semibold">${alert.title}</div>
                    <small class="text-muted">${alert.message}</small>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Filter and Display Products
 */
function filterAndDisplayProducts() {
    GiftStoreState.pagination.currentPage = 1; // Reset to first page
    updateProductTable();
}

/**
 * Update Pagination
 */
function updatePagination(totalItems) {
    GiftStoreState.pagination.totalItems = totalItems;
    const totalPages = Math.ceil(totalItems / GiftStoreState.pagination.itemsPerPage);
    
    const paginationContainer = document.querySelector('.pagination');
    if (!paginationContainer) return;
    
    let paginationHTML = '';
    
    // Previous button
    paginationHTML += `
        <li class="page-item ${GiftStoreState.pagination.currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${GiftStoreState.pagination.currentPage - 1})">Previous</a>
        </li>
    `;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === GiftStoreState.pagination.currentPage) {
            paginationHTML += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
        }
    }
    
    // Next button
    paginationHTML += `
        <li class="page-item ${GiftStoreState.pagination.currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${GiftStoreState.pagination.currentPage + 1})">Next</a>
        </li>
    `;
    
    paginationContainer.innerHTML = paginationHTML;
}

/**
 * Change Page
 */
function changePage(page) {
    const totalPages = Math.ceil(GiftStoreState.pagination.totalItems / GiftStoreState.pagination.itemsPerPage);
    
    if (page < 1 || page > totalPages) return;
    
    GiftStoreState.pagination.currentPage = page;
    updateProductTable();
}

/**
 * Handle Category Filter
 */
function handleCategoryFilter(event) {
    const category = event.currentTarget.getAttribute('data-category');
    GiftStoreState.currentFilter = category || 'all';
    filterAndDisplayProducts();
    
    // Update active state
    document.querySelectorAll('.category-item').forEach(item => {
        item.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
}

/**
 * Handle Add Product
 */
function handleAddProduct(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const product = {
        id: generateProductId(),
        name: formData.get('name'),
        description: formData.get('description'),
        category: formData.get('category'),
        categoryName: getCategoryName(formData.get('category')),
        price: parseInt(formData.get('price')),
        stock: parseInt(formData.get('stock')),
        minStock: parseInt(formData.get('minStock')),
        supplier: 'Default Supplier',
        image: 'fas fa-box',
        color: 'text-primary',
        status: 'in_stock',
        lastUpdated: new Date(),
        sold: 0
    };
    
    // Add to products array
    GiftStoreState.products.unshift(product);
    
    // Update displays
    updateCategoryCounts();
    updateProductTable();
    updateStatsCards();
    updateCategoriesSection();
    updateLowStockAlerts();
    
    // Close modal and reset form
    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
    modal.hide();
    event.target.reset();
    
    VioletMarellaCommon.showNotification('Product added successfully!', 'success');
}

/**
 * Generate Product ID
 */
function generateProductId() {
    const prefix = 'PRD';
    const number = String(GiftStoreState.products.length + 1).padStart(3, '0');
    return `${prefix}-${number}`;
}

/**
 * Get Category Name
 */
function getCategoryName(categoryId) {
    const category = GiftStoreState.categories.find(c => c.id === categoryId);
    return category ? category.name : 'Unknown';
}

/**
 * Edit Product
 */
function editProduct(productId) {
    const product = GiftStoreState.products.find(p => p.id === productId);
    if (!product) return;
    
    VioletMarellaCommon.showNotification(`Edit functionality for ${product.name} would be implemented here.`, 'info');
}

/**
 * Restock Product
 */
function restockProduct(productId) {
    const product = GiftStoreState.products.find(p => p.id === productId);
    if (!product) return;
    
    const quantity = prompt(`Enter restock quantity for ${product.name}:`, '10');
    if (quantity && !isNaN(quantity)) {
        product.stock += parseInt(quantity);
        product.status = product.stock > product.minStock ? 'in_stock' : 'low_stock';
        product.lastUpdated = new Date();
        
        updateProductTable();
        updateStatsCards();
        updateLowStockAlerts();
        
        VioletMarellaCommon.showNotification(`${product.name} restocked with ${quantity} units`, 'success');
    }
}

/**
 * View Product Details
 */
function viewProductDetails(productId) {
    const product = GiftStoreState.products.find(p => p.id === productId);
    if (!product) return;
    
    const details = `
        Product: ${product.name}
        SKU: ${product.id}
        Category: ${product.categoryName}
        Price: ${VioletMarellaCommon.formatCurrency(product.price)}
        Stock: ${product.stock}
        Min Stock: ${product.minStock}
        Status: ${getStatusText(product.status)}
        Last Updated: ${VioletMarellaCommon.formatDate(product.lastUpdated, 'datetime')}
        Total Sold: ${product.sold}
    `;
    
    alert(details); // In production, use a proper modal
}

/**
 * Delete Product
 */
function deleteProduct(productId) {
    const product = GiftStoreState.products.find(p => p.id === productId);
    if (!product) return;
    
    if (confirm(`Are you sure you want to delete ${product.name}?`)) {
        GiftStoreState.products = GiftStoreState.products.filter(p => p.id !== productId);
        
        updateCategoryCounts();
        updateProductTable();
        updateStatsCards();
        updateCategoriesSection();
        updateLowStockAlerts();
        
        VioletMarellaCommon.showNotification(`${product.name} deleted successfully`, 'success');
    }
}

/**
 * Export Inventory
 */
function exportInventory() {
    const data = GiftStoreState.products.map(product => ({
        SKU: product.id,
        Name: product.name,
        Category: product.categoryName,
        Price: product.price,
        Stock: product.stock,
        'Min Stock': product.minStock,
        Status: getStatusText(product.status),
        'Total Sold': product.sold,
        'Last Updated': VioletMarellaCommon.formatDate(product.lastUpdated, 'short')
    }));
    
    const filename = `gift-store-inventory-${new Date().toISOString().split('T')[0]}.csv`;
    VioletMarellaCommon.exportToCSV(data, filename);
    VioletMarellaCommon.showNotification('Inventory exported successfully', 'success');
}

/**
 * Bulk Restock
 */
function bulkRestock() {
    VioletMarellaCommon.showNotification('Bulk restock functionality would be implemented here.', 'info');
}

/**
 * Generate Report
 */
function generateReport() {
    VioletMarellaCommon.showNotification('Generating inventory report...', 'info');
    setTimeout(() => {
        window.location.href = 'reports.html?type=inventory';
    }, 1000);
}

/**
 * Save Product (for modal form)
 */
function saveProduct() {
    const form = document.getElementById('addProductForm');
    if (form && VioletMarellaCommon.validateForm(form)) {
        handleAddProduct({ preventDefault: () => {}, target: form });
    }
}

/**
 * Handle Select All
 */
function handleSelectAll(event) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = event.target.checked;
    });
    updateSelectedProducts();
}

/**
 * Update Selected Products
 */
function updateSelectedProducts() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    GiftStoreState.selectedProducts = Array.from(checkboxes).map(cb => cb.value);
    
    // Update bulk action buttons
    const bulkActions = document.querySelector('.bulk-actions');
    if (bulkActions) {
        bulkActions.style.display = GiftStoreState.selectedProducts.length > 0 ? 'block' : 'none';
    }
}

/**
 * Initialize on DOM Content Loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure common.js has loaded
    setTimeout(initializeGiftStore, 100);
});

// Export gift store functions for global access
window.VioletMarellaGiftStore = {
    editProduct,
    restockProduct,
    viewProductDetails,
    deleteProduct,
    exportInventory,
    bulkRestock,
    generateReport,
    saveProduct,
    changePage
};