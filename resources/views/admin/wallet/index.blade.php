<x-app-layout>
 <title>Smart Idea</title>

    <div class="container-fluid py-4 px-md-4">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 fw-bold text-gray-800 mb-1">Wallet Section</h1>
            <p class="text-muted small mb-0">Securely manage your funds and automated deposits.</p>
        </div>

        @if(session('success') || session('error'))
            <div class="row mb-4">
                <div class="col-12">
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

        <div class="row g-4">
            <!-- Automatic Funding Section -->
            <div class="col-xl-6 col-lg-12">
                <div class="card border-0 shadow-sm h-100 rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><i class="fas fa-university me-2 text-primary"></i>Automatic Funding</h5>
                        @if($virtualAccount)
                            <span class="badge bg-success text-white px-3 py-2 rounded-pill">Active</span>
                        @endif
                    </div>

                    @if($virtualAccount)
                        <!-- Bank Card Display -->
                        <div class="virtual-account-card shadow-lg mb-3 position-relative overflow-hidden" 
                             style="background: linear-gradient(135deg, #0d5c3e 0%, #0a4a31 100%); color: #ffd700; border-radius: 1.25rem; padding: 2rem;">
                            
                            <!-- Decorative Background Patterns -->
                            <div class="position-absolute top-0 end-0 opacity-10">
                                <div style="width: 200px; height: 200px; background: radial-gradient(circle, #ffd700 0%, transparent 70%);"></div>
                            </div>
                            <div class="position-absolute bottom-0 start-0 opacity-10">
                                <div style="width: 150px; height: 150px; background: radial-gradient(circle, #ffd700 0%, transparent 70%);"></div>
                            </div>
                            
                            <!-- Card Content -->
                            <div class="position-relative">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <h4 class="fw-bold mb-0 text-white">{{ $virtualAccount->bankName }}</h4>
                                    <i class="fas fa-microchip fa-2x" style="color: #ffd700; opacity: 0.8;"></i>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="small text-uppercase mb-1" style="color: rgba(255, 215, 0, 0.8); letter-spacing: 1px;">Account Number</p>
                                    <div class="d-flex align-items-center gap-3">
                                        <h2 class="mb-0 fw-bold" style="letter-spacing: 0.15em; color: #ffd700;">{{ $virtualAccount->accountNo }}</h2>
                                        <button class="btn btn-sm rounded-3 py-1 px-2 border-0" 
                                                style="background: rgba(255, 215, 0, 0.2); color: #ffd700;"
                                                onclick="copyToClipboard('{{ $virtualAccount->accountNo }}', 'Account number copied!')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <p class="small text-uppercase mb-1" style="color: rgba(255, 215, 0, 0.8); letter-spacing: 1px;">Account Name</p>
                                        <h6 class="fw-bold mb-0 text-white">{{ $virtualAccount->accountName }}</h6>
                                    </div>
                                    <div class="px-3 py-2 rounded fw-bold" style="background: #ffd700; color: #0d5c3e; font-size: 0.75rem;">
                                        SMART LINK
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info border-0 rounded-4 small p-3">
                            <i class="fas fa-info-circle me-2"></i> Funds sent to this account are credited instantly to your wallet.
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building-columns fa-3x text-muted mb-4"></i>
                            <h4 class="fw-bold">No Virtual Account</h4>
                            <p class="text-muted px-4 mb-4">Generate a dedicated virtual account to fund your wallet instantly via bank transfers.</p>
                            <button class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#virtualAccountModal">
                                <i class="fas fa-plus-circle me-2"></i> Create Virtual Account
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Funding Guidelines -->
            <div class="col-xl-6 col-lg-12">
                <div class="card border-0 shadow-sm h-100 rounded-4 p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-shield-halved me-2 text-primary"></i>Funding Guidelines</h5>
                    
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">1</div>
                            <div>
                                <h6 class="fw-bold mb-1">Automated Credit</h6>
                                <p class="text-muted small mb-0">Payments sent to your virtual account are automatically credited to your wallet in real-time.</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">2</div>
                            <div>
                                <h6 class="fw-bold mb-1">Fee Structure</h6>
                                <p class="text-muted small mb-0">A minimal convenience fee (₦0) is applied to each incoming deposit by our banking partners.</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">3</div>
                            <div>
                                <h6 class="fw-bold mb-1">Support Assistance</h6>
                                <p class="text-muted small mb-0">If your account isn't credited within 30 minutes, please contact support with your receipt.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-5">
                        <a href="{{ route('transactions')}}" class="btn btn-outline-primary w-100 rounded-pill py-3 fw-bold">
                           <i class="fas fa-history me-2"></i> Recent Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Virtual Account Modal -->
        <div class="modal fade" id="virtualAccountModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 pt-0">
                        <div class="text-center mb-4">
                            <i class="fas fa-id-card-clip fa-3x text-primary mb-3"></i>
                            <h4 class="fw-bold">Virtual Account Setup</h4>
                            <p class="text-muted small">Confirm your legal details to generate your deposit account.</p>
                        </div>

                        <form method="POST" action="{{ route('wallet.create') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Legal Name</label>
                                <input type="text" class="form-control rounded-3 bg-light border-0" value="{{ Auth::user()->first_name.' '.Auth::user()->last_name.' '.Auth::user()->middle_name }}" readonly>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Phone Number</label>
                                <input type="text" class="form-control rounded-3 bg-light border-0" value="{{ Auth::user()->phone_no }}" readonly>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input shadow-none" type="checkbox" id="confirmCheck" required>
                                <label class="form-check-label small text-muted" for="confirmCheck">
                                    I confirm my details are correct per my KYC submissions.
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                                Generate Account <i class="fas fa-chevron-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <small class="text-muted">Your virtual account will be generated instantly and linked to your wallet.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
<div class="container-fluid px-4 mt-4">

    @push('scripts')
    <script>
        /**
         * Copy text to clipboard with feedback
         */
        function copyToClipboard(text, message) {
            navigator.clipboard.writeText(text).then(() => {
                if(typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: message,
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    alert(message);
                }
            }).catch(() => {
                alert(message);
            });
        }

        /**
         * Alert for session notifications
         */
        @if(session('success'))
            if(typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}" });
            }
        @endif
        @if(session('error'))
            if(typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error!', text: "{{ session('error') }}" });
            }
        @endif
    </script>
    @endpush
</x-app-layout>