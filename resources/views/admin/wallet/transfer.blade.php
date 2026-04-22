<x-app-layout>
    <title>Smart Idea Smart User</title>

    <div class="page">
        <div class="main-content app-content custom-margin-top">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xxl-12 col-xl-12">
                        <div class="row mt-3 justify-content-center">
                            {{-- Transfer Form Column --}}
                            <div class="col-xl-6 col-lg-6">
                                <div class="card shadow-lg border-0 rounded-4 h-100">
                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-4">
                                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-send me-2 text-white"></i>Transfer Funds</h5>
                                        <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill">P2P</span>
                                    </div>

                                    <div class="card-body p-4">
                                        <div class="text-center mb-4">
                                            <div class="avatar avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-wallet2 fs-2"></i>
                                            </div>
                                            <h6 class="fw-bold">Send Money Instantly</h6>
                                            <p class="text-muted small">Enter the recipient's Wallet Number, Phone or Email.</p>
                                        </div>

                                        {{-- Flash Messages --}}
                                        @if (session('success'))
                                            <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                                                <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        @endif

                                        @if (session('error'))
                                            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                                                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        @endif

                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                                                <ul class="mb-0 text-start small list-unstyled">
                                                    @foreach ($errors->all() as $error)
                                                        <li><i class="bi bi-dot me-1"></i>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        @endif

                                        {{-- Transfer Form --}}
                                        <form id="transferForm" method="POST" action="{{ route('transfer.process') }}">
                                            @csrf

                                            {{-- Wallet ID --}}
                                            <div class="mb-4 text-start">
                                                <label class="form-label fw-semibold text-dark small text-uppercase">Recipient Wallet No / Phone / Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                                                    <input type="text" id="wallet_id" name="wallet_id"
                                                           class="form-control border-start-0 ps-0"
                                                           placeholder="e.g. 08123456789 or user@example.com"
                                                           required>
                                                    <button class="btn btn-primary px-3" type="button" id="verifyBtn" onclick="verifyUser()">
                                                        Verify
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-2" style="min-height: 20px;">
                                                    <small id="userNameDisplay" class="text-success fw-bold transition-all"></small>
                                                    <small id="userErrorDisplay" class="text-danger fw-bold transition-all"></small>
                                                </div>
                                            </div>

                                            {{-- Amount --}}
                                            <div class="mb-4 text-start">
                                                <label for="amount" class="form-label fw-semibold d-flex justify-content-between">
                                                    <span>Amount</span>
                                                    <small class="text-muted">Balance: 
                                                        <strong class="text-success">
                                                            ₦{{ number_format($wallet->balance ?? 0, 2) }}
                                                        </strong>
                                                    </small>
                                                </label>
                                                <input type="number" id="amount" name="amount"
                                                       class="form-control"
                                                       placeholder="e.g. 5000.00"
                                                       min="10" step="0.01"
                                                       required>
                                            </div>


                                            {{-- Description --}}
                                            <div class="mb-4 text-start">
                                                <label class="form-label fw-semibold text-dark small text-uppercase">Description (Optional)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-card-text text-muted"></i></span>
                                                    <textarea name="description" class="form-control border-start-0 ps-0" rows="2" placeholder="What is this for?"></textarea>
                                                </div>
                                            </div>

                                            {{-- Submit --}}
                                            <div class="d-grid mt-2">
                                                <button type="button" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm"
                                                        id="proceedBtn" disabled
                                                        data-bs-toggle="modal" data-bs-target="#pinModal">
                                                    Proceed to Transfer <i class="bi bi-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Advert Column --}}
                            @include('utilities.transfer-advert')

                        </div>
                    </div>
                </div>
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
                    For your security, please confirm your <strong>transaction PIN</strong> before proceeding.
                </p>

                <div class="d-flex justify-content-center">
                    <input 
                        type="password" 
                        name="pin_confirm" 
                        id="pinInput" 
                        class="form-control text-center fw-bold fs-3 py-3 border-2 border-primary rounded-pill shadow-sm w-50" 
                        maxlength="5" 
                        inputmode="numeric" 
                        placeholder="••••"
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
 <div class="mt-4">
    
    <script>
        function verifyUser() {
            const walletId = document.getElementById('wallet_id').value;
            const userNameDisplay = document.getElementById('userNameDisplay');
            const userErrorDisplay = document.getElementById('userErrorDisplay');
            const proceedBtn = document.getElementById('proceedBtn');
            const verifyBtn = document.getElementById('verifyBtn');

            if (!walletId) {
                userErrorDisplay.textContent = "Please enter a Wallet Number, Phone or Email.";
                userNameDisplay.textContent = "";
                return;
            }

            // UI Feedback
            userNameDisplay.textContent = "";
            userErrorDisplay.textContent = "";
            verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            verifyBtn.disabled = true;

            fetch("{{ route('transfer.verify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ wallet_id: walletId })
            })
            .then(response => response.json())
            .then(data => {
                verifyBtn.innerHTML = 'Verify';
                verifyBtn.disabled = false;

                if (data.success) {
                    userNameDisplay.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + data.user_name;
                    userErrorDisplay.textContent = "";
                    proceedBtn.disabled = false;
                } else {
                    userErrorDisplay.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> User not found.';
                    userNameDisplay.textContent = "";
                    proceedBtn.disabled = true;
                }
            })
            .catch(err => {
                console.error("Verification failed:", err);
                verifyBtn.innerHTML = 'Verify';
                verifyBtn.disabled = false;
                userErrorDisplay.textContent = "Verification failed. Please try again.";
                userNameDisplay.textContent = "";
                proceedBtn.disabled = true;
            });
        }

        document.getElementById('confirmPinBtn').addEventListener('click', function() {
            const confirmBtn = this;
            const loader = document.getElementById('pinLoader');
            const confirmText = document.getElementById('confirmPinText');
            const pinError = document.getElementById('pinError');
            const pin = document.getElementById('pinInput').value.trim();

            if (!pin) {
                pinError.textContent = "Please enter your PIN.";
                pinError.classList.remove('d-none');
                return;
            }

            confirmBtn.disabled = true;
            loader.classList.remove('d-none');
            confirmText.textContent = "Verifying...";
            pinError.classList.add('d-none');

            // Verify PIN via AJAX first
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
                    // Append PIN to the form and submit
                    const form = document.getElementById('transferForm');
                    const pinInput = document.createElement('input');
                    pinInput.type = 'hidden';
                    pinInput.name = 'pin';
                    pinInput.value = pin;
                    form.appendChild(pinInput);
                    
                    form.submit();
                } else {
                    pinError.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Incorrect PIN. Please try again.';
                    pinError.classList.remove('d-none');
                    confirmBtn.disabled = false;
                    loader.classList.add('d-none');
                    confirmText.textContent = "Confirm & Proceed";
                    
                    // Clear input
                    document.getElementById('pinInput').value = '';
                    document.getElementById('pinInput').focus();
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
