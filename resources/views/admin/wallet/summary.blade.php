<x-app-layout>
    <x-slot name="header">
        System Wallet & Transaction Summary
    </x-slot>

    <div class="row">
        <!-- Top Stats Cards -->
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card flex-fill stats-card zoom-in">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="stats-info">
                            <h6 class="text-muted mb-2">Total System Balance</h6>
                            <h4 class="mb-0">₦{{ number_format($systemStats['total_balance'], 2) }}</h4>
                        </div>
                        <div class="avatar avatar-lg bg-primary-transparent">
                            <i class="ti ti-wallet text-primary fs-24"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-secondary">Hold Amount: ₦{{ number_format($systemStats['total_hold'], 2) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card flex-fill stats-card zoom-in">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="stats-info">
                            <h6 class="text-muted mb-2">Total Registered Users</h6>
                            <h4 class="mb-0">{{ number_format($systemStats['total_users']) }}</h4>
                        </div>
                        <div class="avatar avatar-lg bg-info-transparent">
                            <i class="ti ti-users-group text-info fs-24"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-secondary">Total Wallets: {{ number_format($systemStats['total_wallets']) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card flex-fill stats-card zoom-in">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="stats-info">
                            <h6 class="text-muted mb-2">Total Transactions</h6>
                            <h4 class="mb-0">{{ number_format($transactionSummary->total_count) }}</h4>
                        </div>
                        <div class="avatar avatar-lg bg-warning-transparent">
                            <i class="ti ti-history text-warning fs-24"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success-transparent text-success">{{ number_format($transactionSummary->success_count) }} Success</span>
                        <span class="badge bg-danger-transparent text-danger">{{ number_format($transactionSummary->failed_count) }} Failed</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card flex-fill stats-card zoom-in">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="stats-info">
                            <h6 class="text-muted mb-2">Total Volume (Revenue)</h6>
                            <h4 class="mb-0">₦{{ number_format($transactionSummary->total_volume, 2) }}</h4>
                        </div>
                        <div class="avatar avatar-lg bg-success-transparent">
                            <i class="ti ti-currency-naira text-success fs-24"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-secondary text-truncate d-block">System-wide transaction volume</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Most Used Services -->
        <div class="col-xl-6 col-lg-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Most Used Services</h5>
                    <i class="ti ti-chart-bar text-muted"></i>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Service Type</th>
                                    <th class="text-center">Usage Count</th>
                                    <th class="text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mostUsedServices as $service)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-xs bg-primary-transparent me-2">
                                                    <i class="ti ti-settings fs-14"></i>
                                                </span>
                                                <span class="fw-semibold text-capitalize text-dark">{{ str_replace('_', ' ', $service->service_type) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-transparent text-secondary px-3 py-2 fs-12">
                                                {{ number_format($service->usage_count) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            ₦{{ number_format($service->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No service data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Users by Balance -->
        <div class="col-xl-6 col-lg-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Top 10 High Balance Users</h5>
                    <i class="ti ti-medal text-warning"></i>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Phone / Email</th>
                                    <th class="text-end">Wallet Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topBalanceUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2 bg-dark-transparent font-bold">
                                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                                </div>
                                                <div class="line-height-1">
                                                    <h6 class="mb-0 fs-13">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                                    <small class="text-muted fs-11">UID: #{{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="line-height-1">
                                                <div class="fs-12 text-secondary">{{ $user->phone_no }}</div>
                                                <div class="fs-11 text-muted">{{ $user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            ₦{{ number_format($user->balance, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Service Heavy Users -->
        <div class="col-xl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Service Power Users (Top 10 by Transaction Count)</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Overall
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>User</th>
                                    <th>Contact Information</th>
                                    <th class="text-center">Txn Count</th>
                                    <th class="text-end">Total Volume Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($serviceHeavyUsers as $index => $heavy)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $index < 3 ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-2" style="width: 25px; height: 25px; display: inline-flex; align-items: center; justify-content: center;">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs me-2 bg-primary">
                                                    {{ strtoupper(substr($heavy->user->first_name ?? 'U', 0, 1)) }}
                                                </div>
                                                <h6 class="mb-0">{{ $heavy->user->first_name ?? 'Unknown' }} {{ $heavy->user->last_name ?? '' }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="d-block">{{ $heavy->user->email ?? 'N/A' }}</small>
                                            <small class="text-muted">{{ $heavy->user->phone_no ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="px-3 py-1 bg-info-transparent text-info rounded-pill fs-12 fw-bold">
                                                {{ number_format($heavy->transaction_count) }} Txns
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            ₦{{ number_format($heavy->total_spent, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No usage data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent System Transactions -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Recent System Transactions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Ref #</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $txn)
                            <tr>
                                <td><small class="text-muted">{{ $txn->transaction_ref }}</small></td>
                                <td>{{ $txn->user->first_name ?? 'System' }} {{ $txn->user->last_name ?? 'System' }}</td>
                                <td><span class="text-capitalize">{{ str_replace('_', ' ', $txn->service_type ?? $txn->type) }}</span></td>
                                <td>₦{{ number_format($txn->amount, 2) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($txn->status) {
                                            'completed', 'success' => 'success',
                                            'failed' => 'danger',
                                            'pending' => 'warning',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}-transparent text-{{ $statusClass }}">{{ ucfirst($txn->status) }}</span>
                                </td>
                                <td><small>{{ $txn->created_at->format('M d, H:i') }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .stats-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .bg-primary-transparent { background-color: rgba(15, 118, 110, 0.1) !important; }
        .bg-info-transparent { background-color: rgba(0, 184, 216, 0.1) !important; }
        .bg-warning-transparent { background-color: rgba(255, 188, 33, 0.1) !important; }
        .bg-success-transparent { background-color: rgba(26, 188, 156, 0.1) !important; }
        .bg-danger-transparent { background-color: rgba(231, 76, 60, 0.1) !important; }
        .text-primary { color: #0F766E !important; }
        .avatar-lg i { font-size: 24px; }
        .avatar { display: inline-flex; align-items: center; justify-content: center; }
        .line-height-1 { line-height: 1.2; }
    </style>
    @endpush
</x-app-layout>
