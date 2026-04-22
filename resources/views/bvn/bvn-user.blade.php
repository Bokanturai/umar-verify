<x-app-layout>
    <title>Zaid-Verify - {{ $title ?? 'BVN Enrolment Agent' }}</title>

    <div class="page-body">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <h3 class="fw-bold text-primary">BVN Enrolment Agent Request Form</h3>
                        <p class="text-muted small mb-0">
                            Submit your BVN agency request. Note that the information will be shared with the selected bank.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- BVN User Form -->
                <div class="col-12 col-xl-12 mb-4">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-person-badge me-2"></i>BVN Enrolment Form
                            </h5>
                            <span class="badge bg-light text-primary fw-semibold">Zaid-Verify</span>
                        </div>

                        <div class="card-body">
                            {{-- Alerts --}}
                            @if (session('status'))
                                <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                                    <i class="bi bi-{{ session('status') === 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Please fix the following errors:</strong>
                                    <ul class="mb-0 small mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- BVN User Form --}}
                            <form method="POST" action="{{ route('bvn-user.store') }}" class="needs-validation" novalidate>
                                @csrf
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                                <div class="row g-3">
                                    @include('pages.bvn.bvn_user_form')

                                    <div class="col-12 d-grid mt-4">
                                        <button class="btn btn-primary btn-lg fw-semibold py-2" type="submit">
                                            <i class="bi bi-send-fill me-2"></i> Submit Form
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                 <!-- User Request History -->
                <div class="col-12 col-xl-12 mb-4">
                    <div class="card shadow-sm border-0 rounded-3 h-100">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>User Request History</h5>
                            <span class="badge bg-light text-primary fw-semibold">Smart Idea</span>
                        </div>
                        <div class="card-body">

                            <!-- Filter Form -->
                            <form class="row g-3 mb-3" method="GET">
                                <div class="col-12 col-md-6">
                                    <input class="form-control" name="search" type="text" placeholder="Search by Phone" value="{{ request('search') }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <select class="form-select" name="status">
                                        <option value="">All Status</option>
                                        @foreach(['pending', 'processing', 'query', 'resolved', 'successful', 'rejected'] as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-2 d-grid">
                                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                                </div>
                            </form>

                            <!-- Submissions Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Request ID</th>
                                            <th>Name</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($crmSubmissions as $index => $submission)
                                            <tr>
                                                <td>{{ $loop->iteration + $crmSubmissions->firstItem() - 1 }}</td>
                                                <td>{{ $submission->reference }}</td>
                                                <td>{{ $submission->account_name }}</td>
                                                <td>{{ $submission->phone_no }}</td>
                                                <td>{{ $submission->email }}</td>
                                                <td>₦{{ number_format($submission->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ match($submission->status) {
                                                        'pending' => 'warning',
                                                        'processing' => 'primary',
                                                        'query' => 'info',
                                                        'resolved' => 'success',
                                                        'successful' => 'success',
                                                        'rejected' => 'danger',
                                                        default => 'secondary'
                                                    } }}">{{ ucfirst($submission->status) }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $fileUrl = '';
                                                        if (!empty($submission->file_url)) {
                                                            $f = $submission->file_url;
                                                            if (preg_match('/^https?:\/\//', $f)) {
                                                                $fileUrl = $f;
                                                            } elseif (str_starts_with($f, '/storage') || str_starts_with($f, 'storage')) {
                                                                $fileUrl = asset(ltrim($f, '/'));
                                                            } else {
                                                                $fileUrl = \Illuminate\Support\Facades\Storage::url($f);
                                                            }
                                                        }
                                                    @endphp

                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="View Comment"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#commentModal"
                                                        data-comment="{{ $submission->comment ?? 'No comment yet.' }}"
                                                        data-query="{{ $submission->query ?? 'No query yet.' }}"
                                                        data-file-url="{{ $fileUrl }}">
                                                        <i class="bi bi-chat-left-text"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No submissions found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{-- Custom Pagination --}}
                                {{ $crmSubmissions->withQueryString()->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Row -->
        </div>
    </div>

     {{-- Comment Modal --}}
    @include('pages.comment')

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</x-app-layout>
