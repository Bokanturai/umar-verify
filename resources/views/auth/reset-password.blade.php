<x-guest-layout>
    <div class="text-center mb-4">
         <div class="logo-container">
            <div class="logo-badge">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Imam data sub Logo" style="height: 50px;">
            </div>
        </div>
        <h2 class="h4 fw-bold text-dark mb-1">Reset Password</h2>
        <p class="text-muted small">Secure your account with a new password</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Password Reset Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email Address --}}
        <div class="mb-3">
            <label class="tiny-label" for="email">Email Address</label>
            <div class="input-group">
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $request->email) }}" 
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
            <label class="tiny-label" for="password">New Password</label>
            <div class="pass-group position-relative">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Create a secure password">
                <span class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer"></span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- Password Strength Bar --}}
            <div class="progress mt-2" style="height: 4px;">
                <div id="passwordStrengthBar" class="progress-bar" role="progressbar"></div>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <small id="passwordStrengthText" class="text-muted tiny fw-bold"></small>
            </div>
        </div>

        {{-- Confirm Password Field --}}
        <div class="mb-4">
            <label class="tiny-label" for="password_confirmation">Confirm Password</label>
            <div class="pass-group position-relative">
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="Confirm new password">
                <span class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer"></span>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mb-4">
            <button type="submit" class="btn btn-primary w-100">
                <span class="btn-text">Update Password</span>
            </button>
        </div>

        {{-- Back to Login --}}
        <div class="text-center">
            <p class="mb-0 small text-muted">
                Suddenly remembered? 
                <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Back to Sign In</a>
            </p>
        </div>
    </form>

    <div class="text-center mt-5">
        <p class="text-muted small mb-0">&copy; {{ date('Y') }} Quick Slip</p>
    </div>
</x-guest-layout>
