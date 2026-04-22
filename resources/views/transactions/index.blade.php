<x-app-layout>
    <title>Smart Idea - Transactions</title>

    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <!-- Page Header -->
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <h3 class="fw-bold text-primary">Transaction History</h3>
                        <p class="text-muted small mb-0">
                            View and filter your wallet transactions and service history.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-0 g-md-4">
                <!-- Transaction History -->
                <div class="col-12 col-xl-12 mb-4">
                    <div class="card shadow-sm border-0 rounded-0 rounded-md-4 h-100">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-0 rounded-top-md-4">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-receipt me-2"></i>Transactions</h5>
                            <span class="badge bg-light text-primary fw-semibold">Smart Idea</span>
                        </div>
                        <div class="card-body">

                            <!-- Filter Form -->
                            <form class="row g-3 mb-4" method="GET" action="{{ route('transactions') }}">
                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-bold text-muted">Transaction Type</label>
                                    <select class="form-select" name="type">
                                        <option value="">All Types</option>
                                        <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                                        <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                                        <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-bold text-muted">Service Type</label>
                                    <select class="form-select" name="service_type">
                                        <option value="">All Services</option>
                                        <option value="Airtime" {{ request('service_type') == 'Airtime' ? 'selected' : '' }}>Airtime</option>
                                        <option value="Data" {{ request('service_type') == 'Data' ? 'selected' : '' }}>Data</option>
                                        <option value="Electricity" {{ request('service_type') == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                                        <option value="Cable" {{ request('service_type') == 'Cable' ? 'selected' : '' }}>Cable TV</option>
                                        <option value="Education" {{ request('service_type') == 'Education' ? 'selected' : '' }}>Education (WAEC/NECO/JAMB)</option>
                                        <option value="Funding" {{ request('service_type') == 'Funding' ? 'selected' : '' }}>Wallet Funding</option>
                                        <option value="VNIN_TO_NIBSS" {{ request('service_type') == 'VNIN_TO_NIBSS' ? 'selected' : '' }}>VNIN TO NIBSS</option>
                                        <option value="BVN_SEARCH" {{ request('service_type') == 'BVN_SEARCH' ? 'selected' : '' }}>BVN Search</option>
                                        <option value="BVN_MODIFICATION" {{ request('service_type') == 'BVN_MODIFICATION' ? 'selected' : '' }}>BVN Modification</option>
                                        <option value="CRM" {{ request('service_type') == 'CRM' ? 'selected' : '' }}>CRM</option>
                                        <option value="BVN_USER" {{ request('service_type') == 'BVN_USER' ? 'selected' : '' }}>BVN User</option>
                                        <option value="APPROVAL_REQUEST" {{ request('service_type') == 'APPROVAL_REQUEST' ? 'selected' : '' }}>Approval Request</option>
                                        <option value="AFFIDAVIT" {{ request('service_type') == 'AFFIDAVIT' ? 'selected' : '' }}>Affidavit</option>
                                        <option value="NIN_SELFSERVICE" {{ request('service_type') == 'NIN_SELFSERVICE' ? 'selected' : '' }}>NIN Self Service</option>
                                        <option value="NIN_VALIDATION" {{ request('service_type') == 'NIN_VALIDATION' ? 'selected' : '' }}>NIN Validation</option>
                                        <option value="IPE" {{ request('service_type') == 'IPE' ? 'selected' : '' }}>IPE</option>
                                        <option value="NIN_MODIFICATION" {{ request('service_type') == 'NIN_MODIFICATION' ? 'selected' : '' }}>NIN Modification</option>
                                        <option value="TIN_INDIVIDUAL" {{ request('service_type') == 'TIN_INDIVIDUAL' ? 'selected' : '' }}>TIN Individual</option>
                                        <option value="TIN_CORPORATE" {{ request('service_type') == 'TIN_CORPORATE' ? 'selected' : '' }}>TIN Corporate</option>
                                        <option value="CAC" {{ request('service_type') == 'CAC' ? 'selected' : '' }}>CAC</option>
                                        <option value="not_selected" {{ request('service_type') == 'not_selected' ? 'selected' : '' }}>Not Selected</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small fw-bold text-muted">From Date</label>
                                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label small fw-bold text-muted">To Date</label>
                                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-12 col-md-2 d-flex align-items-end">
                                    <button class="btn btn-primary w-100 fw-semibold" type="submit">
                                        <i class="bi bi-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </form>

                            <!-- Transactions Table -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="d-none d-md-table-cell">#</th>
                                            <th>Date</th>
                                            <th class="d-none d-lg-table-cell">Reference</th>
                                            <th>Description</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $index => $transaction)
                                            <tr>
                                                <td class="d-none d-md-table-cell">{{ $transactions->firstItem() + $index }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold small">{{ $transaction->created_at->format('d M Y') }}</span>
                                                        <small class="text-muted x-small">{{ $transaction->created_at->format('h:i A') }}</small>
                                                    </div>
                                                </td>
                                                <td class="d-none d-lg-table-cell"><span class="font-monospace small text-primary">{{ Str::limit($transaction->transaction_ref, 10, '...') }}</span></td>
                                                <td>
                                                    <span class="d-inline-block text-truncate-15 cursor-help" 
                                                          style="max-width: 150px; cursor: help;" 
                                                          data-bs-toggle="tooltip"
                                                          title="{{ $transaction->description }}">
                                                        {{ Str::limit($transaction->description, 15, '...') }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($transaction->type == 'credit')
                                                        <span class="badge bg-success-subtle text-success fw-semibold x-small">Credit</span>
                                                    @elseif($transaction->type == 'debit')
                                                        <span class="badge bg-danger-subtle text-danger fw-semibold x-small">Debit</span>
                                                    @else
                                                        <span class="badge bg-info-subtle text-info fw-semibold x-small">{{ ucfirst($transaction->type) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type == 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $transaction->status == 'completed' || $transaction->status == 'successful' ? 'success' : ($transaction->status == 'failed' ? 'danger' : 'warning') }}-subtle text-{{ $transaction->status == 'completed' || $transaction->status == 'successful' ? 'success' : ($transaction->status == 'failed' ? 'danger' : 'warning') }} fw-semibold x-small">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                        data-bs-toggle="modal" data-bs-target="#txModal{{ $transaction->id }}">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="bi bi-inbox text-muted fs-1 mb-3"></i>
                                                        <h6 class="fw-bold text-muted">No transactions found</h6>
                                                        <p class="text-muted small">Try adjusting your filters.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
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
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold text-white"><i class="bi bi-info-circle me-2"></i>Transaction Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start p-4">
                        <div class="mb-4 text-center">
                            <h2 class="fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }} mb-1">
                                ₦{{ number_format($transaction->amount, 2) }}
                            </h2>
                            <span class="badge bg-{{ $transaction->status == 'completed' || $transaction->status == 'successful' ? 'success' : ($transaction->status == 'failed' ? 'danger' : 'warning') }}-subtle text-{{ $transaction->status == 'completed' || $transaction->status == 'successful' ? 'success' : ($transaction->status == 'failed' ? 'danger' : 'warning') }} fw-bold px-3 py-2 rounded-pill">
                                {{ strtoupper($transaction->status) }}
                            </span>
                        </div>
                        <div class="receipt-box bg-light p-3 rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Reference:</span>
                                <span class="fw-bold small font-monospace text-primary">{{ $transaction->transaction_ref }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Service Type:</span>
                                <span class="fw-bold small">{{ str_replace('_', ' ', $transaction->service_type ?? 'N/A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Transaction Type:</span>
                                <span class="fw-bold small text-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">{{ ucfirst($transaction->type) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Date & Time:</span>
                                <span class="fw-bold small">{{ $transaction->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                        <div class="description-box">
                            <label class="text-muted small fw-bold d-block mb-1">Description:</label>
                            <p class="small text-dark fw-medium bg-white p-2 border rounded-3">{{ $transaction->description }}</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-primary rounded-pill px-4" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print Receipt
                        </button>
                        <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <style>
        .x-small { font-size: 0.75rem; }
        .cursor-help { cursor: help; }
        .text-truncate-15 { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        @media (max-width: 576px) {
            .card-header h5 { font-size: 1rem; }
            .table-responsive { border: 0; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
</x-app-layout>
