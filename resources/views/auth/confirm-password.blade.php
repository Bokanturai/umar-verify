<x-guest-layout>
    <div class="text-center mb-4">
        <div class="logo-container">
            <div class="logo-badge">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Smart Idea Logo" style="height: 50px;">
            </div>
        </div>
        <h2 class="h4 fw-bold text-dark mb-1">Confirm Security</h2>
        <p class="text-muted small">Please verify your identity before proceeding</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        {{-- Password Field --}}
        <div class="mb-4">
            <label class="tiny-label" for="password">Security Password</label>
            <div class="pass-group position-relative">
                <input type="password" id="password" name="password" required autocomplete="current-password"
                    class="form-control @error('password') is-invalid @enderror" placeholder="Verify your password">
                <span
                    class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer"></span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mb-4">
            <button type="submit" class="btn btn-primary w-100">
                <span class="btn-text">Confirm Identity</span>
            </button>
        </div>

        <div class="text-center mt-5">
            <p class="text-muted small mb-0">&copy; {{ date('Y') }} Smart Idea</p>
        </div>
    </form>
</x-guest-layout>