<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">Violet Marella</a>
        <small class="sidebar-subtitle">Management Suite</small>
    </div>

    <div class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('gift-store') }}"><i class="fas fa-gift me-2"></i>Gift Store</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('supermarket') }}"><i class="fas fa-shopping-cart me-2"></i>Supermarket</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('music-studio') }}"><i class="fas fa-music me-2"></i>Music Studio</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('instrument-rental') }}"><i class="fas fa-guitar me-2"></i>Instrument Rental</a></li>
        </ul>

        <hr class="sidebar-divider">

        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="{{ route('reports') }}"><i class="fas fa-chart-bar me-2"></i>Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('settings') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('users') }}"><i class="fas fa-users me-2"></i>Users</a></li>
        </ul>
    </div>
</nav>
