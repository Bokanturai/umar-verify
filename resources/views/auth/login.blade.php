<x-guest-layout>
    <div class="text-center mb-4">
        <div class="logo-container">
            <div class="logo-badge">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="imam data sub" style="height: 50px;">
            </div>
        </div>
        <h2 class="h4 fw-bold text-dark mb-1">Welcome Back</h2>
        <p class="text-muted small">Sign in to manage your digital ecosystem</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email Field --}}
        <div class="mb-3">
            <label class="tiny-label" for="email">Email Address</label>
            <div class="input-group">
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
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

        {{-- Password Field --}}
        <div class="mb-3">
            <label class="tiny-label" for="password">Password</label>
            <div class="pass-group position-relative">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter your password">
                <span class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer"></span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Remember Me + Forgot Password --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label small text-muted" for="remember_me">Keep me logged in</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-success small text-decoration-none fw-bold">Forgot Password?</a>
            @endif
        </div>

        {{-- Submit Button --}}
        <div class="mb-4">
            <button type="submit" class="btn btn-primary w-100">
                <span class="btn-text">Sign In to Account</span>
            </button>
        </div>

        {{-- Register Link --}}
        <div class="text-center">
            <p class="mb-0 small text-muted">
                Don’t have an account? 
                <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Create One Now</a>
            </p>
        </div>
    </form>
    

    <div class="text-center mt-5">
        <p class="text-muted small mb-0">&copy; {{ date('Y') }} Quick Slip</p>
    </div>
</x-guest-layout>
