<x-guest-layout>
    <div class="text-center mb-4">
        <div class="logo-container">
            <div class="logo-badge">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Imam data Sub Logo" style="height: 50px;">
            </div>
        </div>
        <h2 class="h4 fw-bold text-dark mb-1">Verify Email</h2>
        <p class="text-muted small">One last step to secure your experience</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small text-center mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            <i class="ti ti-circle-check me-2"></i> A new verification link has been sent!
        </div>
    @endif

    <div class="d-flex flex-column gap-3 mb-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-100">
                <span class="btn-text">Resend Verification</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted small w-100 text-decoration-none fw-bold">
                Not now? <span class="text-danger">Log Out</span>
            </button>
        </form>
    </div>

    <div class="text-center mt-5">
        <p class="text-muted small mb-0">&copy; {{ date('Y') }} Quick Slip</p>
    </div>
</x-guest-layout>
