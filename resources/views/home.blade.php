@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="mb-2">{{ __('You are logged in!') }}</p>
                    <p class="text-muted mb-0">
                        Redirecting to dashboard in
                        <strong id="dashboardRedirectCountdown">5</strong>
                        seconds...
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const redirectUrl = @json(route('dashboard'));
        const countdownElement = document.getElementById('dashboardRedirectCountdown');
        let secondsRemaining = 5;

        const intervalId = window.setInterval(function () {
            secondsRemaining -= 1;

            if (countdownElement && secondsRemaining >= 0) {
                countdownElement.textContent = String(secondsRemaining);
            }

            if (secondsRemaining <= 0) {
                window.clearInterval(intervalId);
            }
        }, 1000);

        window.setTimeout(function () {
            window.location.href = redirectUrl;
        }, 5000);
    });
</script>
@endpush
