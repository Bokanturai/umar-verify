<x-app-layout>
    <title>Smart Idea - Manage {{ $serviceName }} Variations</title>

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
                            <h3 class="page-title text-primary mb-1 fw-bold">{{ $serviceName }} Variations</h3>
                            <ul class="breadcrumb bg-transparent p-0 mb-0">
                                <li class="breadcrumb-item text-muted">Data Variations</li>
                                <li class="breadcrumb-item active text-primary">{{ $serviceName }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal"
                        data-bs-target="#addVariationModal">
                        <i class="ti ti-plus me-1"></i> Add Variation
                    </button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0 fw-bold">Variations List</h5>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">S/N</th>
                                <th>Name</th>
                                <th>Variation Code</th>
                                <th>Amount</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($variations as $variation)
                                <tr>
                                    <td class="ps-4 fw-medium text-muted">{{ $variations->firstItem() + $loop->index }}</td>
                                    <td class="fw-bold text-dark">{{ $variation->name }}</td>
                                    <td><code class="text-primary">{{ $variation->variation_code }}</code></td>
                                    <td class="fw-bold">₦{{ number_format($variation->variation_amount, 2) }}</td>
                                    <td class="text-muted">₦{{ number_format($variation->convinience_fee, 2) }}</td>
                                    <td>
                                        @if($variation->status === 'enabled')
                                            <span
                                                class="badge bg-soft-success text-success border border-success-subtle rounded-pill px-3">Enabled</span>
                                        @else
                                            <span
                                                class="badge bg-soft-danger text-danger border border-danger-subtle rounded-pill px-3">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-icon btn-sm btn-soft-info rounded-circle me-1"
                                            data-bs-toggle="modal" data-bs-target="#editVariationModal{{ $variation->id }}"
                                            title="Edit Variation">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-sm btn-soft-danger rounded-circle"
                                            onclick="confirmDelete('{{ $variation->id }}', '{{ $variation->name }}')"
                                            title="Delete Variation">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $variation->id }}"
                                            action="{{ route('admin.data-variations.destroy', $variation) }}" method="POST"
                                            class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ti ti-layers-intersect fs-15 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No variations found for this service.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($variations->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $variations->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Add Variation Modal -->
    <div class="modal fade" id="addVariationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Add Variation for {{ $serviceName }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.data-variations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $serviceId }}">
                    <input type="hidden" name="service_name" value="{{ $serviceName }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., MTN 1GB Monthly"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Variation Code (from API)</label>
                            <input type="text" name="variation_code" class="form-control" placeholder="e.g., mtn-1gb"
                                required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Variation Amount (₦)</label>
                                <input type="number" step="0.01" name="variation_amount" class="form-control"
                                    placeholder="0.00" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Convenience Fee (₦)</label>
                                <input type="number" step="0.01" name="convinience_fee" class="form-control"
                                    value="0.00">
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-4 mb-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" checked>
                                <label class="form-check-label fw-medium" for="statusSwitch">Active Status</label>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="fixedPrice" id="fixedPriceSwitch">
                                <label class="form-check-label fw-medium" for="fixedPriceSwitch">Fixed Price</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Create Variation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Variation Modals -->
    @foreach($variations as $variation)
        <div class="modal fade" id="editVariationModal{{ $variation->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Edit Variation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.data-variations.update', $variation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="service_id" value="{{ $serviceId }}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Plan Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $variation->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Variation Code</label>
                                <input type="text" name="variation_code" class="form-control"
                                    value="{{ $variation->variation_code }}" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Variation Amount (₦)</label>
                                    <input type="number" step="0.01" name="variation_amount" class="form-control"
                                        value="{{ $variation->variation_amount }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Convenience Fee (₦)</label>
                                    <input type="number" step="0.01" name="convinience_fee" class="form-control"
                                        value="{{ $variation->convinience_fee }}">
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-4 mb-3">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="status"
                                        id="statusEdit{{ $variation->id }}" {{ $variation->status === 'enabled' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="statusEdit{{ $variation->id }}">Active
                                        Status</label>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="fixedPrice"
                                        id="fixedPriceEdit{{ $variation->id }}" {{ $variation->fixedPrice === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="fixedPriceEdit{{ $variation->id }}">Fixed
                                        Price</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Update Variation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @push('scripts')
        <script>
            // Display Session Messages
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

            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Delete Variation?',
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