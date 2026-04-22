<x-app-layout>
    <title>Smart Idea - Data Variations Management</title>

    <div class="content">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h3 class="page-title text-primary mb-1 fw-bold">Data & Service Variations</h3>
                    <ul class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item text-muted">Management</li>
                        <li class="breadcrumb-item active text-primary">Data Variations</li>
                    </ul>
                </div>
                <div class="col-md-8 text-md-end mt-3 mt-md-0">
                    <form action="{{ route('admin.data-variations.sync') }}" method="POST" id="syncForm"
                        class="d-inline">
                        @csrf
                        <button type="button" class="btn btn-soft-primary rounded-pill px-4 border-primary-subtle"
                            onclick="confirmSync()">
                            <i class="ti ti-rotate text-primary me-1"></i> Sync from API
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row g-4 mb-4">
            <div class="col-xl-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small fw-bold text-uppercase">Total Variations</p>
                                <h3 class="mb-0 fw-bold">{{ number_format($totalVariationsCount) }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-soft-primary rounded-circle">
                                <i class="ti ti-layers-intersect text-primary fs-15"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small fw-bold text-uppercase">Active Variations</p>
                                <h3 class="mb-0 fw-bold text-success">{{ number_format($activeVariationsCount) }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-soft-success rounded-circle">
                                <i class="ti ti-cloud-check text-success fs-15"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 small fw-bold text-uppercase">Inactive Variations</p>
                                <h3 class="mb-0 fw-bold text-danger">{{ number_format($inactiveVariationsCount) }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-soft-danger rounded-circle">
                                <i class="ti ti-cloud-off text-danger fs-15"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Grid -->
        <div class="row g-4">
            @foreach($availableServices as $id => $service)
                <div class="col-xxl-3 col-xl-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm transition-all hover-translate-y">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md bg-soft-{{ $service['color'] }} rounded-3 me-3">
                                    <i class="{{ $service['icon'] }} text-{{ $service['color'] }} fs-15"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-0 fw-bold">{{ $service['name'] }}</h5>
                                    <small class="text-muted">{{ $id }}</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded-3 mb-3">
                                <span class="text-muted small">Total Variations:</span>
                                <span class="badge bg-{{ $service['color'] }} rounded-pill px-3">
                                    {{ $serviceCounts[$id] ?? 0 }}
                                </span>
                            </div>

                            <a href="{{ route('admin.data-variations.show', $id) }}"
                                class="btn btn-outline-primary w-100 rounded-pill">
                                <i class="ti ti-settings-automation me-1"></i> Manage Variations
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Styles for Hover and Transitions -->
    <style>
        .transition-all {
            transition: all 0.3s ease;
        }

        .hover-translate-y:hover {
            transform: translateY(-5px);
        }

        .bg-soft-primary {
            background-color: rgba(99, 102, 241, 0.1);
        }

        .btn-soft-primary {
            background-color: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }

        .bg-soft-success {
            background-color: rgba(34, 197, 94, 0.1);
        }

        .bg-soft-danger {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .bg-soft-warning {
            background-color: rgba(245, 158, 11, 0.1);
        }

        .bg-soft-info {
            background-color: rgba(6, 182, 212, 0.1);
        }

        .bg-soft-secondary {
            background-color: rgba(107, 114, 128, 0.1);
        }

        .bg-soft-dark {
            background-color: rgba(31, 41, 55, 0.1);
        }
    </style>

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
                    title: 'Sync Failed!',
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
                    title: 'Sync Data Plans?',
                    text: "This will fetch the latest plans from the API and update your database. Existing convenience fees will be preserved.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6366f1',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Sync Now',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    customClass: {
                        popup: 'rounded-4 shadow-sm',
                        confirmButton: 'rounded-pill px-4',
                        cancelButton: 'rounded-pill px-4'
                    },
                    preConfirm: () => {
                        document.getElementById('syncForm').submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>