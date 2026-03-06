<!-- Sidebar Navigation -->
<aside id="sidebar" class="sidebar d-flex flex-column bg-dark text-white vh-100 position-fixed start-0 top-0 overflow-auto" style="width: 300px; z-index: 1000;">
    @php
        $companyName = $companyProfile['name'] ?? 'Violet Marella Ltd';
        $directory = collect($businessDirectory ?? []);
        $loungeName = optional($directory->get('lounge'))->name ?? 'Lounge';
        $giftStoreName = optional($directory->get('gift_store'))->name ?? 'Anire Craft Store';
        $photoStudioName = optional($directory->get('photo_studio'))->name ?? 'Photo Studio';
        $propRentalName = optional($directory->get('prop_rental'))->name ?? 'Prop Rental';
        $canDashboard = $user->hasPermission('dashboard.view');
        $canLounge = $user->hasBusinessAccess('lounge') && $user->hasPermission('lounge.access');
        $canGiftStore = $user->hasBusinessAccess('gift_store') && $user->hasPermission('gift_store.access');
        $canPhotoStudio = $user->hasBusinessAccess('photo_studio') && $user->hasPermission('photo_studio.access');
        $canPropRental = $user->hasBusinessAccess('prop_rental') && $user->hasPermission('prop_rental.access');

        $canReports = $user->hasPermission('reports.view');
        $canUsersManage = $user->hasPermission('users.manage');
        $canRolesManage = $user->hasPermission('roles.manage');
        $canDepartmentsManage = $user->hasPermission('departments.manage');
        $canSecurityManage = $user->hasPermission('security.manage');
        $canSystemUpdate = $user->hasPermission('system.update');

        $dashboardLabel = ($user->isSuperAdmin() || $user->role === 'admin')
            ? 'All Business Overview'
            : ($user->role === 'manager' ? 'Managed Businesses' : 'My Dashboard');

        $canSystemManagement = $canReports || $canUsersManage || $canRolesManage || $canDepartmentsManage || $canSecurityManage || $canSystemUpdate;
        $canUserManagementMenu = $canUsersManage || $canRolesManage || $canDepartmentsManage || $canSecurityManage;
    @endphp

    <!-- Sidebar Header -->
    <div class="p-4 border-bottom border-secondary border-opacity-25">
        <h3 class="h5 mb-0 fw-semibold">{{ $companyName }}</h3>
    </div>
    
    <!-- Sidebar Navigation -->
    <nav class="flex-grow-1 py-3">
        <ul class="nav flex-column">
            
            @if($canDashboard)
                <!-- Overall Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : '' }}">
                        <i class="fas fa-home me-3" style="width: 20px;"></i>
                        <span>{{ $dashboardLabel }}</span>
                    </a>
                </li>
            @endif
            
            @if($canLounge)
                <!-- Lounge Section Header -->
                <li class="nav-item mt-3">
                    <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                        <i class="fas fa-store me-2 small"></i>
                        <span>{{ $loungeName }}</span>
                    </div>
                </li>
            
            <!-- Point of Sale -->
            <li class="nav-item">
                <a href="{{ route('lounge.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('lounge.index') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-cash-register me-3" style="width: 20px;"></i>
                    <span>Point of Sale</span>
                </a>
            </li>
            
            <!-- Product Management -->
            <li class="nav-item">
                <a href="#productsMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('lounge.products.*') || request()->routeIs('lounge.categories.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('lounge.products.*') || request()->routeIs('lounge.categories.*') ? 'true' : 'false' }}">
                    <i class="fas fa-boxes me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Products</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lounge.products.*') || request()->routeIs('lounge.categories.*') ? 'show' : '' }}" id="productsMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('lounge.products.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('lounge.products.index') ? 'text-white' : '' }}">
                                <i class="fas fa-list me-2"></i> All Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('lounge.products.create') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('lounge.products.create') ? 'text-white' : '' }}">
                                <i class="fas fa-plus me-2"></i> Add Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('lounge.categories.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('lounge.categories.*') ? 'text-white' : '' }}">
                                <i class="fas fa-tags me-2"></i> Categories
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Customer Management -->
            <li class="nav-item">
                <a href="#customersMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('lounge.customers.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('lounge.customers.*') ? 'true' : 'false' }}">
                    <i class="fas fa-users me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Customers</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lounge.customers.*') ? 'show' : '' }}" id="customersMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('lounge.customers.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('lounge.customers.index') ? 'text-white' : '' }}">
                                <i class="fas fa-list me-2"></i> All Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('lounge.customers.create') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('lounge.customers.create') ? 'text-white' : '' }}">
                                <i class="fas fa-user-plus me-2"></i> Add Customer
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Sales & Transactions -->
            <li class="nav-item">
                <a href="#salesMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('lounge.sales.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('lounge.sales.*') ? 'true' : 'false' }}">
                    <i class="fas fa-receipt me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Sales</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lounge.sales.*') ? 'show' : '' }}" id="salesMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('lounge.sales.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small">
                                <i class="fas fa-list me-2"></i> All Sales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('lounge.sales.today') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small">
                                <i class="fas fa-calendar-day me-2"></i> Today's Sales
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Inventory -->
             <li class="nav-item">
                <a href="#inventoryMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('lounge.inventory.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('lounge.inventory.*') ? 'true' : 'false' }}">
                    <i class="fas fa-boxes me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Inventory</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lounge.inventory.*') ? 'show' : '' }}" id="inventoryMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('lounge.inventory.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('lounge.inventory.index') ? 'text-white' : '' }}">
                                <i class="fas fa-warehouse me-3" style="width: 20px;"></i>
                                <span>Overview</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('lounge.inventory.low-stock') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('lounge.inventory.low-stock') ? 'text-white' : '' }}">
                                <i class="fas fa-exclamation-triangle me-2"></i> Low Stock
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            
            @if($canGiftStore)
                <!-- Anire Craft Store Section Header -->
                <li class="nav-item mt-4">
                    <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                        <i class="fas fa-gift me-2 small"></i>
                        <span>{{ $giftStoreName }}</span>
                    </div>
                </li>

            <!-- Point of Sale -->
            <li class="nav-item">
                <a href="{{ route('anire-craft-store.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('anire-craft-store.index') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-cash-register me-3" style="width: 20px;"></i>
                    <span>Point of Sale</span>
                </a>
            </li>

            <!-- Product Management -->
            <li class="nav-item">
                <a href="#storeProductsMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('anire-craft-store.products.*') || request()->routeIs('anire-craft-store.categories.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('anire-craft-store.products.*') || request()->routeIs('anire-craft-store.categories.*') ? 'true' : 'false' }}">
                    <i class="fas fa-boxes me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Products</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('anire-craft-store.products.*') || request()->routeIs('anire-craft-store.categories.*') ? 'show' : '' }}" id="storeProductsMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.products.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.products.index') ? 'text-white' : '' }}">
                                <i class="fas fa-list me-2"></i> All Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.products.create') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.products.create') ? 'text-white' : '' }}">
                                <i class="fas fa-plus me-2"></i> Add Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.categories.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.categories.*') ? 'text-white' : '' }}">
                                <i class="fas fa-tags me-2"></i> Categories
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Customer Management -->
            <li class="nav-item">
                <a href="#storeCustomersMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('anire-craft-store.customers.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('anire-craft-store.customers.*') ? 'true' : 'false' }}">
                    <i class="fas fa-users me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Customers</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('anire-craft-store.customers.*') ? 'show' : '' }}" id="storeCustomersMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.customers.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.customers.index') ? 'text-white' : '' }}">
                                <i class="fas fa-list me-2"></i> All Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.customers.create') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.customers.create') ? 'text-white' : '' }}">
                                <i class="fas fa-user-plus me-2"></i> Add Customer
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Sales Management -->
            <li class="nav-item">
                <a href="#storeSalesMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('anire-craft-store.sales.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('anire-craft-store.sales.*') ? 'true' : 'false' }}">
                    <i class="fas fa-shopping-cart me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Sales</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('anire-craft-store.sales.*') ? 'show' : '' }}" id="storeSalesMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.sales.today') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.sales.today') ? 'text-white' : '' }}">
                                <i class="fas fa-calendar-day me-2"></i> Today's Sales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.sales.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.sales.index') ? 'text-white' : '' }}">
                                <i class="fas fa-history me-2"></i> Sales History
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Inventory Management -->
            <li class="nav-item">
                <a href="#storeInventoryMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('anire-craft-store.inventory.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('anire-craft-store.inventory.*') ? 'true' : 'false' }}">
                    <i class="fas fa-boxes me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Inventory</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('anire-craft-store.inventory.*') ? 'show' : '' }}" id="storeInventoryMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.inventory.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.inventory.index') ? 'text-white' : '' }}">
                                <i class="fas fa-warehouse me-2"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('anire-craft-store.inventory.low-stock') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('anire-craft-store.inventory.low-stock') ? 'text-white' : '' }}">
                                <i class="fas fa-exclamation-triangle me-2"></i> Low Stock
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Dashboard -->
            <!-- <li class="nav-item">
                <a href="{{ route('anire-craft-store.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('anire-craft-store.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-gift me-3" style="width: 20px;"></i>
                    <span>Dashboard</span>
                </a>
            </li> -->



            @endif

            @if($canPhotoStudio)
                <!-- Photo Studio Section Header -->
                <li class="nav-item mt-3">
                    <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                        <i class="fas fa-camera me-2 small"></i>
                        <span>{{ $photoStudioName }}</span>
                    </div>
                </li>

            <!-- Photo Studio Dashboard -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.index') || request()->routeIs('photo-studio.dashboard') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-th-large me-3" style="width: 20px;"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Active Sessions -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.sessions.active') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.sessions.active') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-clock me-3" style="width: 20px;"></i>
                    <span>Active Sessions</span>
                    @if(isset($activeSessions) && $activeSessions > 0)
                        <span class="badge bg-danger ms-auto">{{ $activeSessions }}</span>
                    @endif
                </a>
            </li>

            <!-- Studio Categories -->
            <li class="nav-item">
                <a href="#studioCategoriesMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.categories.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('photo-studio.categories.*') ? 'true' : 'false' }}">
                    <i class="fas fa-layer-group me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Categories</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('photo-studio.categories.*') ? 'show' : '' }}" id="studioCategoriesMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.categories.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.categories.index') ? 'text-white' : '' }}">
                                <i class="fas fa-list me-2"></i> All Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.categories.create') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.categories.create') ? 'text-white' : '' }}">
                                <i class="fas fa-plus me-2"></i> Add Category
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Physical Rooms (Optional) -->
            <li class="nav-item">
                <a href="#studioRoomsMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.rooms.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('photo-studio.rooms.*') ? 'true' : 'false' }}">
                    <i class="fas fa-door-open me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Physical Rooms</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('photo-studio.rooms.*') ? 'show' : '' }}" id="studioRoomsMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.rooms.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.rooms.index') ? 'text-white' : '' }}">
                                <i class="fas fa-list me-2"></i> All Rooms
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.rooms.create') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.rooms.create') ? 'text-white' : '' }}">
                                <i class="fas fa-plus me-2"></i> Add Room
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Customer Management -->
            <li class="nav-item">
                <a href="#studioCustomersMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.customers.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('photo-studio.customers.*') ? 'true' : 'false' }}">
                    <i class="fas fa-users me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Customers</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('photo-studio.customers.*') ? 'show' : '' }}" id="studioCustomersMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.customers.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.customers.index') ? 'text-white' : '' }}">
                                <i class="fas fa-list me-2"></i> All Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.customers.create') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.customers.create') ? 'text-white' : '' }}">
                                <i class="fas fa-user-plus me-2"></i> Add Customer
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Session History -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.sessions.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.sessions.index') || request()->routeIs('photo-studio.sessions.show') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-history me-3" style="width: 20px;"></i>
                    <span>Session History</span>
                </a>
            </li>

            <!-- Reports & Analytics -->
            <li class="nav-item">
                <a href="#studioReportsMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.reports.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('photo-studio.reports.*') ? 'true' : 'false' }}">
                    <i class="fas fa-chart-bar me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Reports</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('photo-studio.reports.*') ? 'show' : '' }}" id="studioReportsMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.reports.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.reports.index') ? 'text-white' : '' }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.reports.daily') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.reports.daily') ? 'text-white' : '' }}">
                                <i class="fas fa-calendar-day me-2"></i> Daily Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.reports.revenue') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.reports.revenue') ? 'text-white' : '' }}">
                                <i class="fas fa-naira-sign me-2"></i> Revenue Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.reports.occupancy') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.reports.occupancy') ? 'text-white' : '' }}">
                                <i class="fas fa-percentage me-2"></i> Occupancy Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.reports.customers') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.reports.customers') ? 'text-white' : '' }}">
                                <i class="fas fa-user-friends me-2"></i> Customer Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('photo-studio.reports.category-performance') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('photo-studio.reports.category-performance') ? 'text-white' : '' }}">
                                <i class="fas fa-chart-line me-2"></i> Category Performance
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Settings -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.settings.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.settings.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-cog me-3" style="width: 20px;"></i>
                    <span>Settings</span>
                </a>
            </li>

            
            @endif

            @if($canPropRental)
                <!-- Prop Rental Section Header -->
                <li class="nav-item mt-4">
                    <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                        <i class="fas fa-guitar me-2 small"></i>
                        <span>{{ $propRentalName }}</span>
                    </div>
                </li>
                       
            <!-- Prop Rental Dashboard -->
            <li class="nav-item">
                <a href="{{ route('prop-rental.dashboard') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('prop-rental.dashboard') ? 'text-white' : '' }}">
                    <i class="fas fa-chart-line me-2"></i> Dashboard
                </a>
            </li>

            <!-- All Props -->
            <li class="nav-item">
                <a href="{{ route('prop-rental.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('prop-rental.index') && !request()->has('tab') ? 'text-white' : '' }}">
                    <i class="fas fa-th-large me-2"></i> All Props
                </a>
            </li>

            <!-- Active Rentals -->
            <li class="nav-item">
                <a href="{{ route('prop-rental.index', ['tab' => 'active-rentals']) }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->get('tab') === 'active-rentals' || request()->routeIs('prop-rental.rentals.*') ? 'text-white' : '' }}">
                    <i class="fas fa-calendar-check me-2"></i> Active Rentals
                </a>
            </li>

            <!-- Prop Rental Customers -->
            <li class="nav-item">
                <a href="{{ route('prop-rental.index', ['tab' => 'customers']) }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->get('tab') === 'customers' || request()->routeIs('prop-rental.lounge.customers.*') ? 'text-white' : '' }}">
                    <i class="fas fa-users me-2"></i> Customers
                </a>
            </li>

            <!-- Prop Rental Calendar -->
            <li class="nav-item">
                <a href="{{ route('prop-rental.index', ['tab' => 'calendar']) }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->get('tab') === 'calendar' ? 'text-white' : '' }}">
                    <i class="fas fa-calendar me-2"></i> Calendar
                </a>
            </li>

            <!-- Prop Rental Customers -->
            <li class="nav-item">
                <a href="{{ route('prop-rental.reports') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->get('tab') === 'customers' || request()->routeIs('prop-rental.lounge.customers.*') ? 'text-white' : '' }}">
                    <i class="fas fa-chart-bar me-2"></i> Reports
                </a>
            </li>
            
            

            @endif

            @if($canSystemManagement)
                <!-- System & Settings Section Header -->
                <li class="nav-item mt-3">
                    <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                        <i class="fas fa-cog me-2 small"></i>
                        <span>System Management</span>
                    </div>
                </li>
                
                @if($canReports)
                    <!-- Reports -->
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('reports.*') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-chart-bar me-3" style="width: 20px;"></i>
                            <span>Reports & Analytics</span>
                        </a>
                    </li>
                @endif

                @if($canSystemUpdate)
                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link text-white-50 d-flex align-items-center py-2 px-4 w-100 text-start border-0 bg-transparent"
                            data-bs-toggle="modal"
                            data-bs-target="#systemUpdateModal"
                        >
                            <i class="fas fa-cloud-arrow-down me-3" style="width: 20px;"></i>
                            <span>Update Application</span>
                        </button>
                    </li>
                @endif
            
            <!-- Settings -->
            {{-- <li class="nav-item">
                <a href="#settingsMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('settings.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('settings.*') ? 'true' : 'false' }}">
                    <i class="fas fa-sliders-h me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Settings</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('settings.*') ? 'show' : '' }}" id="settingsMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('settings.general') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('settings.general') ? 'text-white' : '' }}">
                                <i class="fas fa-cog me-2"></i> General Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settings.business') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('settings.business') ? 'text-white' : '' }}">
                                <i class="fas fa-building me-2"></i> Business Units
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settings.integrations') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('settings.integrations') ? 'text-white' : '' }}">
                                <i class="fas fa-plug me-2"></i> Integrations
                            </a>
                        </li>
                    </ul>
                </div>
            </li> --}}
            
                @if($canUserManagementMenu)
                    <!-- User Management with Submenu -->
                    <li class="nav-item">
                        <a href="#userManagementMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('departments.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('departments.*') ? 'true' : 'false' }}">
                            <i class="fas fa-user-shield me-3" style="width: 20px;"></i>
                            <span class="flex-grow-1">User Management</span>
                            <i class="fas fa-chevron-down small"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('departments.*') ? 'show' : '' }}" id="userManagementMenu">
                            <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                                @if($canUsersManage)
                                    <li class="nav-item">
                                        <a href="{{ route('users.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('users.index') || request()->is('users') ? 'text-white' : '' }}">
                                            <i class="fas fa-users me-2"></i> All Users
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('users.create') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('users.create') ? 'text-white' : '' }}">
                                            <i class="fas fa-user-plus me-2"></i> Add New User
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('users.activity') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('users.activity') ? 'text-white' : '' }}">
                                            <i class="fas fa-history me-2"></i> Activity Log
                                        </a>
                                    </li>
                                @endif
                                @if($canRolesManage)
                                    <li class="nav-item">
                                        <a href="{{ route('roles.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('roles.*') ? 'text-white' : '' }}">
                                            <i class="fas fa-user-tag me-2"></i> Roles & Permissions
                                        </a>
                                    </li>
                                @endif
                                @if($canDepartmentsManage)
                                    <li class="nav-item">
                                        <a href="{{ route('departments.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('departments.*') ? 'text-white' : '' }}">
                                            <i class="fas fa-sitemap me-2"></i> Departments
                                        </a>
                                    </li>
                                @endif
                                @if($canSecurityManage)
                                    <li class="nav-item">
                                        <a href="{{ route('users.security') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('users.security') ? 'text-white' : '' }}">
                                            <i class="fas fa-shield-alt me-2"></i> Security Settings
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif
            @endif
            
        </ul>
    </nav>
    
    <!-- User Profile Section -->
    <div class="p-3 border-top border-secondary border-opacity-25 mt-auto">
        <div class="d-flex align-items-center gap-3">
            <div class="text-primary fs-1">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="flex-grow-1 small">
                <div class="text-white fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                <div class="text-secondary">{{ ucfirst($user->role) }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-danger p-0" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

@if($canSystemUpdate)
    <div class="modal fade" id="systemUpdateModal" tabindex="-1" aria-labelledby="systemUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="systemUpdateModalLabel">
                        <i class="fas fa-cloud-arrow-down me-2 text-primary"></i>
                        Update Application
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        This will run a full system update now.
                    </p>
                    <ul class="mb-0 ps-3">
                        <li>Create a database backup in <code>storage/backups</code></li>
                        <li>Run <code>git pull</code></li>
                        <li>Run <code>php artisan migrate --force</code></li>
                        <li>Run <code>php artisan db:seed --force</code></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('system.update.run') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-rotate me-2"></i>Confirm Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
