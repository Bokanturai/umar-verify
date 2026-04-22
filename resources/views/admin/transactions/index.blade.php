<x-app-layout>
    <title>Smart Idea - Transactions</title>

    <div class="content">
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.1s;">
                <div class="financial-card shadow-sm h-100 p-4 rounded-0 rounded-md-4" style="background: var(--primary-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Total Transactions</p>
                            <h3 class="stats-value mb-0">{{ number_format($totalTransactions) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Volume of Activity</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-receipt fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.2s;">
                <div class="financial-card shadow-sm h-100 p-4 rounded-0 rounded-md-4" style="background: var(--success-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Total Credits</p>
                            <h3 class="stats-value mb-0">₦{{ number_format($totalCredits, 2) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Wallet Inflow</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-cash-banknote fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.3s;">
                <div class="financial-card shadow-sm h-100 p-4 rounded-0 rounded-md-4" style="background: var(--danger-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Total Debits</p>
                            <h3 class="stats-value mb-0">₦{{ number_format($totalDebits, 2) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Wallet Outflow</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-wallet fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.4s;">
                <div class="financial-card shadow-sm h-100 p-4 rounded-0 rounded-md-4" style="background: var(--info-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Success Rate</p>
                            <h3 class="stats-value mb-0">{{ number_format($successfulTransactions) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Completed Trades</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-checklist fs-24 text-white"></i>
                        </div>
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
            }

            .financial-card {
                position: relative;
                overflow: hidden;
                border: none;
                /* border-radius: 1rem; handled by bootstrap classes */
                color: white;
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

            .stats-label {
                font-size: 0.875rem;
                font-weight: 500;
                opacity: 0.9;
            }

            .stats-value {
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: -0.025em;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .fade-in-up {
                animation: fadeIn 0.5s ease-out forwards;
            }

            .avatar-lg {
                width: 3rem;
                height: 3rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .badge-soft-success {
                background-color: rgba(34, 197, 94, 0.15);
                color: #22c55e;
            }

            .badge-soft-danger {
                background-color: rgba(239, 68, 68, 0.15);
                color: #ef4444;
            }

            .badge-soft-warning {
                background-color: rgba(245, 158, 11, 0.15);
                color: #f59e0b;
            }

            .badge-soft-info {
                background-color: rgba(59, 130, 246, 0.15);
                color: #3b82f6;
            }
        </style>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-0 rounded-md-4">
                    <div class="card-body p-3">
                        <form action="{{ route('transactions') }}" method="GET">
                            <div class="row g-3 align-items-center">
                                <div class="col-lg-3">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="ti ti-list"></i></span>
                                        <select name="type" class="form-select border-start-0 bg-light"
                                            onchange="this.form.submit()">
                                            <option value="">All Types</option>
                                            <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>
                                                Credit</option>
                                            <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="ti ti-settings"></i></span>
                                        <select name="service_type" class="form-select border-start-0 bg-light"
                                            onchange="this.form.submit()">
                                            <option value="">All Services</option>
                                            @foreach(['Airtime', 'Data', 'Electricity', 'Cable', 'Education', 'Funding', 'VNIN_TO_NIBSS', 'BVN_SEARCH', 'BVN_MODIFICATION', 'CRM', 'BVN_USER', 'APPROVAL_REQUEST', 'AFFIDAVIT', 'NIN_SELFSERVICE', 'NIN_VALIDATION', 'IPE', 'NIN_MODIFICATION', 'TIN_INDIVIDUAL', 'TIN_CORPORATE', 'CAC'] as $service)
                                                <option value="{{ $service }}" {{ request('service_type') == $service ? 'selected' : '' }}>{{ str_replace('_', ' ', $service) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="ti ti-calendar"></i></span>
                                        <input type="date" name="date_from" class="form-control border-start-0 bg-light"
                                            value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="ti ti-calendar-event"></i></span>
                                        <input type="date" name="date_to" class="form-control border-start-0 bg-light"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="ti ti-filter me-1"></i>Apply Filters
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table Card -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm rounded-0 rounded-md-4">
                    <div
                        class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center rounded-0 rounded-top-md-4">
                        <h5 class="card-title mb-0 fw-bold">Recent Transactions</h5>
                        <div>
                            <span class="badge bg-light text-dark border me-2">Displaying:
                                {{ $transactions->count() }}</span>
                            <span class="badge bg-soft-primary text-primary">Smart Link</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">S/N</th>
                                        <th>Date & Time</th>
                                        <th>Reference</th>
                                        <th>Performed By</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $index => $transaction)
                                        <tr>
                                            <td class="ps-4">{{ $transactions->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="text-dark fw-medium fs-13">{{ $transaction->created_at->format('d M Y') }}</span>
                                                    <small
                                                        class="text-muted fs-12">{{ $transaction->created_at->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="font-monospace text-primary fs-12">{{ $transaction->transaction_ref }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar avatar-xs bg-soft-info text-info rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-user fs-12"></i>
                                                    </div>
                                                    <span class="text-dark fw-medium fs-13">
                                                        @if($transaction->performer)
                                                            {{ $transaction->performer->fullname }}
                                                        @else
                                                        {{ $transaction->performed_by ?? 'Auto' }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $typeClass = match ($transaction->type) {
                                                        'credit' => 'badge-soft-success',
                                                        'debit' => 'badge-soft-danger',
                                                        default => 'badge-soft-info'
                                                    };
                                                    $typeIcon = match ($transaction->type) {
                                                        'credit' => 'ti-arrow-down-left',
                                                        'debit' => 'ti-arrow-up-right',
                                                        default => 'ti-refresh'
                                                    };
                                                @endphp
                                                <span class="badge {{ $typeClass }} rounded-pill px-2 py-1">
                                                    <i class="ti {{ $typeIcon }} me-1"></i>{{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td
                                                class="text-end fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type == 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $statusClass = match ($transaction->status) {
                                                        'completed', 'successful' => 'badge-soft-success',
                                                        'failed' => 'badge-soft-danger',
                                                        'pending' => 'badge-soft-warning',
                                                        default => 'badge-soft-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }} rounded-pill px-2 py-1">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button type="button"
                                                    class="btn btn-xs btn-outline-primary border-0 rounded-circle"
                                                    data-bs-toggle="modal" data-bs-target="#txModal{{ $transaction->id }}">
                                                    <i class="ti ti-eye fs-14"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="ti ti-receipt-off fs-1 opacity-50"></i>
                                                    <p class="mt-2 mb-0 fw-bold">No transactions found</p>
                                                    <p class="small">Try adjusting your filters to find what you're looking
                                                        for.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Showing {{ $transactions->firstItem() ?? 0 }} to
                                    {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} entries
                                </div>
                                {{ $transactions->withQueryString()->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modals -->
    @foreach ($transactions as $transaction)
        <div class="modal fade" id="txModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-0 rounded-md-4">
                    <div class="modal-header bg-primary text-white rounded-0 rounded-top-md-4">
                        <h5 class="modal-title fw-bold text-white"><i class="ti ti-info-circle me-2"></i>Transaction Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            @php
                                $iconClass = $transaction->type == 'credit' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';
                                $icon = $transaction->type == 'credit' ? 'ti-arrow-down-left' : 'ti-arrow-up-right';
                                $statusClass = match ($transaction->status) {
                                    'completed', 'successful' => 'badge-soft-success',
                                    'failed' => 'badge-soft-danger',
                                    'pending' => 'badge-soft-warning',
                                    default => 'badge-soft-secondary'
                                };
                            @endphp
                            <div class="avatar avatar-xl {{ $iconClass }} rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="ti {{ $icon }} fs-1"></i>
                            </div>
                            <h2 class="fw-bold mb-1 {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type == 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                            </h2>
                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 fs-12">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>

                        <div class="bg-light rounded-3 p-3">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Reference</label>
                                    <span
                                        class="text-dark fw-medium font-monospace fs-12">{{ $transaction->transaction_ref }}</span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Date</label>
                                    <span
                                        class="text-dark fw-medium fs-13">{{ $transaction->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Service Type</label>
                                    <span
                                        class="text-dark fw-medium fs-13">{{ str_replace('_', ' ', $transaction->service_type ?? 'N/A') }}</span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">User (Owner)</label>
                                    <span class="text-dark fw-medium fs-13">{{ $transaction->user->fullname ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Performed By</label>
                                    <span class="text-dark fw-medium fs-13">{{ $transaction->performer->fullname ?? 'System' }}</span>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Description</label>
                                    <span class="text-dark fw-medium fs-13">{{ $transaction->description }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary px-4" onclick="window.print()">
                            <i class="ti ti-printer me-1"></i>Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        // Optional: Add smooth closing behavior
        document.addEventListener('DOMContentLoaded', function () {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function (modal) {
                modal.addEventListener('hide.bs.modal', function () {
                    // Reset any transform/opacity if needed, though Bootstrap handles this mostly.
                    // This is just to match the user's previous request for smooth closing.
                });
            });
        });
    </script>
</x-app-layout>