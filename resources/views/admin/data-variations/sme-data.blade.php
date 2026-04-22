<x-app-layout>
    <title>Smart Idea - SME Data Management</title>

    <div class="content">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.data-variations.index') }}"
                            class="btn btn-icon btn-sm btn-light rounded-circle me-3">
                            <i class="ti ti-arrow-left"></i>
                        </a>
                        <div>
                            <h3 class="page-title text-primary mb-1 fw-bold">SME Data Management</h3>
                            <ul class="breadcrumb bg-transparent p-0 mb-0">
                                <li class="breadcrumb-item text-muted">Data Variations</li>
                                <li class="breadcrumb-item active text-primary">SME Data Management</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-flex align-items-center justify-content-md-end gap-2">
                        <button class="btn btn-soft-primary rounded-pill px-3" onclick="confirmSync()">
                            <i class="ti ti-refresh me-1"></i> Sync from API
                        </button>
                        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal"
                            data-bs-target="#addSmeModal">
                            <i class="ti ti-plus me-1"></i> Add Manual Plan
                        </button>
                    </div>
                    <form id="sync-form" action="{{ route('admin.sme-data.sync') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <!-- Network Stats Overview -->
        @if(isset($stats))
            <div class="row g-4 mb-4">
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center h-100">
                        <div class="card-body p-3">
                            <div class="avatar avatar-md bg-soft-warning rounded-circle mx-auto mb-2">
                                <i class="ti ti-layers-intersect text-warning fs-15"></i>
                            </div>
                            <h6 class="text-muted small fw-bold text-uppercase mb-1">MTN</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['mtn']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center h-100">
                        <div class="card-body p-3">
                            <div class="avatar avatar-md bg-soft-danger rounded-circle mx-auto mb-2">
                                <i class="ti ti-layers-intersect text-danger fs-15"></i>
                            </div>
                            <h6 class="text-muted small fw-bold text-uppercase mb-1">Airtel</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['airtel']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center h-100">
                        <div class="card-body p-3">
                            <div class="avatar avatar-md bg-soft-success rounded-circle mx-auto mb-2">
                                <i class="ti ti-layers-intersect text-success fs-15"></i>
                            </div>
                            <h6 class="text-muted small fw-bold text-uppercase mb-1">Glo</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['glo']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center h-100">
                        <div class="card-body p-3">
                            <div class="avatar avatar-md bg-soft-dark rounded-circle mx-auto mb-2">
                                <i class="ti ti-layers-intersect text-dark fs-15"></i>
                            </div>
                            <h6 class="text-muted small fw-bold text-uppercase mb-1">9mobile</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['mobile']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center h-100">
                        <div class="card-body p-3 bg-soft-primary border border-primary-subtle">
                            <div class="avatar avatar-md bg-primary rounded-circle mx-auto mb-2">
                                <i class="ti ti-database text-white fs-15"></i>
                            </div>
                            <h6 class="text-primary small fw-bold text-uppercase mb-1">Total</h6>
                            <h4 class="mb-0 fw-bold text-primary">{{ number_format($stats['total']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Active SME Data Plans</h5>
                <div
                    class="badge bg-soft-info text-info rounded-pill px-3 py-2 border border-info-subtle small fw-medium">
                    <i class="ti ti-info-circle me-1"></i> These plans are synced with your API provider.
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Data ID</th>
                                <th>Network</th>
                                <th>Plan Type</th>
                                <th>Size</th>
                                <th>Amount</th>
                                <th>Validity</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($smeData as $plan)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#{{ $plan->data_id }}</td>
                                    <td class="text-capitalize fw-medium">
                                        @php
                                            $networkName = strtolower($plan->network);
                                            if ($networkName == '9mobile') {
                                                $displayNetwork = '9mobile';
                                            } else {
                                                $displayNetwork = ucfirst($networkName);
                                            }
                                        @endphp
                                        {{ $displayNetwork }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-soft-info text-info border border-info-subtle">{{ $plan->plan_type }}</span>
                                    </td>
                                    <td class="fw-bold">{{ $plan->size }}</td>
                                    <td class="fw-bold">₦{{ number_format($plan->amount, 2) }}</td>
                                    <td class="text-muted small">{{ $plan->validity }}</td>
                                    <td>
                                        @if($plan->status)
                                            <span
                                                class="badge bg-soft-success text-success border border-success-subtle rounded-pill px-3">Available</span>
                                        @else
                                            <span
                                                class="badge bg-soft-danger text-danger border border-danger-subtle rounded-pill px-3">Unavailable</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-icon btn-sm btn-soft-info rounded-circle me-1"
                                            data-bs-toggle="modal" data-bs-target="#editSmeModal{{ $plan->id }}"
                                            title="Edit Plan">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-sm btn-soft-danger rounded-circle"
                                            onclick="confirmDelete('{{ $plan->id }}', '{{ $plan->network }} - {{ $plan->size }}')"
                                            title="Delete Plan">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $plan->id }}"
                                            action="{{ route('admin.sme-data.destroy', $plan->id) }}" method="POST"
                                            class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ti ti-database-off fs-1 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No SME data plans found. Sync from API to populate
                                                your list.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($smeData->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $smeData->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Add Modal Placeholder (Same implementation as Variations) -->
    <div class="modal fade" id="addSmeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Add SME Data Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.sme-data.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Network</label>
                                <select name="network" class="form-select" required>
                                    <option value="MTN">MTN</option>
                                    <option value="AIRTEL">Airtel</option>
                                    <option value="GLO">Glo</option>
                                    <option value="9MOBILE">9mobile</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Data ID (API)</label>
                                <input type="text" name="data_id" class="form-control" required placeholder="e.g. 101">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Plan Type</label>
                                <input type="text" name="plan_type" class="form-control" value="SME" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Size</label>
                                <input type="text" name="size" class="form-control" placeholder="e.g. 1.0GB" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Amount (₦)</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required
                                    placeholder="0.00">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Validity</label>
                                <input type="text" name="validity" class="form-control" value="1 Month" required>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0 bg-light p-3 rounded-4">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="status" id="statusSwitch"
                                checked value="1">
                            <label class="form-check-label fw-medium" for="statusSwitch">Available Status</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Create Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach($smeData as $plan)
        <div class="modal fade" id="editSmeModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Edit SME Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.sme-data.update', $plan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Network</label>
                                    <select name="network" class="form-select" required>
                                        <option value="MTN" {{ $plan->network == 'MTN' ? 'selected' : '' }}>MTN</option>
                                        <option value="AIRTEL" {{ $plan->network == 'AIRTEL' ? 'selected' : '' }}>Airtel
                                        </option>
                                        <option value="GLO" {{ $plan->network == 'GLO' ? 'selected' : '' }}>Glo</option>
                                        <option value="9MOBILE" {{ $plan->network == '9MOBILE' ? 'selected' : '' }}>9mobile
                                        </option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Data ID (API)</label>
                                    <input type="text" name="data_id" class="form-control" value="{{ $plan->data_id }}"
                                        required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Plan Type</label>
                                    <input type="text" name="plan_type" class="form-control" value="{{ $plan->plan_type }}"
                                        required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Size</label>
                                    <input type="text" name="size" class="form-control" value="{{ $plan->size }}" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Amount (₦)</label>
                                    <input type="number" step="0.01" name="amount" class="form-control"
                                        value="{{ $plan->amount }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Validity</label>
                                    <input type="text" name="validity" class="form-control" value="{{ $plan->validity }}"
                                        required>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0 bg-light p-3 rounded-4">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="status"
                                    id="statusEdit{{ $plan->id }}" {{ $plan->status ? 'checked' : '' }} value="1">
                                <label class="form-check-label fw-medium" for="statusEdit{{ $plan->id }}">Available
                                    Status</label>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Update Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @push('scripts')
        <script>
            // Display Session Messages using SweetAlert2
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-4 shadow-sm'
                    }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    customClass: {
                        popup: 'rounded-4 shadow-sm'
                    }
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: '<ul class="text-start mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    customClass: {
                        popup: 'rounded-4 shadow-sm'
                    }
                });
            @endif

            function confirmSync() {
                Swal.fire({
                    title: 'Sync Plans?',
                    text: "Updating your local inventory from the provider's API. Continue?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6366f1',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="ti ti-refresh me-1"></i> Sync Now',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    customClass: {
                        popup: 'rounded-4 shadow-sm',
                        confirmButton: 'rounded-pill px-4',
                        cancelButton: 'rounded-pill px-4'
                    },
                    preConfirm: () => {
                        document.getElementById('sync-form').submit();
                    }
                });
            }

            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Delete SME Plan?',
                    html: `Are you sure you want to delete <strong>${name}</strong>? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4 shadow-sm',
                        confirmButton: 'rounded-pill px-4',
                        cancelButton: 'rounded-pill px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            }
        </script>
    @endpush

    <style>
        .bg-soft-success {
            background-color: rgba(34, 197, 94, 0.1);
        }

        .bg-soft-danger {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .bg-soft-info {
            background-color: rgba(6, 182, 212, 0.1);
        }

        .bg-soft-warning {
            background-color: rgba(245, 158, 11, 0.1);
        }

        .bg-soft-primary {
            background-color: rgba(99, 102, 241, 0.1);
        }

        .bg-soft-dark {
            background-color: rgba(31, 41, 55, 0.1);
        }

        .btn-soft-primary {
            background-color: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }

        .btn-soft-info {
            background-color: rgba(6, 182, 212, 0.1);
            color: #06b6d4;
        }

        .btn-soft-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
    </style>
</x-app-layout>