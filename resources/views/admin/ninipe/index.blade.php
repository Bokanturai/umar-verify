<x-app-layout>
 <title>Smart Idea - NIN - IPE</title>
      <div class="page-body">
    <div class="container-fluid">
      <div class="page-title">
        <div class="row">
          <div class="col-sm-6 col-12">
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.1s;">
            <div class="financial-card shadow-sm h-100 p-4" style="background: var(--primary-gradient);">
                <div class="d-flex justify-content-between align-items-start position-relative z-1">
                    <div>
                        <p class="stats-label mb-1" style="color: white;">Pending</p>
                        <h3 class="stats-value mb-0">{{ $statusCounts['pending'] ?? 0 }}</h3>
                        <small class="text-white-50 fs-12 fw-medium">Work on this request, it's Urgent!</small>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                        <i class="ti ti-hourglass-empty fs-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.2s;">
            <div class="financial-card shadow-sm h-100 p-4" style="background: var(--info-gradient);">
                <div class="d-flex justify-content-between align-items-start position-relative z-1">
                    <div>
                        <p class="stats-label mb-1" style="color: white;">Processing</p>
                        <h3 class="stats-value mb-0">{{ $statusCounts['processing'] ?? 0 }}</h3>
                        <small class="text-white-50 fs-12 fw-medium">Check and confirm the status</small>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                        <i class="ti ti-settings fs-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.3s;">
            <div class="financial-card shadow-sm h-100 p-4" style="background: var(--success-gradient);">
                <div class="d-flex justify-content-between align-items-start position-relative z-1">
                    <div>
                        <p class="stats-label mb-1" style="color: white;">Resolved</p>
                        <h3 class="stats-value mb-0">{{ $statusCounts['resolved'] ?? 0 }}</h3>
                        <small class="text-white-50 fs-12 fw-medium">You have done a great job</small>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                        <i class="ti ti-circle-check fs-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.4s;">
            <div class="financial-card shadow-sm h-100 p-4" style="background: var(--danger-gradient);">
                <div class="d-flex justify-content-between align-items-start position-relative z-1">
                    <div>
                        <p class="stats-label mb-1" style="color: white;">Rejected</p>
                        <h3 class="stats-value mb-0">{{ $statusCounts['rejected'] ?? 0 }}</h3>
                        <small class="text-white-50 fs-12 fw-medium">Don’t give up — Keep accepting requests</small>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                        <i class="ti ti-circle-x fs-24 text-white"></i>
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
            border-radius: 1rem;
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
        
        .stats-label { font-size: 0.875rem; font-weight: 500; opacity: 0.9; }
        .stats-value { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .avatar-lg { width: 3rem; height: 3rem; display: flex; align-items: center; justify-content: center; }
    </style>


    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 1.25rem;">
                <div class="card-body p-4 bg-white">
                    <form method="GET" action="{{ route('admin.ninipe.index') }}">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="ti ti-search fs-18"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 bg-light py-2" 
                                           placeholder="Search Name, NIN, BVN, Tracking ID..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="ti ti-building-bank fs-18"></i></span>
                                    <select name="bank" class="form-select border-start-0 bg-light py-2" onchange="this.form.submit()">
                                        <option value="">All Banks</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank }}" {{ request('bank') == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="ti ti-chart-dots fs-18"></i></span>
                                    <select name="status" class="form-select border-start-0 bg-light py-2" onchange="this.form.submit()">
                                        <option value="">All Statuses</option>
                                        @foreach(['pending', 'processing', 'in-progress', 'resolved', 'successful', 'rejected', 'failed', 'query', 'remark'] as $s)
                                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center justify-content-center flex-grow-1">
                                        <i class="ti ti-filter me-2 fs-18"></i> Filter Results
                                    </button>
                                    @if(request('status') || request('search') || request('bank'))
                                        <a href="{{ route('admin.ninipe.index') }}" class="btn btn-outline-danger px-4 py-2 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-refresh me-2 fs-18"></i> Clear
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 1.25rem;">
        <div class="card-header bg-white py-4 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="ti ti-list-details me-2 text-primary fs-22"></i>
                NIN IPE Requests
            </h5>
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small">
                    Total: {{ $enrollments->total() }} records
                </div>
                <button type="button" id="checkAllStatusBtn" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2 px-3 py-2" onclick="checkAllStatuses()">
                    <i class="ti ti-refresh fs-16"></i>
                    <span class="fw-semibold">Check Status for All</span>
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- Errors & Success Messages --}}
            <div class="px-4">
                @if (session('errorMessage'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-circle fs-20 me-2"></i>
                            <div><strong>Error!</strong> {{ session('errorMessage') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('successMessage'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-circle-check fs-20 me-2"></i>
                            <div><strong>Success!</strong> {{ session('successMessage') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Tracking ID</th>
                            <th class="py-3">Agent Details</th>
                            <th class="py-3">Service Type</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3">Date Created</th>
                            <th class="pe-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            <tr>
                                <td class="ps-4">
                                    <span class="text-muted fw-medium">#{{ $loop->iteration + ($enrollments->currentPage() - 1) * $enrollments->perPage() }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                            <i class="ti ti-fingerprint fs-16"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $enrollment->tracking_id }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $enrollment->performed_by }}</span>
                                        <small class="text-muted">{{ $enrollment->user_email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                        {{ $enrollment->bank ?? $enrollment->service_type }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusConfig = match($enrollment->status) {
                                            'pending' => ['color' => 'warning', 'icon' => 'ti-hourglass-low'],
                                            'processing' => ['color' => 'info', 'icon' => 'ti-loader-2'],
                                            'in-progress' => ['color' => 'primary', 'icon' => 'ti-progress'],
                                            'resolved', 'successful' => ['color' => 'success', 'icon' => 'ti-circle-check'],
                                            'rejected', 'failed' => ['color' => 'danger', 'icon' => 'ti-circle-x'],
                                            'query' => ['color' => 'warning', 'icon' => 'ti-help-circle'],
                                            'remark' => ['color' => 'secondary', 'icon' => 'ti-message-dots'],
                                            default => ['color' => 'secondary', 'icon' => 'ti-dots']
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusConfig['color'] }}-subtle text-{{ $statusConfig['color'] }} px-3 py-2 rounded-pill fw-bold">
                                        <i class="ti {{ $statusConfig['icon'] }} me-1"></i>
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-medium small">{{ \Carbon\Carbon::parse($enrollment->submission_date)->format('M j, Y') }}</span>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($enrollment->submission_date)->format('g:i A') }}</small>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button type="button" class="btn btn-icon btn-light btn-sm rounded-circle shadow-sm check-status-btn"
                                            data-id="{{ $enrollment->id }}"
                                            data-url="{{ route('admin.ninipe.check', $enrollment->id) }}"
                                            data-bs-toggle="tooltip" title="Check Status">
                                            <i class="ti ti-refresh text-info"></i>
                                        </button>
                                        <a href="{{ route('admin.ninipe.show', $enrollment->id) }}" class="btn btn-icon btn-light btn-sm rounded-circle shadow-sm" data-bs-toggle="tooltip" title="View Details">
                                            <i class="ti ti-eye text-primary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="ti ti-database-off fs-40 text-muted mb-3 d-block"></i>
                                        <h6 class="text-muted fw-normal">No enrollment records found.</h6>
                                        @if(request('search') || request('status'))
                                            <a href="{{ route('admin.ninipe.index') }}" class="btn btn-link btn-sm mt-2">Clear all filters</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($enrollments->hasPages())
            <div class="card-footer bg-white py-3 border-top-0">
                {{ $enrollments->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>

<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-icon {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }
    .badge {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .form-select, .form-control {
        border-color: #f1f3f4;
    }
    .form-select:focus, .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
    }
</style>

{{-- Batch Check Status Progress Modal --}}
<div class="modal fade" id="checkAllModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="ti ti-refresh me-2 text-primary"></i>Checking All Statuses
                </h5>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted small mb-3" id="checkProgressText">Initialising...</p>
                <div class="progress mb-3" style="height: 8px; border-radius: 99px;">
                    <div id="checkProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%;"></div>
                </div>
                <div class="d-flex gap-3 mb-2">
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-12 fw-semibold">
                        <i class="ti ti-circle-check me-1"></i><span id="successCount">0</span> Updated
                    </span>
                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fs-12 fw-semibold">
                        <i class="ti ti-circle-x me-1"></i><span id="failCount">0</span> Failed
                    </span>
                </div>
                <div id="checkResultLog" class="mt-3 bg-light rounded p-3" style="max-height: 200px; overflow-y: auto; font-size: 12px; font-family: monospace; display: none;"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" id="closeCheckModal" data-bs-dismiss="modal" style="display:none;">Close & Refresh</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Per-row single check
    document.querySelectorAll('.check-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const icon = this.querySelector('i');
            icon.classList.add('spin-icon');
            this.disabled = true;

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    icon.classList.remove('spin-icon');
                    this.disabled = false;
                    if (data.success) {
                        showToast('Status updated: ' + (data.data?.status ?? ''), 'success');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showToast(data.message ?? 'No update.', 'warning');
                    }
                })
                .catch(() => {
                    icon.classList.remove('spin-icon');
                    this.disabled = false;
                    showToast('Request failed.', 'danger');
                });
        });
    });

    // Init tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
})();

async function checkAllStatuses() {
    const buttons = Array.from(document.querySelectorAll('.check-status-btn'));
    if (!buttons.length) return;

    const modal = new bootstrap.Modal(document.getElementById('checkAllModal'));
    modal.show();

    const progressBar = document.getElementById('checkProgressBar');
    const progressText = document.getElementById('checkProgressText');
    const successCount = document.getElementById('successCount');
    const failCount = document.getElementById('failCount');
    const logBox = document.getElementById('checkResultLog');
    const closeBtn = document.getElementById('closeCheckModal');
    const mainBtn = document.getElementById('checkAllStatusBtn');

    logBox.style.display = 'block';
    logBox.innerHTML = '';
    successCount.textContent = '0';
    failCount.textContent = '0';
    mainBtn.disabled = true;

    let done = 0, success = 0, failed = 0;
    const total = buttons.length;

    for (const btn of buttons) {
        const url = btn.dataset.url;
        progressText.textContent = `Checking ${done + 1} of ${total}...`;
        progressBar.style.width = Math.round((done / total) * 100) + '%';

        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.success) {
                success++;
                successCount.textContent = success;
                logBox.innerHTML += `<div class="text-success">&#10004; #${btn.dataset.id}: ${data.data?.status ?? 'updated'}</div>`;
            } else {
                failed++;
                failCount.textContent = failed;
                logBox.innerHTML += `<div class="text-danger">&#10008; #${btn.dataset.id}: ${data.message ?? 'no update'}</div>`;
            }
        } catch (e) {
            failed++;
            failCount.textContent = failed;
            logBox.innerHTML += `<div class="text-danger">&#10008; #${btn.dataset.id}: request error</div>`;
        }

        logBox.scrollTop = logBox.scrollHeight;
        done++;
        await new Promise(r => setTimeout(r, 300));
    }

    progressBar.style.width = '100%';
    progressBar.classList.remove('progress-bar-animated');
    progressText.textContent = `Done! ${success} updated, ${failed} failed out of ${total} records.`;
    closeBtn.style.display = 'inline-block';
    mainBtn.disabled = false;

    closeBtn.addEventListener('click', () => location.reload(), { once: true });
}

function showToast(msg, type) {
    const t = document.createElement('div');
    t.className = `alert alert-${type} alert-dismissible fade show position-fixed shadow`;
    t.style.cssText = 'bottom:20px;right:20px;z-index:9999;min-width:280px;';
    t.innerHTML = msg + `<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}
</script>

<style>
.spin-icon { animation: spin 0.8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

</x-app-layout>
