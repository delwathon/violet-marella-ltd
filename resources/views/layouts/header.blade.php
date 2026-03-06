<div class="header-shell d-flex align-items-center justify-content-between gap-3">
    <div class="header-left d-flex align-items-center gap-3">
        <button class="btn btn-link d-md-none p-0 app-menu-toggle" onclick="document.querySelector('.sidebar')?.classList.toggle('mobile-open')">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <div class="header-context">
            <span class="workspace-chip">{{ $companyProfile['name'] ?? 'Business' }} Workspace</span>
            <h5 class="mb-0 header-title" id="currentPageTitle">Business Suite</h5>
            <small class="header-subtitle" id="currentPageSubtitle">Unified operations across all business units</small>
        </div>
    </div>

    <div class="header-center d-none d-lg-flex">
        <form class="global-search" onsubmit="return false;">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text" class="form-control" placeholder="Search products, customers, sessions, rentals...">
            <span class="search-shortcut">Ctrl+K</span>
        </form>
    </div>

    <div class="header-right d-flex align-items-center gap-2">
        <button class="btn btn-soft d-none d-xl-inline-flex">
            <i class="fas fa-bolt me-1"></i>Quick Actions
        </button>

        <div class="dropdown me-1">
            <button class="btn btn-soft dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-bell me-1"></i>
                Alerts
                <span class="badge rounded-pill bg-danger ms-1">3</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Low stock alert - Gift items</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-clock text-info me-2"></i>Studio booking reminder</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-chart-bar text-success me-2"></i>Monthly report ready</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center" href="#">View All Alerts</a></li>
            </ul>
        </div>

        <div class="user-profile dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle user-trigger" data-bs-toggle="dropdown">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->first_name . ' ' . $user->last_name ?? 'U', 0, 1)) }}
                </div>
                <div class="d-none d-md-block ms-2">
                    <div class="fw-semibold">{{ $user->first_name . ' ' . $user->last_name ?? 'Unknown User' }}</div>
                    <small class="text-muted">{{ ucfirst($user->role ?? 'User') }}</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
