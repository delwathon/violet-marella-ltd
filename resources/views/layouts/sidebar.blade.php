<!-- Sidebar Navigation -->
<aside class="d-flex flex-column bg-dark text-white vh-100 position-fixed start-0 top-0 overflow-auto" style="width: 300px; z-index: 1000;">
    <!-- Sidebar Header -->
    <div class="p-4 border-bottom border-secondary border-opacity-25">
        <h3 class="h5 mb-0 fw-semibold">Violet Marella Ltd</h3>
    </div>
    
    <!-- Sidebar Navigation -->
    <nav class="flex-grow-1 py-3">
        <ul class="nav flex-column">
            
            <!-- Overall Dashboard -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-home me-3" style="width: 20px;"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Lounge Section Header -->
            <li class="nav-item mt-3">
                <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                    <i class="fas fa-store me-2 small"></i>
                    <span>Lounge</span>
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
                                <span>Inventory</span>
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

            
            <!-- Anire Craft Store Section Header -->
            <li class="nav-item mt-4">
                <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                    <i class="fas fa-gift me-2 small"></i>
                    <span>Anire Craft Store</span>
                </div>
            </li>
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('anire-craft-store.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('anire-craft-store.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-gift me-3" style="width: 20px;"></i>
                    <span>Dashboard</span>
                </a>
            </li>



            <!-- Photo Studio Section Header -->
            <li class="nav-item mt-4">
                <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                    <i class="fas fa-camera me-2 small"></i>
                    <span>Photo Studio</span>
                </div>
            </li>
            
            <!-- Photo Studio Dashboard -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.dashboard') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.dashboard') || (request()->routeIs('photo-studio.index') && !request()->has('tab')) ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-tachometer-alt me-3" style="width: 20px;"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Active Sessions -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.sessions.active') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.sessions.active') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-play-circle me-3" style="width: 20px;"></i>
                    <span>Active Sessions</span>
                </a>
            </li>

            <!-- Session History -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.sessions.history') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.sessions.history') || request()->routeIs('photo-studio.sessions.index') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-history me-3" style="width: 20px;"></i>
                    <span>Session History</span>
                </a>
            </li>

            <!-- Studio Customers -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.customers.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.customers.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-user-friends me-3" style="width: 20px;"></i>
                    <span>Customers</span>
                </a>
            </li>

            <!-- Studio Management -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.studios.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.studios.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-door-open me-3" style="width: 20px;"></i>
                    <span>Studio Rooms</span>
                </a>
            </li>

            <!-- Rates & Pricing -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.rates.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.rates.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-tags me-3" style="width: 20px;"></i>
                    <span>Rates & Pricing</span>
                </a>
            </li>

            <!-- QR Scanner -->
            <li class="nav-item">
                <a href="#" class="nav-link text-white-50 d-flex align-items-center py-2 px-4" onclick="openQRScanner(); return false;">
                    <i class="fas fa-qrcode me-3" style="width: 20px;"></i>
                    <span>QR Scanner</span>
                </a>
            </li>

            <!-- Studio Reports -->
            <li class="nav-item">
                <a href="{{ route('photo-studio.reports.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('photo-studio.reports.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-chart-line me-3" style="width: 20px;"></i>
                    <span>Studio Reports</span>
                </a>
            </li>


            
            <!-- Prop Rental Section Header -->
            <li class="nav-item mt-4">
                <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                    <i class="fas fa-guitar me-2 small"></i>
                    <span>Prop Rental</span>
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
            
            

            <!-- Other Section Header -->
            <li class="nav-item mt-4">
                <div class="px-4 py-2 text-secondary text-uppercase fw-semibold small d-flex align-items-center">
                    <i class="fas fa-ellipsis-h me-2 small"></i>
                    <span>Others</span>
                </div>
            </li>

            <!-- Reports -->
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('reports.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-chart-bar me-3" style="width: 20px;"></i>
                    <span>All Business Reports</span>
                </a>
            </li>
            
            <!-- Settings -->
            {{-- @if(auth()->user()->isAdmin()) --}}
            <li class="nav-item">
                <a href="#settingsMenu" class="nav-link text-white-50 d-flex align-items-center py-2 px-4 {{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'bg-primary text-white' : '' }}" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'true' : 'false' }}">
                    <i class="fas fa-cog me-3" style="width: 20px;"></i>
                    <span class="flex-grow-1">Settings</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'show' : '' }}" id="settingsMenu">
                    <ul class="nav flex-column bg-black bg-opacity-25 py-1">
                        <li class="nav-item">
                            <a href="{{ route('settings.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('settings.index') ? 'text-white' : '' }}">
                                <i class="fas fa-sliders-h me-2"></i> General Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link text-white-50 d-flex align-items-center py-2 small {{ request()->routeIs('users.*') ? 'text-white' : '' }}">
                                <i class="fas fa-user-shield me-2"></i> User Management
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            {{-- @endif --}}
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