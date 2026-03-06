@extends('layouts.app')
@section('title', 'Secure Login - ' . ($companyProfile['name'] ?? 'Violet Marella Limited'))

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $availableBusinessNames = collect($businessDirectory ?? [])->pluck('name')->filter()->values()->all();
    $showcaseBusinessNames = !empty($availableBusinessNames)
        ? implode(', ', $availableBusinessNames)
        : 'all assigned businesses';
    $emailPlaceholder = ($companyProfile['email'] ?? '') !== ''
        ? $companyProfile['email']
        : 'name@example.com';
@endphp
<div class="auth-shell row g-0">
    <section class="col-lg-6 auth-showcase">
        <div class="showcase-overlay"></div>
        <div class="showcase-content">
            <span class="eyebrow">{{ $companyProfile['name'] ?? 'Violet Marella Limited' }}</span>
            <h1 class="showcase-title">Multi-Business Operations, One Secure Entry Point.</h1>
            <p class="showcase-text">
                Access {{ $showcaseBusinessNames }} workflows from one unified platform.
            </p>

            <div class="showcase-pills">
                <span class="showcase-pill"><i class="fas fa-layer-group"></i> Role-based access</span>
                <span class="showcase-pill"><i class="fas fa-shield-alt"></i> Security policies enforced</span>
                <span class="showcase-pill"><i class="fas fa-chart-line"></i> Real-time business insights</span>
            </div>
        </div>
    </section>

    <section class="col-lg-6 auth-panel">
        <div class="auth-panel-inner">
            <div class="brand-badge">VM</div>
            <h2 class="auth-title">Welcome back</h2>
            <p class="auth-subtitle">Sign in with your assigned account credentials.</p>

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="input-block">
                    <label for="email" class="form-label">Work Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="{{ $emailPlaceholder }}"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-block">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-row">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Keep me signed in</label>
                    </div>
                    <span class="support-note">Need access? Contact administrator</span>
                </div>

                <button type="submit" class="btn auth-submit w-100">
                    <i class="fas fa-arrow-right-to-bracket me-2"></i>Sign In
                </button>
            </form>

            <div class="auth-footer-note">
                <i class="fas fa-circle-check me-2"></i>Authorized personnel only
            </div>
        </div>
    </section>
</div>
@endsection
