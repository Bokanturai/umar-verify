<x-app-layout>
    <title>Smart Idea - Wallet Management</title>

    <div class="content">
        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fs-4 me-3"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <!-- Total Manual Credit -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 fade-in-up" style="animation-delay: 0.1s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--primary-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Total Manual Credit</p>
                            <h3 class="stats-value mb-0">₦{{ number_format($monthly_manual_credit, 2) }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-arrow-up-right fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Manual Debit -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 fade-in-up" style="animation-delay: 0.2s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--warning-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Total Manual Debit</p>
                            <h3 class="stats-value mb-0">₦{{ number_format($monthly_manual_debit, 2) }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-arrow-down-left fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Funding -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 fade-in-up" style="animation-delay: 0.3s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--success-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Monthly Funding</p>
                            <h3 class="stats-value mb-0">₦{{ number_format($monthlyFunding ?? 0, 2) }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-arrow-down-circle fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Debit -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 fade-in-up" style="animation-delay: 0.4s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--danger-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Monthly Debit</p>
                            <h3 class="stats-value mb-0">₦{{ number_format($monthlyDebit ?? 0, 2) }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-arrow-up-circle fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.wallet.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-uppercase text-muted">From Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar"></i></span>
                                    <input type="date" name="from_date" class="form-control bg-light border-start-0" value="{{ request('from_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-uppercase text-muted">To Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar"></i></span>
                                    <input type="date" name="to_date" class="form-control bg-light border-start-0" value="{{ request('to_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-uppercase text-muted">Action Type</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-filter"></i></span>
                                    <select name="type" class="form-select bg-light border-start-0">
                                        <option value="">All Types</option>
                                        <option value="manual_credit" {{ request('type') == 'manual_credit' ? 'selected' : '' }}>Manual Credit</option>
                                        <option value="manual_debit" {{ request('type') == 'manual_debit' ? 'selected' : '' }}>Manual Debit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-search me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                                    <i class="ti ti-refresh me-1"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1 fw-bold">Manual Wallet Transactions</h4>
                            <p class="text-muted mb-0 small">Overview of all manual funding and debit operations.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.wallet.fund.view') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="ti ti-plus me-1"></i> Fund/Debit
                            </a>
                            <a href="{{ route('admin.wallet.bulk-fund.view') }}" class="btn btn-outline-primary d-flex align-items-center">
                                <i class="ti ti-users me-1"></i> Bulk Action
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap align-middle mb-0">
                                <thead class="bg-light-gray">
                                    <tr>
                                        <th class="ps-4 text-uppercase fs-11 fw-bold text-muted">S/N</th>
                                        <th class="text-uppercase fs-11 fw-bold text-muted">Reference</th>
                                        <th class="text-uppercase fs-11 fw-bold text-muted">User</th>
                                        <th class="text-uppercase fs-11 fw-bold text-muted">Type</th>
                                        <th class="text-uppercase fs-11 fw-bold text-muted">Amount</th>
                                        <th class="text-uppercase fs-11 fw-bold text-muted">Status</th>
                                        <th class="text-uppercase fs-11 fw-bold text-muted text-center">Date</th>
                                        <th class="text-uppercase fs-11 fw-bold text-muted pe-4">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td class="ps-4 fw-medium text-muted">
                                                {{ $transactions->firstItem() + $loop->index }}
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark fw-medium border">{{ $transaction->transaction_ref }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3">
                                                        {{ strtoupper(substr($transaction->user->first_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fs-14 fw-bold text-dark text-capitalize">
                                                            {{ $transaction->user->first_name ?? 'Unknown' }} {{ $transaction->user->last_name ?? '' }}
                                                        </h6>
                                                        <span class="text-muted fs-12">{{ $transaction->user->email ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($transaction->type == 'manual_credit')
                                                    <span class="badge bg-soft-success text-success px-3 py-2 rounded-pill fw-bold">
                                                        <i class="ti ti-circle-check me-1"></i> Manual Credit
                                                    </span>
                                                @elseif($transaction->type == 'manual_debit')
                                                    <span class="badge bg-soft-danger text-danger px-3 py-2 rounded-pill fw-bold">
                                                        <i class="ti ti-circle-x me-1"></i> Manual Debit
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary px-3 py-2 rounded-pill">
                                                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="fw-bold {{ $transaction->type == 'manual_credit' ? 'text-success' : 'text-danger' }} fs-15">
                                                <span class="me-1">{{ $transaction->type == 'manual_credit' ? '+' : '-' }}</span>₦{{ number_format($transaction->amount, 2) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-success rounded-pill px-3 fw-medium">Completed</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-dark fs-13 fw-medium">{{ $transaction->created_at->format('d M, Y') }}</div>
                                                <div class="text-muted fs-11">{{ $transaction->created_at->format('h:i A') }}</div>
                                            </td>
                                            <td class="pe-4 text-wrap" style="min-width: 200px;">
                                                <span class="text-muted fs-13" data-bs-toggle="tooltip" title="{{ $transaction->description }}">
                                                    {{ \Illuminate\Support\Str::limit($transaction->description, 15) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="avatar avatar-xxl bg-light text-muted rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                                        <i class="ti ti-receipt-off fs-1"></i>
                                                    </div>
                                                    <h5 class="fw-bold">No Transactions Found</h5>
                                                    <p class="text-primary">There are no manual wallet transactions recorded yet.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($transactions->hasPages())
                            <div class="card-footer bg-white border-top py-4 px-4 d-flex justify-content-between align-items-center">
                                <p class="mb-0 text-muted fs-13">Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries</p>
                                {{ $transactions->links('vendor.pagination.custom') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <style>
            :root {
                --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
                --success-gradient: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
                --info-gradient: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
                --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                --danger-gradient: linear-gradient(135deg, #ef4444 0%, #f43f5e 100%);
                --purple-gradient: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
                --orange-gradient: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
                --teal-gradient: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
                --indigo-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            }

            /* Financial Cards */
            .financial-card {
                position: relative;
                overflow: hidden;
                border: none;
                border-radius: 1rem;
                color: white;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .financial-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            }
            .financial-card::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 150px;
                height: 150px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                transform: translate(30%, -30%);
            }
            .financial-card::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100px;
                height: 100px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                transform: translate(-30%, 30%);
            }

            /* Stats Typography */
            .stats-label { font-size: 0.875rem; font-weight: 500; opacity: 0.9; }
            .stats-value { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em; color: white !important; }

            /* Animation */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .fade-in-up {
                animation: fadeIn 0.5s ease-out forwards;
            }
            
            .avatar-xxl { width: 5rem; height: 5rem; }
        </style>
    </div>
</x-app-layout>
