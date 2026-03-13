<x-guest-layout>
    <div class="text-center mb-4">
        <div class="logo-container">
            <div class="logo-badge">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="imam data sub Logo" style="height: 50px;">
            </div>
        </div>
        <h2 class="h4 fw-bold text-dark mb-1">Forgot Password</h2>
        <p class="text-muted small">We'll help you get back into your account</p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Email Input --}}
        <div class="mb-4">
            <label class="tiny-label" for="email">Email Address</label>
            <div class="input-group">
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="name@company.com">
                <span class="input-group-text">
                    <i class="ti ti-mail"></i>
                </span>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mb-4">
            <button type="submit" class="btn btn-primary w-100">
                <span class="btn-text">Send Reset Link</span>
            </button>
        </div>

        {{-- Back to Login --}}
        <div class="text-center">
            <p class="mb-0 small text-muted">
                Wait, I remember it! 
                <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Return to Sign In</a>
            </p>
        </div>
    </form>

    <div class="text-center mt-5">
        <p class="text-muted small mb-0">&copy; {{ date('Y') }} Quick Slip</p>
    </div>
</x-guest-layout>
