<x-app-layout>
    <title>Smart Idea - Data Services Management</title>

    <div class="content">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="page-title text-primary mb-1 fw-bold">Data Services </h3>
                    <ul class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item active text-muted">
                            Manage data plans and variations grouped by network.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-xl-4 col-md-6 fade-in-up" style="animation-delay: 0.1s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--primary-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Total Variations</p>
                            <h3 class="stats-value mb-0 text-white">{{ $totalVariationsCount }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-list-details fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 fade-in-up" style="animation-delay: 0.2s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--success-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Active Variations</p>
                            <h3 class="stats-value mb-0 text-white">{{ $activeVariationsCount }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-check fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 fade-in-up" style="animation-delay: 0.3s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--danger-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Inactive Variations</p>
                            <h3 class="stats-value mb-0 text-white">{{ $inactiveVariationsCount }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-x fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "{{ session('success') }}",
                        timer: 3000,
                        showConfirmButton: true,
                        confirmButtonColor: '#6366f1',
                    });
                });
            </script>
        @endif

        <!-- Services Table Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3 bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold">Data Service Categories</h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <div class="input-icon-end position-relative">
                            <input type="text" class="form-control" placeholder="Search network..." id="serviceSearch">
                            <span class="input-icon-addon">
                                <i class="ti ti-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">S/N</th>
                                <th>Network Name</th>
                                <th>Service ID</th>
                                <th>Total Variations</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach($availableServices as $id => $service)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-semibold text-muted">{{ $i++ }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md bg-soft-{{ $service['color'] }} text-{{ $service['color'] }} rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <i class="{{ $service['icon'] }} fs-18"></i>
                                            </div>
                                            <h6 class="fw-medium mb-0">
                                                <a href="{{ route('admin.data-variations.show', $id) }}" class="text-dark">{{ $service['name'] }}</a>
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-primary">{{ $id }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info text-info">{{ $serviceCounts[$id] ?? 0 }} Variations</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-success d-flex align-items-center badge-xs" style="width: fit-content;">
                                            <i class="ti ti-point-filled me-1"></i>Active
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="action-icon d-inline-flex">
                                            <a href="{{ route('admin.data-variations.show', $id) }}" class="btn btn-sm btn-soft-primary rounded-pill px-5" title="View Variations">
                                                View <i class="ti ti-eye ms-1"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        
        .stats-label { font-size: 0.875rem; font-weight: 500; opacity: 0.9; }
        .stats-value { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em; }

        .avatar-lg { width: 3rem; height: 3rem; display: flex; align-items: center; justify-content: center; }
        .avatar-md { width: 2.5rem; height: 2.5rem; }
        
        .bg-soft-primary { background-color: rgba(99, 102, 241, 0.1); }
        .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1); }
        .bg-soft-danger { background-color: rgba(239, 68, 68, 0.1); }
        .bg-soft-success { background-color: rgba(34, 197, 94, 0.1); }
        .bg-soft-info { background-color: rgba(59, 130, 246, 0.1); }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .fs-18 { font-size: 18px; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    
    <script>
        document.getElementById('serviceSearch').addEventListener('keyup', function() {
            let value = this.value.toLowerCase();
            let rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                let text = row.querySelector('h6').innerText.toLowerCase();
                let sid = row.querySelector('code').innerText.toLowerCase();
                row.style.display = (text.includes(value) || sid.includes(value)) ? '' : 'none';
            });
        });
    </script>
</x-app-layout>
