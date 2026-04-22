<x-app-layout>
    <title>Smart Idea - {{ $title ?? 'Buy Data' }}</title>

    <div class="row">
        <div class="col-xxl-12 col-xl-12">
            <div class="row mt-3">
                <div class="col-xl-6 mb-3">
                    <div class="card custom-card shadow-sm border-0">
                        <div class="card-header justify-content-between bg-primary text-white rounded-top">
                            <div class="card-title fw-semibold">
                                <i class="bi bi-credit-card me-2"></i> Buy Data
                            </div>
                        </div>

                        <div class="card-body">
                            <center class="mb-3">
                                <img src="{{ asset('assets/img/apps/network_providers.png') }}"
                                     class="img-fluid mb-3 rounded-2"
                                     style="width: 45%; min-width: 120px;" alt="Network Providers">
                            </center>

                            <p class="text-center text-muted mb-4">
                                Select your mobile network, enter your phone number, and choose a data plan to proceed.
                            </p>

                            {{-- Flash Messages --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                    {!! session('success') !!}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 text-start small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Buy Data Form --}}
                            <form id="buyDataForm" method="POST" action="{{ route('buydata') }}">
                                @csrf

                                {{-- Network --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Select Network</label>
                                    <select name="network" id="service_id" class="form-select text-center" required>
                                        <option value="">Choose Network</option>
                                        @foreach ($servicename as $service)
                                            <option value="{{ $service->service_id }}">{{ $service->service_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Bundle --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Select Bundle</label>
                                    <select name="bundle" id="bundle" class="form-select text-center" required>
                                        <option value="">Choose Bundle</option>
                                    </select>
                                </div>

                                {{-- Amount --}}
                                <div class="mb-3 text-start">
                                    <label for="amount" class="form-label fw-semibold d-flex justify-content-between">
                                        <span>Amount</span>
                                        <small class="text-muted">Balance: 
                                            <strong class="text-success">
                                                ₦{{ number_format($wallet->balance ?? 0, 2) }}
                                            </strong>
                                        </small>
                                    </label>
                                    <input type="text" id="amountToPay" name="amount" readonly class="form-control text-center" />
                                </div>

                                {{-- Phone Number --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" id="mobileno" name="mobileno"
                                           oninput="validateNumber()" 
                                           class="form-control text-center"
                                           placeholder="08012345678"
                                           maxlength="11" required>
                                    <small id="networkResult" class="text-muted"></small>
                                </div>

                                {{-- Submit --}}
                                <div class="d-grid mt-4">
                                    <button type="button" class="btn btn-primary btn-lg fw-semibold"
                                            data-bs-toggle="modal" data-bs-target="#pinModal">
                                        Proceed to Buy
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right Column --}}
                @include('utilities.data-advert')
            </div>
        </div>
    </div>

 {{-- PIN Confirmation Modal --}}
<div class="modal fade" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold" id="pinModalLabel">
                    <i class="bi bi-shield-lock-fill me-2"></i> Enter Your Transaction PIN
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center py-4">
                <p class="text-muted mb-3 small">
                    For your security, please confirm your <strong>5-digit transaction PIN</strong> before proceeding.
                </p>

                <div class="d-flex justify-content-center">
                    <input 
                        type="password" 
                        name="pin" 
                        id="pinInput" 
                        class="form-control text-center fw-bold fs-3 py-3 border-2 border-primary rounded-pill shadow-sm w-50" 
                        maxlength="5" 
                        inputmode="numeric" 
                        placeholder="•••••"
                        required
                        style="letter-spacing: 10px; font-family: 'Courier New', monospace;"
                    >
                </div>

                <small id="pinError" class="text-danger d-none mt-3 d-block fw-semibold">
                    Incorrect PIN. Please try again.
                </small>
            </div>

            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">
                    Cancel
                </button>

                {{-- Main action button --}}
                <button type="button" id="confirmPinBtn" class="btn btn-primary px-4 rounded-pill fw-semibold">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="pinLoader" role="status" aria-hidden="true"></span>
                    <span id="confirmPinText">Confirm & Proceed</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('confirmPinBtn').addEventListener('click', function() {
    const confirmBtn = this;
    const loader = document.getElementById('pinLoader');
    const confirmText = document.getElementById('confirmPinText');
    const pinError = document.getElementById('pinError');
    const pin = document.getElementById('pinInput').value.trim();

    // If PIN field is empty, stop
    if (!pin) {
        pinError.textContent = "Please enter your PIN.";
        pinError.classList.remove('d-none');
        return;
    }

    // Disable the button & show loader
    confirmBtn.disabled = true;
    loader.classList.remove('d-none');
    confirmText.textContent = "Verifying...";

    fetch("{{ route('verify.pin') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ pin })
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            // PIN is correct → submit the main form
            document.getElementById('buyDataForm').submit();
        } else {
            // Incorrect PIN → show error, reset button
            pinError.textContent = "Incorrect PIN. Please try again.";
            pinError.classList.remove('d-none');
            confirmBtn.disabled = false;
            loader.classList.add('d-none');
            confirmText.textContent = "Confirm & Proceed";
        }
    })
    .catch(err => {
        console.error("PIN check failed:", err);
        pinError.textContent = "Network error. Please try again.";
        pinError.classList.remove('d-none');
        confirmBtn.disabled = false;
        loader.classList.add('d-none');
        confirmText.textContent = "Confirm & Proceed";
    });
});
</script>

</x-app-layout>
