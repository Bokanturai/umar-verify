<x-app-layout>
    <title>Smart Idea - User Management</title>

    <div class="content">

        {{-- Alerts --}}
        {{-- Alerts --}}
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: "{{ session('success') }}",
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif

        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let errorMsg = '';
                    @foreach ($errors->all() as $error)
                        errorMsg += '{{ $error }}\n';
                    @endforeach
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg,
                        confirmButtonText: 'OK'
                    });
                });
            </script>
        @endif

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.1s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--primary-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Total Users</p>
                            <h3 class="stats-value mb-0">{{ number_format($totalUsers) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Registered Accounts</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-users fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.2s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--success-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Active Users</p>
                            <h3 class="stats-value mb-0">{{ number_format($activeUsers) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Currently Active</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-user-check fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.3s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--danger-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Inactive Users</p>
                            <h3 class="stats-value mb-0">{{ number_format($inactiveUsers) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Requires Attention</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-user-x fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.4s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--info-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Transacting Users</p>
                            <h3 class="stats-value mb-0">{{ number_format($usersWithTransactions) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">With Activity</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-credit-card fs-24 text-white"></i>
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

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('admin.users.index') }}" method="GET">
                            <div class="row g-3 align-items-center">
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-search"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0 bg-light" 
                                               placeholder="Search Name, Email, Phone..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <select name="role" class="form-select bg-light" onchange="this.form.submit()">
                                        <option value="">All Roles</option>
                                        @foreach(['personal','agent','partner','business','staff','checker','super_admin','api'] as $r)
                                            <option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <select name="status" class="form-select bg-light" onchange="this.form.submit()">
                                        <option value="">All Statuses</option>
                                        @foreach(['active','inactive','suspended','pending'] as $s)
                                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <select name="state" class="form-select bg-light" onchange="this.form.submit()">
                                        <option value="">All States</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>{{ ucfirst($state) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <select name="lga" class="form-select bg-light" onchange="this.form.submit()">
                                        <option value="">All LGAs</option>
                                        @foreach($lgas as $lga)
                                            <option value="{{ $lga }}" {{ request('lga') == $lga ? 'selected' : '' }}>{{ ucfirst($lga) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-1">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Registered Users</h5>
                        <div>
                            <span class="badge bg-light text-dark border me-2">Total: {{ $users->total() }}</span>
                            <!-- Quick Action Buttons -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                    <i class="ti ti-user-plus me-1"></i>Add
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                                    <i class="ti ti-file-upload me-1"></i>Bulk
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">S/N</th>
                                        <th>User Details</th>
                                        <th>Contact Info</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Limit</th>
                                        <th>Joined</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td class="ps-4">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-sm me-2 avatar-rounded bg-primary text-white d-flex align-items-center justify-content-center fw-bold">
                                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                                    </span>
                                                    <div>
                                                        <h6 class="mb-0 fs-14 fw-medium text-dark">
                                                            <a href="{{ route('admin.users.show', $user) }}" class="text-dark text-decoration-none">
                                                                {{ $user->first_name }} {{ $user->last_name }}
                                                            </a>
                                                        </h6>
                                                        <span class="text-muted fs-12">ID: {{ $user->id }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fs-13">{{ $user->email }}</span>
                                                    <span class="text-muted fs-12">{{ $user->phone_no }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.users.update-role', $user) }}">
                                                    @csrf @method('PATCH')
                                                    <select name="role" class="form-select form-select-sm border-0 bg-transparent fw-medium text-dark" style="width: auto;" onchange="this.form.submit()">
                                                        @foreach(['personal','agent','partner','business','staff','checker','super_admin','api'] as $r)
                                                            <option value="{{ $r }}" @selected($user->role == $r)>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.users.update-status', $user) }}">
                                                    @csrf @method('PATCH')
                                                    @php
                                                        $statusClass = match($user->status) {
                                                            'active' => 'text-success',
                                                            'inactive' => 'text-secondary',
                                                            'suspended' => 'text-danger',
                                                            'pending' => 'text-warning',
                                                            default => 'text-dark'
                                                        };
                                                    @endphp
                                                    <select name="status" class="form-select form-select-sm border-0 bg-transparent fw-bold {{ $statusClass }}" style="width: auto;" onchange="this.form.submit()">
                                                        @foreach(['active','inactive','suspended','pending','query'] as $s)
                                                            <option value="{{ $s }}" @selected($user->status == $s)>{{ ucfirst($s) }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>

                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-muted fs-12">{{ $user->limit }}</span>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="text-muted fs-12">{{ $user->created_at->format('d M Y') }}</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-xs btn-outline-primary border-0 rounded-circle me-1" title="View">
                                                        <i class="ti ti-eye fs-12"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-xs btn-outline-secondary border-0 rounded-circle me-1" title="Edit">
                                                        <i class="ti ti-edit fs-12"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-xs btn-outline-danger border-0 rounded-circle" onclick="confirmDelete('{{ $user->id }}')" title="Delete">
                                                        <i class="ti ti-trash fs-12"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="ti ti-users-off fs-1 opacity-50"></i>
                                                    <p class="mt-2 mb-0">No users found</p>
                                                    <div class="mt-3">
                                                        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                                            <i class="ti ti-user-plus me-1"></i>Add First User
                                                        </button>
                                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                                                            <i class="ti ti-file-upload me-1"></i>Bulk Upload
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="card-footer bg-white border-top py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
                                    </div>
                                    {{ $users->links('vendor.pagination.custom') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- IP Blocking Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold">IP Address Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="ipBlockingAccordion">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button bg-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ipBlockingCollapse">
                                        <i class="ti ti-shield-lock me-2"></i>
                                        Manage Blocked IP Addresses
                                    </button>
                                </h2>
                                <div id="ipBlockingCollapse" class="accordion-collapse collapse" data-bs-parent="#ipBlockingAccordion">
                                    <div class="accordion-body">
                                        <form action="{{ route('admin.users.block-ip') }}" method="POST" class="row g-3 mb-4 align-items-end">
                                            @csrf
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">IP Address</label>
                                                <input type="text" name="ip_address" placeholder="e.g. 192.168.1.1" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Reason</label>
                                                <input type="text" name="reason" placeholder="Why is this IP being blocked?" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-danger w-100"><i class="ti ti-ban me-1"></i> Block</button>
                                            </div>
                                        </form>

                                        @if($blockedIps && $blockedIps->count())
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>IP Address</th>
                                                            <th>Reason</th>
                                                            <th>Blocked By</th>
                                                            <th>Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($blockedIps as $ip)
                                                            <tr>
                                                                <td class="font-monospace">{{ $ip->ip_address }}</td>
                                                                <td>{{ $ip->reason }}</td>
                                                                <td>{{ $ip->blocker->name ?? 'N/A' }}</td>
                                                                <td>{{ $ip->created_at->format('d M Y H:i') }}</td>
                                                                <td>
                                                                    <form action="{{ route('admin.users.unblock-ip', $ip) }}" method="POST" class="d-inline">
                                                                        @csrf @method('DELETE')
                                                                        <button class="btn btn-xs btn-outline-success rounded-pill">Unblock</button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center mb-0">No IP addresses are currently blocked.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="ti ti-user-plus me-2"></i>Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_no" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">BVN</label>
                                <input type="text" name="bvn" class="form-control" maxlength="11" required>
                            </div>
                        </div>
                        <div class="alert alert-light border mt-3 mb-0">
                            <div class="d-flex">
                                <i class="ti ti-info-circle text-primary fs-4 me-2"></i>
                                <small class="text-muted">
                                    A random password will be generated and sent to the user's email address along with a verification link.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Upload Modal -->
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="ti ti-file-upload me-2"></i>Bulk User Upload</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <div class="avatar avatar-lg bg-soft-primary text-primary rounded-circle mb-3">
                                <i class="ti ti-file-spreadsheet fs-1"></i>
                            </div>
                            <h6 class="fw-bold">Upload CSV/Excel File</h6>
                            <p class="text-muted small">Upload a file containing multiple user records to create them in bulk.</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Select File</label>
                            <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">Supported formats: .csv, .xlsx, .xls</div>
                        </div>

                        <div class="alert alert-light border d-flex align-items-center" role="alert">
                            <i class="ti ti-download text-primary fs-4 me-3"></i>
                            <div>
                                <h6 class="alert-heading fs-14 fw-bold mb-1">Need a template?</h6>
                                <p class="mb-0 small text-muted">Download the <a href="{{ route('admin.users.download-sample') }}" class="fw-bold text-primary text-decoration-none">sample CSV file</a> to see the required format.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Upload & Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
     <div class="page-header mb-6">
            </div>

    <!-- Bootstrap JS (if not already loaded) -->
    <!-- Bootstrap JS (if not already loaded) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    {{-- SweetAlert CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! User data will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete user!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</x-app-layout>