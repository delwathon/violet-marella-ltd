<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Violet Marella</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('gift-store') }}">Gift Store</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('instrument-rental') }}">Instrument Rental</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('music-studio') }}">Music Studio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports') }}">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('settings') }}">Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('supermarket') }}">Supermarket</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('users') }}">Users</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
