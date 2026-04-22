<x-app-layout>
   <title>Smart Idea - Vnin to nibss</title>
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
                        <small class="text-white-50 fs-12 fw-medium">Work on this request its Urgent!</small>
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
                        <small class="text-white-50 fs-12 fw-medium">Check and confirm The status</small>
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
                        <small class="text-white-50 fs-12 fw-medium">You have did a great Job</small>
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
                        <small class="text-white-50 fs-12 fw-medium">Don’t give up — Kept accepting Request</small>
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


<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">VNIN to NIBSS Requests</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots-vertical text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                <div class="dropdown-header">Export Options:</div>
                <a class="dropdown-item" href="#"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Export as CSV</a>
                <a class="dropdown-item" href="#"><i class="bi bi-file-excel me-2"></i>Export as Excel</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Print Records</a>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Search and Filters --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" class="form-inline search-full col">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by BVN, NIN, Transaction Ref, Agent Name..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="bank" value="{{ request('bank') }}">
                </form>
            </div>

            <div class="col-md-6 text-md-end">
                <div class="btn-group">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel"></i>
                        @if(request('status') || request('bank'))
                            Filters Active
                        @else
                            Filters
                        @endif
                    </button>

                    @if(request('status') || request('search') || request('bank'))
                        <a href="{{ route('admin.vnin-nibss.index') }}" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Errors --}}
         @if (session('errorMessage'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error!</strong> {{ session('errorMessage') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @if (session('successMessage'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Success!</strong> {{ session('successMessage') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif


        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Req ID</th>
                        <th>BVN</th>
                        <th>NIN</th>
                        <th>Agent Name</th>
                        <th>Service Field</th>
                        <th>Status</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                  
                    @forelse ($enrollments as $enrollment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $enrollment->request_id }}</td>
                            <td>{{ $enrollment->bvn }}</td>
                            <td>{{ $enrollment->nin }}</td>
                            <td>{{ $enrollment->performed_by }}</td>
                            <td>{{ $enrollment->service_field_name ?? $enrollment->field_name }}</td>
                            <td>
                               @php
                                    $statusColor = match($enrollment->status) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'in-progress' => 'primary',
                                        'resolved', 'successful' => 'success',
                                        'rejected', 'failed' => 'danger',
                                        'query' => 'warning',
                                        'remark' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($enrollment->submission_date)->format('M j, Y g:i A') }}</td>
                            <td>
                                <a href="{{ route('admin.vnin-nibss.show', $enrollment->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="card-footer bg-white">
                {{ $enrollments->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>

{{-- Filter Modal --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Requests</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bankFilter" class="form-label">Bank</label>
                        <select class="form-select" id="bankFilter" name="bank">
                            <option value="">All Banks</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank }}" {{ request('bank') == $bank ? 'selected' : '' }}>
                                    {{ $bank }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">All Statuses</option>
                            @foreach(['pending', 'processing', 'in-progress', 'resolved', 'successful', 'rejected', 'failed', 'query', 'remark'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="search" value="{{ request('search') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
