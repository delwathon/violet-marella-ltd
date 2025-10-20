<div class="d-flex justify-content-between align-items-center">
    <div>
        <button class="btn btn-link d-md-none p-0 me-3" onclick="VioletMarellaCommon.toggleSidebar()">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <h5 class="mb-0" id="currentPageTitle">Business Suite</h5>
        <small class="text-muted" id="currentPageSubtitle">Welcome to Violet Marella Management Suite</small>
    </div>
    
    <div class="d-flex align-items-center">
        <div class="dropdown me-3">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-bell me-1"></i>
                Notifications
                <span class="badge bg-danger ms-1">3</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Low stock alert - Gift items</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-clock text-info me-2"></i>Studio booking reminder</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-chart-bar text-success me-2"></i>Monthly report ready</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center" href="notifications.html">View All Notifications</a></li>
            </ul>
        </div>
        
        <div class="user-profile dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->first_name.' '. $user->last_name ?? 'U', 0, 1)) }}
                </div>
                <div class="d-none d-md-block ms-2">
                    <div class="fw-semibold">{{ $user->first_name.' '. $user->last_name ?? 'Unknown User' }}</div>
                    <small class="text-muted">{{ ucfirst($user->role ?? 'User') }}</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
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
