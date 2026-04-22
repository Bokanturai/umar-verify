<x-app-layout>
    <title>Smart Idea - User Details</title>

    <div class="container-fluid px-4 mt-4">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title text-primary mb-1 fw-bold">User Details</h3>
                    <ul class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none">Users</a>
                        </li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light border shadow-sm">
                            <i class="ti ti-arrow-left me-2"></i>Back
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-settings me-2"></i>Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                        <i class="ti ti-activity me-2"></i>Update Status
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#updateRoleModal">
                                        <i class="ti ti-user-cog me-2"></i>Update Role
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#updateLimitModal">
                                        <i class="ti ti-cash me-2"></i>Set Transaction Limit
                                    </button>
                                </li>
                                @if(!$user->hasVerifiedEmail())
                                <li>
                                    <form action="{{ route('admin.users.verify-email', $user) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="dropdown-item text-success">
                                            <i class="ti ti-check me-2"></i>Mark Email Verified
                                        </button>
                                    </form>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure? This action cannot be undone.')">
                                            <i class="ti ti-trash me-2"></i>Delete User
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- User Profile Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center pt-5 pb-4">
                        <div class="avatar avatar-xl rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                        </div>
                        <h4 class="mb-1 fw-bold text-dark">{{ $user->first_name }} {{ $user->last_name }} {{ $user->middle_name }}</h4>
                        <p class="text-muted mb-3">{{ $user->email }} 
                            @if($user->hasVerifiedEmail())
                                <i class="ti ti-circle-check-filled text-success ms-1" title="Email Verified"></i>
                            @else
                                <i class="ti ti-alert-circle-filled text-warning ms-1" title="Email Not Verified"></i>
                            @endif
                        </p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                                {{ ucfirst($user->role) }}
                            </span>
                            @php
                                $statusClass = match($user->status) {
                                    'active' => 'bg-soft-success text-success',
                                    'inactive' => 'bg-soft-secondary text-secondary',
                                    'suspended' => 'bg-soft-danger text-danger',
                                    'pending' => 'bg-soft-warning text-warning',
                                    default => 'bg-soft-dark text-dark'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-center gap-2 mb-4">
                             <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#virtualAccountModal">
                                <i class="ti ti-building-bank me-1"></i>Virtual Account
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill" data-bs-toggle="modal" data-bs-target="#userAddressModal">
                                <i class="ti ti-map-pin me-1"></i>User Address
                            </button>
                        </div>

                        <!-- Wallet Balance Section -->
                        <div class="bg-light rounded-3 p-3 mb-4 text-start border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Wallet Balance</span>
                                <i class="ti ti-wallet text-primary fs-4"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-0">₦{{ number_format($user->wallet->balance ?? 0, 2) }}</h3>
                            <div class="small text-muted mt-1">
                                Available: <span class="fw-medium text-dark">₦{{ number_format($user->wallet->available_balance ?? 0, 2) }}</span>
                            </div>
                        </div>

                        <div class="border-top pt-4 text-start">
                            <h6 class="text-uppercase text-muted fs-12 fw-bold mb-3">Contact Information</h6>
                            
                            <div class="mb-3">
                                <label class="small text-muted d-block">Phone Number</label>
                                <span class="text-dark fw-medium">{{ $user->phone_no }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <label class="small text-muted d-block">Address</label>
                                <span class="text-dark fw-medium">{{ $user->address ?: 'N/A' }}</span>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted d-block">BVN</label>
                                <span class="text-dark fw-medium font-monospace">{{ $user->bvn ?: 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="border-top pt-4 text-start">
                            <h6 class="text-uppercase text-muted fs-12 fw-bold mb-3">Account Details</h6>
                            
                            <div class="mb-3">
                                <label class="small text-muted d-block">Transaction Limit</label>
                                <span class="text-dark fw-medium font-monospace">₦{{ number_format($user->limit, 2) }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <label class="small text-muted d-block">Joined On</label>
                                <span class="text-dark fw-medium">{{ $user->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Card -->
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Transaction History</h5>
                        <span class="badge bg-light text-dark border">Total: {{ $transactions->total() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if($transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">S/N</th>
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td class="ps-4">{{ $transactions->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="font-monospace text-primary fw-medium">{{ $transaction->reference }}</span>
                                                        <span class="text-muted small text-wrap" style="max-width: 200px;">{{ Str::limit($transaction->description, 30) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-info text-info rounded-pill px-2">
                                                        {{ ucfirst($transaction->type) }}
                                                    </span>
                                                </td>
                                                <td class="fw-bold text-dark">₦{{ number_format($transaction->amount, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = match($transaction->status) {
                                                            'success', 'successful', 'completed' => 'bg-soft-success text-success',
                                                            'failed' => 'bg-soft-danger text-danger',
                                                            'pending' => 'warning text-warning',
                                                            default => 'bg-soft-secondary text-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusClass }} rounded-pill px-2">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-dark fs-13">{{ $transaction->created_at->format('d M Y') }}</span>
                                                        <span class="text-muted fs-12">{{ $transaction->created_at->format('h:i A') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top">
                                {{ $transactions->links('vendor.pagination.custom') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="ti ti-receipt-off fs-1 text-muted opacity-50"></i>
                                </div>
                                <h6 class="text-muted fw-bold">No transactions found</h6>
                                <p class="text-muted small mb-0">This user hasn't performed any transactions yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Update User Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Select Status</label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Role Modal -->
    <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Update User Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Select Role</label>
                            <select name="role" class="form-select form-select-lg">
                                @foreach(['personal','agent','partner','business','staff','checker','super_admin','api'] as $r)
                                    <option value="{{ $r }}" {{ $user->role === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Limit Modal -->
    <div class="modal fade" id="updateLimitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Set Transaction Limit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.update-limit', $user) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Transaction Limit (₦)</label>
                            <input type="number" name="limit" class="form-control form-control-lg" value="{{ $user->limit }}" min="0" step="0.01">
                            <div class="form-text">Set the maximum transaction limit for this user.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Limit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Virtual Account Modal -->
    <div class="modal fade" id="virtualAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom bg-primary text-white">
                    <h5 class="modal-title fw-bold">Virtual Account Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    @if($user->virtualAccount)
                        <div class="mb-4">
                            <div class="avatar avatar-lg bg-soft-primary rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center">
                                <i class="ti ti-building-bank fs-1 text-primary"></i>
                            </div>
                            <h6 class="text-muted text-uppercase fs-12 fw-bold mb-1">Bank Name</h6>
                            <h4 class="fw-bold text-dark mb-4">{{ $user->virtualAccount->bankName ?? 'N/A' }}</h4>

                            <h6 class="text-muted text-uppercase fs-12 fw-bold mb-1">Account Number</h6>
                            <h2 class="fw-bold text-primary mb-4 font-monospace">{{ $user->virtualAccount->accountNo ?? 'N/A' }}</h2>

                            <h6 class="text-muted text-uppercase fs-12 fw-bold mb-1">Account Name</h6>
                            <h5 class="fw-bold text-dark">{{ $user->virtualAccount->accountName ?? 'N/A' }}</h5>

                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">Reference: <span class="font-monospace">{{ $user->virtualAccount->accountReference ?? 'N/A' }}</span></small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-alert-circle fs-1 text-warning mb-3"></i>
                            <h6 class="text-muted">No Virtual Account Found</h6>
                            <p class="small text-muted mb-0">This user does not have a virtual account yet.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Address Modal -->
    <div class="modal fade" id="userAddressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom bg-primary text-white">
                    <h5 class="modal-title fw-bold">User Address Information</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                     <div class="row g-3">
                        <div class="col-6">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-2">State</label>
                            <p class="fw-medium text-dark border p-3 rounded bg-light mb-0">
                                {{ $user->state ?: 'Not provided' }}
                            </p>
                        </div>
                         <div class="col-6">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-2">LGA</label>
                            <p class="fw-medium text-dark border p-3 rounded bg-light mb-0">
                                {{ $user->lga ?: 'Not provided' }}
                            </p>
                        </div>
                         <div class="col-12">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-2">Full Address</label>
                            <p class="fw-medium text-dark border p-3 rounded bg-light mb-0" style="min-height: 60px;">
                                {{ $user->address ?: 'Not provided' }}
                            </p>
                        </div>
                         <div class="col-12">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-2">Nearest Bus Stop</label>
                            <p class="fw-medium text-dark border p-3 rounded bg-light mb-0">
                                {{ $user->nearest_bus_stop ?: 'Not provided' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<div class="container-fluid px-4 mt-4">

    <!-- SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update Status Form
            const statusForm = document.querySelector('#updateStatusModal form');
            if (statusForm) {
                statusForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const selectedStatus = this.querySelector('select[name="status"]').value;
                    
                    Swal.fire({
                        title: 'Update User Status?',
                        text: `Change status to "${selectedStatus.toUpperCase()}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, update it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            // Update Role Form
            const roleForm = document.querySelector('#updateRoleModal form');
            if (roleForm) {
                roleForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const selectedRole = this.querySelector('select[name="role"]').selectedOptions[0].text;
                    
                    Swal.fire({
                        title: 'Update User Role?',
                        text: `Change role to "${selectedRole}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, update it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            // Update Limit Form
            const limitForm = document.querySelector('#updateLimitModal form');
            if (limitForm) {
                limitForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const limitValue = this.querySelector('input[name="limit"]').value;
                    
                    Swal.fire({
                        title: 'Update Transaction Limit?',
                        text: `Set limit to ₦${parseFloat(limitValue).toLocaleString()}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, update it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            // Email Verification Form
            const verifyEmailForm = document.querySelector('form[action*="verify-email"]');
            if (verifyEmailForm) {
                verifyEmailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Verify Email?',
                        text: 'Mark this user\'s email as verified?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, verify it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            // Delete User Form
            const deleteForm = document.querySelector('form[action*="destroy"]');
            if (deleteForm) {
                const deleteButton = deleteForm.querySelector('button[type="submit"]');
                if (deleteButton) {
                    deleteButton.removeAttribute('onclick'); // Remove old confirm
                    deleteForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        Swal.fire({
                            title: 'Delete User?',
                            text: 'This action cannot be undone! All user data will be permanently deleted.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                }
            }

            // Success/Error Messages from Session
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: '<ul class="text-start">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
</x-app-layout>
