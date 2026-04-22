<x-guest-layout>
    <div class="text-center mb-4">
        <div class="logo-container">
            <div class="logo-badge">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Smart Idea Logo" style="height: 50px;">
            </div>
        </div>
        <h2 class="h4 fw-bold text-dark mb-1">Verify Email</h2>
        <p class="text-muted small mb-4">Account Activation Required</p>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4 text-start">
            <p class="text-muted small mb-3" style="line-height: 1.6;">
                To complete your registration and activate your account, please check your email inbox and click the verification link we sent to you.
            </p>
            <p class="text-muted small mb-3" style="line-height: 1.6;">
                If you don't see the email, please check your <span class="fw-bold text-primary">spam or junk folder</span>.
            </p>
            <div class="mt-3 pt-3 border-top">
                <p class="small mb-0 text-dark fw-bold">
                    <i class="ti ti-alert-circle me-1 text-warning"></i> Note: 
                    <span class="fw-normal">You must verify your email before you can start using your account.</span>
                </p>
            </div>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small text-center mb-4 border-0 shadow-sm"
            style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
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
        <p class="text-muted small mb-0">&copy; {{ date('Y') }} Smart Idea</p>
    </div>
</x-guest-layout>