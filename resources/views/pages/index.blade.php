@extends('layouts.app')
@section('title', 'Login - Violet Marella Limited')
@push('styles')
<link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card p-5">
                    <div class="text-center mb-4">
                        <h1 class="brand-logo">Violet Marella</h1>
                        <p class="brand-subtitle">Business Management Suite</p>
                    </div>
                    <form id="loginForm">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
                            <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control" id="password" placeholder="Password" required>
                            <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">Remember me</label>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <a href="#" class="text-decoration-none">Forgot password?</a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
