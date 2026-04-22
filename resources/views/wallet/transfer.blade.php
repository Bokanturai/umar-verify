<x-app-layout>
    <title>Smart Idea - P2P Balance Transfer</title>

    <div class="container-fluid py-4 px-0 px-md-4">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 fw-bold text-gray-800 mb-1">P2P Transfer</h1>
            <p class="text-muted small mb-0">Send funds instantly to other wallets on the platform.</p>
        </div>

        @if(session('success') || session('error'))
            <div class="row g-0 g-md-4 mb-4">
                <div class="col-12 col-lg-8">
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-4">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-4">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="row g-0 g-md-4">
            <!-- Transfer Form -->
            <div class="col-12 col-xl-8 col-lg-12">
                <div class="card border-0 shadow-sm rounded-0 rounded-md-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-primary">
                            <i class="fas fa-paper-plane me-2"></i>Transfer Details
                        </h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <form method="POST" action="{{ route('wallet.transfer.process') }}" id="transferForm">
                            @csrf
                            
                            <!-- Recipient Lookup -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted mb-2">Recipient Identifier</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-4 px-3">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="wallet_id" id="recipient_id" 
                                           class="form-control rounded-end-4 bg-light border-0 py-3" 
                                           placeholder="Enter Phone, Email or Wallet ID" required>
                                </div>
                                <div id="recipient_info" class="mt-2 text-success fw-bold small" style="display: none;">
                                    <i class="fas fa-user-check me-1"></i> <span id="recipient_name"></span>
                                </div>
                                <div id="recipient_error" class="mt-2 text-danger small" style="display: none;">
                                    <i class="fas fa-user-times me-1"></i> Recipient not found.
                                </div>
                            </div>

                            <div class="row">
                                <!-- Amount -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-muted mb-2">Amount (₦)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 rounded-start-4 px-3">₦</span>
                                        <input type="number" name="amount" class="form-control rounded-end-4 bg-light border-0 py-3" 
                                               placeholder="0.00" min="10" required>
                                    </div>
                                </div>
                                
                                <!-- PIN -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-muted mb-2">Transaction PIN</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 rounded-start-4 px-3">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input type="password" name="pin" class="form-control rounded-end-4 bg-light border-0 py-3" 
                                               placeholder="4-digit PIN" maxlength="4" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted mb-2">Description (Optional)</label>
                                <textarea name="description" class="form-control rounded-4 bg-light border-0 p-3" 
                                          placeholder="A brief note about this transfer..." rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mt-2 transition-all" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Send Funds Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Balance Sidebar -->
            <div class="col-12 col-xl-4 col-lg-12 mt-2 mt-md-0">
                <div class="card border-0 shadow-sm rounded-0 rounded-md-4 p-4 text-white text-center h-100" 
                     style="background: linear-gradient(135deg, #0d5c3e 0%, #0a4a31 100%);">
                    <div class="mb-3">
                        <i class="fas fa-wallet fa-3x text-warning opacity-75"></i>
                    </div>
                    <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="letter-spacing: 1px;">Available Balance</h6>
                    <h2 class="fw-bold mb-4" style="color: #ffd700;">₦{{ number_format($wallet->balance, 2) }}</h2>
                    <hr class="opacity-10 mb-4">
                    
                    <div class="text-start">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Transfer Rules:</h6>
                        <ul class="list-unstyled small opacity-75">
                            <li class="mb-2"><i class="fas fa-check-circle text-warning me-2"></i>Minimum transfer: ₦10.00</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-warning me-2"></i>Instant processing</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-warning me-2"></i>Zero transfer fees</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-warning me-2"></i>Secured with 2FA PIN</li>
                        </ul>
                    </div>
                    
                    <div class="mt-auto">
                         <a href="{{ route('wallet') }}" class="btn btn-outline-warning w-100 rounded-pill py-2 small fw-bold">
                            <i class="fas fa-arrow-left me-2"></i> Back to Wallet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            let timeout = null;

            $('#recipient_id').on('input', function() {
                clearTimeout(timeout);
                let value = $(this).val();

                if (value.length < 3) {
                    $('#recipient_info, #recipient_error').hide();
                    return;
                }

                timeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('wallet.transfer.verify') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            wallet_id: value
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#recipient_name').text(response.user_name);
                                $('#recipient_info').fadeIn();
                                $('#recipient_error').hide();
                                $('#submitBtn').prop('disabled', false);
                            } else {
                                $('#recipient_error').fadeIn();
                                $('#recipient_info').hide();
                                $('#submitBtn').prop('disabled', true);
                            }
                        },
                        error: function() {
                            $('#recipient_error').fadeIn();
                            $('#recipient_info').hide();
                        }
                    });
                }, 500);
            });
        });
    </script>
    @endpush
    <style>
        .transition-all { transition: all 0.3s ease; }
        .btn-primary:active { transform: scale(0.98); }
        .input-group-text { min-width: 50px; justify-content: center; }
    </style>
</x-app-layout>
