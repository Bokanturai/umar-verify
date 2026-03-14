<x-app-layout>
    <div class="mt-4">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold">Referral Program</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Referral</li>
                    </ul>
                </div>
            </div>
        </div>

        @include('pages.alart')

        <!-- Referral Hero Section -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #1b4332 0%, #081c15 100%);">
            <div class="card-body p-4 p-md-5 text-white">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h2 class="fw-bold mb-3 text-primary">Invite Friends & <span class="text-warning">Earn Rewards</span></h2>
                        <p class="lead mb-4 opacity-75 text-white">Share your referral code. Earn ₦{{ number_format($defaultBonus, 2) }} when your friend joins and makes at least 5 successful transactions!</p>
                        
                        <div class="d-flex align-items-center gap-2 bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-25 mb-4" style="max-width: 400px;">
                            <div class="flex-grow-1">
                                <small class="d-block text-white opacity-50 text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Your Referral Code</small>
                                <h4 class="mb-0 fw-bold font-monospace text-white" id="referralCode">{{ $user->referral_code ?? 'N/A' }}</h4>
                            </div>
                            <button class="btn btn-warning btn-sm rounded-circle p-2" onclick="copyReferralCode()" title="Copy Code">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="https://wa.me/?text=Hi! Join me on Quick Slip using my referral code {{ $user->referral_code }} to get started: {{ url('/register/' . $user->referral_code) }}" target="_blank" class="btn btn-success rounded-pill px-4">
                                <i class="fab fa-whatsapp me-1"></i> Share on WhatsApp
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-center">
                        <div class="bg-white bg-opacity-10 p-4 rounded-circle d-inline-block shadow-lg">
                            <i class="fas fa-users-cog text-white" style="font-size: 80px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Bonus Balance Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                                <i class="fas fa-gift fs-2 text-warning"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-1">Available Bonus</p>
                                <h3 class="fw-bold mb-0 text-dark">₦{{ number_format($wallet->bonus ?? 0, 2) }}</h3>
                            </div>
                        </div>
                        
                        <div class="bg-light p-3 rounded-3 mb-4 border-start border-4 border-warning">
                            <h6 class="fw-bold mb-1 small text-dark"><i class="fas fa-info-circle me-1 text-warning"></i> Claim Milestone</h6>
                            <p class="text-muted mb-0 small" style="line-height: 1.4;">Referrals must complete at least 5 successful transactions before you can claim the bonus.</p>
                        </div>
                        
                        <form action="{{ route('refferal.claim') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 py-2 rounded-3 fw-bold text-white shadow-sm" {{ ($wallet->bonus ?? 0) <= 0 ? 'disabled' : '' }}>
                                <i class="fas fa-coins me-1"></i> Claim Bonus Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 p-4 pb-0">
                        <h5 class="fw-bold mb-0">Referral History</h5>
                    </div>
                    <div class="card-body p-4">
                        @if($referralHistory->isEmpty())
                            <div class="text-center py-5">
                                <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                                    <i class="fas fa-users text-muted fs-30"></i>
                                </div>
                                <h5 class="text-muted">No referrals yet</h5>
                                <p class="text-muted small">Share your code to start earning bonuses!</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light border-0">
                                        <tr>
                                            <th class="border-0">User</th>
                                            <th class="border-0">Progress</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0 text-end">Bonus</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-0">
                                        @foreach($referralHistory as $history)
                                            <tr>
                                                <td class="border-0">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 11px;">
                                                            {{ substr($history->referredUser->first_name ?? 'U', 0, 1) }}{{ substr($history->referredUser->last_name ?? '', 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <span class="d-block fw-semibold text-dark small">{{ $history->referredUser->first_name ?? 'User' }} {{ $history->referredUser->surname ?? '#' . $history->referred_user_id }}</span>
                                                            <small class="text-muted d-block" style="font-size: 10px;">{{ Str::limit($history->referredUser->email ?? '****@***.com', 15) }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="border-0">
                                                    <div class="d-flex flex-column" style="width: 80px;">
                                                        <span class="fw-bold small {{ $history->transaction_count >= 5 ? 'text-success' : 'text-warning' }}">{{ $history->transaction_count }}/5</span>
                                                        <div class="progress mt-1" style="height: 4px;">
                                                            <div class="progress-bar {{ $history->transaction_count >= 5 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ min(($history->transaction_count / 5) * 100, 100) }}%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="border-0">
                                                    @if($history->transaction_count >= 5)
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">Eligible</span>
                                                    @else
                                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 small">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="border-0 text-end">
                                                    <span class="fw-bold text-success small">+₦{{ number_format($history->amount, 2) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- How it works -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">How it Works</h5>
                <div class="row g-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="text-center">
                            <div class="btn btn-warning btn-lg rounded-circle mb-3 p-0 d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fas fa-share-alt text-white"></i>
                            </div>
                            <h6 class="fw-bold mb-1 small">Share Code</h6>
                            <p class="text-muted small mb-0">Share your unique code with friends.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-center">
                            <div class="btn btn-primary btn-lg rounded-circle mb-3 p-0 d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            <h6 class="fw-bold mb-1 small">Friend Joins</h6>
                            <p class="text-muted small mb-0">Friend uses code during signup.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-center">
                            <div class="btn btn-info btn-lg rounded-circle mb-3 p-0 d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fas fa-exchange-alt text-white"></i>
                            </div>
                            <h6 class="fw-bold mb-1 small">Transactions</h6>
                            <p class="text-muted small mb-0">Friend makes 5 transactions.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-center">
                            <div class="btn btn-success btn-lg rounded-circle mb-3 p-0 d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fas fa-coins text-white"></i>
                            </div>
                            <h6 class="fw-bold mb-1 small">Earn & Claim</h6>
                            <p class="text-muted small mb-0">You receive and claim bonus!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function copyReferralCode() {
        const code = document.getElementById('referralCode').innerText;
        navigator.clipboard.writeText(code).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: 'success',
                title: 'Referral code copied!'
            });
        });
    }
    </script>
    @endpush
</x-app-layout>
