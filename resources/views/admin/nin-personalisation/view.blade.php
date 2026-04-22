<x-app-layout>
    <title>Smart Idea - NIN Personalisation details</title>

    <div class="content">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold text-primary">NIN Personalisation Details</h4>
                            <p class="text-muted mb-0">View and manage NIN Personalisation request</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.nin-personalisation.index') }}" class="btn btn-light">
                                <i class="ti ti-arrow-left me-1"></i> Back to List
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                <i class="ti ti-edit me-1"></i> Update Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('errorMessage'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle fs-4 me-3"></i>
                    <div>
                        <strong>Error!</strong> {{ session('errorMessage') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('successMessage'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fs-4 me-3"></i>
                    <div>
                        <strong>Success!</strong> {{ session('successMessage') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Left Column - Enrollment Info --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="ti ti-info-circle me-2 text-primary"></i>Enrollment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            {{-- Agent ID --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Agent ID</label>
                                <div class="d-flex align-items-center">
                                    <span class="fw-medium">{{ $enrollmentInfo->user_id }}</span>
                                    @if (!empty($user))
                                        <button type="button" class="btn btn-sm btn-light ms-2 text-primary"
                                            data-bs-toggle="modal" data-bs-target="#agentInfoModal">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Amount Charged --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Amount Charged</label>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-wallet text-success fs-20 me-2"></i>
                                    <span class="text-uppercase fw-bold text-dark">₦{{ number_format($enrollmentInfo->amount, 2) }}</span>
                                </div>
                            </div>

                            {{-- Request ID --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Request ID</label>
                                <p class="fw-medium mb-0">{{ $enrollmentInfo->reference }}</p>
                            </div>

                        

                            {{-- NIN --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">NIN</label>
                                <p class="fw-medium mb-0">{{ $enrollmentInfo->nin }}</p>
                            </div>

                            {{-- Tracking ID --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Tracking ID</label>
                                <p class="fw-medium mb-0">{{ $enrollmentInfo->tracking_id }}</p>
                            </div>


                            {{-- Service Field --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Service Field</label>
                                <p class="fw-medium mb-0">{{ $enrollmentInfo->service_field_name ?? $enrollmentInfo->field_name }}</p>
                            </div>

                            {{-- Current Status --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Current Status</label>
                                <div>
                                    @php
                                        $statusClass = match($enrollmentInfo->status) {
                                            'pending' => 'bg-warning-subtle text-warning',
                                            'processing' => 'bg-info-subtle text-info',
                                            'in-progress' => 'bg-primary-subtle text-primary',
                                            'resolved', 'successful' => 'bg-success-subtle text-success',
                                            'rejected', 'failed' => 'bg-danger-subtle text-danger',
                                            'query' => 'bg-warning-subtle text-warning',
                                            'remark' => 'bg-secondary-subtle text-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill">
                                        {{ ucfirst($enrollmentInfo->status) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Date Created --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Date Created</label>
                                <p class="fw-medium mb-0">
                                    {{ $enrollmentInfo->submission_date ? \Carbon\Carbon::parse($enrollmentInfo->submission_date)->format('M j, Y g:i A') : 'N/A' }}
                                </p>
                            </div>

                            {{-- New Information --}}
                            <div class="col-12">
                                <label class="form-label text-muted small text-uppercase fw-bold">New Information</label>
                                <div class="p-3 bg-light rounded border">
                                    {{ $enrollmentInfo->description }}
                                </div>
                            </div>

                            {{-- Comment --}}
                            <div class="col-12">
                                <label class="form-label text-muted small text-uppercase fw-bold">Latest Comment</label>
                                <div class="p-3 bg-light rounded border">
                                    {{ $enrollmentInfo->comment ?? 'N/A' }}
                                </div>
                            </div>

                            {{-- Documents --}}
                            <div class="col-12">
                                <label class="form-label text-muted small text-uppercase fw-bold mb-3">Documents</label>
                                <div class="d-flex gap-3 flex-wrap">
                                    @if($enrollmentInfo->file_url)
                                    <div class="card border p-3" style="min-width: 200px;">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ti ti-file-check fs-3 text-success me-2"></i>
                                            <span class="fw-bold">Uploaded Document</span>
                                        </div>
                                        <div class="text-muted small mb-2">Supporting Doc</div>
                                        <a href="{{ Storage::url($enrollmentInfo->file_url) }}" target="_blank" class="btn btn-sm btn-outline-success w-100">
                                            <i class="ti ti-download me-1"></i> Download
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Status History --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="ti ti-history me-2 text-primary"></i>Status History
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($statusHistory->isNotEmpty())
                            <div class="timeline">
                                @foreach ($statusHistory as $history)
                                    <div class="timeline-item pb-4 border-start ps-4 position-relative">
                                        @php
                                            $historyStatusColor = match($history['status']) {
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
                                        <span class="position-absolute top-0 start-0 translate-middle p-2 bg-{{ $historyStatusColor }} border border-light rounded-circle"></span>
                                        
                                        <div class="mb-1">
                                            <span class="badge bg-{{ $historyStatusColor }}-subtle text-{{ $historyStatusColor }} mb-1">
                                                {{ ucfirst($history['status']) }}
                                            </span>
                                            <span class="text-muted small d-block">
                                                {{ \Carbon\Carbon::parse($history['submission_date'])->format('M j, Y g:i A') }}
                                            </span>
                                        </div>
                                        
                                        @if (!empty($history['comment']))
                                            <div class="bg-light p-2 rounded small text-muted mt-2">
                                                {{ $history['comment'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="ti ti-clock-off fs-1 mb-2 d-block"></i>
                                No status history available
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
            
        {{-- Update Status Modal --}}
        <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="ti ti-edit me-2"></i>Update Status
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.nin-personalisation.update', $enrollmentInfo->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4">
                            {{-- Status --}}
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">New Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" {{ old('status', $enrollmentInfo->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status', $enrollmentInfo->status) === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="in-progress" {{ old('status', $enrollmentInfo->status) === 'in-progress' ? 'selected' : '' }}>In-Progress</option>
                                    <option value="resolved" {{ old('status', $enrollmentInfo->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="successful" {{ old('status', $enrollmentInfo->status) === 'successful' ? 'selected' : '' }}>Successful</option>
                                    <option value="query" {{ old('status', $enrollmentInfo->status) === 'query' ? 'selected' : '' }}>Query</option>
                                    <option value="rejected" {{ old('status', $enrollmentInfo->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="failed" {{ old('status', $enrollmentInfo->status) === 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="remark" {{ old('status', $enrollmentInfo->status) === 'remark' ? 'selected' : '' }}>Remark</option>
                                </select>
                            </div>

                            {{-- Comment --}}
                            <div class="mb-3">
                                <label for="comment" class="form-label fw-semibold">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="4" 
                                    placeholder="Enter your comment here..." 
                                    >{{ old('comment', $enrollmentInfo->comment) }}</textarea>
                                <small class="text-muted">
                                    <i class="ti ti-info-circle me-1"></i>Comment will auto-fill based on selected status
                                </small>
                            </div>

                            {{-- File Upload --}}
                            <div class="mb-3">
                                <label for="file" class="form-label fw-semibold">Upload Document</label>
                                <input type="file" class="form-control" id="file" name="file" 
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">
                                    <i class="ti ti-paperclip me-1"></i>Accepted formats: PDF, JPG, PNG, DOC, DOCX (Max: 2MB)
                                </small>
                            </div>

                            {{-- Force Refund --}}
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="force_refund" name="force_refund" value="1">
                                <label class="form-check-label text-danger fw-bold" for="force_refund">
                                    Force Refund (Process again even if already refunded)
                                </label>
                                <small class="form-text text-muted d-block">
                                    Check this ONLY if you need to credit the user again manually.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Agent Info Modal --}}
        @if(!empty($user))
            <div class="modal fade" id="agentInfoModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="agentInfoModalLabel">
                                <i class="bi bi-person-badge me-2"></i> Agent Information
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body bg-light p-4">
                            <div class="text-center mb-4">
                                @php
                                    if (!empty($user->profile_photo_url)) {
                                        if (filter_var($user->profile_photo_url, FILTER_VALIDATE_URL)) {
                                            $profileImage = $user->profile_photo_url;
                                        } else {
                                            $profileImage = asset('storage/' . $user->profile_photo_url);
                                        }
                                    } else {
                                        $profileImage = asset('assets/img/users/user-01.jpg');
                                    }
                                @endphp
                                <img src="{{ $profileImage }}" 
                                     alt="{{ $user->first_name }} {{ $user->last_name }}" 
                                     class="rounded-circle shadow border border-3 border-white" 
                                     width="100" 
                                     height="100"
                                     style="object-fit: cover;"
                                     onerror="this.src='{{ asset('assets/img/users/user-01.jpg') }}'">
                                <h5 class="mt-3 mb-1 fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h5>
                                <span class="badge bg-primary-subtle text-primary">{{ ucfirst($user->role ?? 'User') }}</span>
                            </div>

                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="mb-3 border-bottom pb-2">
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Email Address</small>
                                        <span class="fw-medium">{{ $user->email }}</span>
                                    </div>
                                    <div class="mb-3 border-bottom pb-2">
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Phone Number</small>
                                        <span class="fw-medium">{{ $user->phone_no ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Address</small>
                                        <span class="fw-medium">{{ $user->address ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- JavaScript for Auto Messages --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const statusField = document.getElementById("status");
                const commentField = document.getElementById("comment");

                const messages = {
                    pending: "Your request is pending. Our team will review it shortly.",
                    processing: "Your request is currently being processed. Please hold on.",
                    "in-progress": "Your request is in progress. We are working on it and will update you soon.",
                    resolved: "✅ Your Request Has Been Successfully Treated!\n\nHello 👋,\n\nWe're glad to inform you that your recent request has been successfully processed. Thank you for trusting us!\n🎯 Don't stop here — we're always ready to serve you better. Feel free to send in more requests anytime.\n\nYour satisfaction is our priority!",
                    successful: "✅ Success! Your request has been completed successfully. Thank you for using our service!",
                    query: "We require additional information regarding your request. Kindly respond promptly.",
                    rejected: "Unfortunately, your request has been rejected. Please review and try again.",
                    failed: "Your request could not be completed due to an error. Please contact support for assistance.",
                    remark: "A remark has been added to your request. Kindly review the details provided."
                };

                // Auto update comment when status changes
                statusField.addEventListener("change", function () {
                    const selectedStatus = statusField.value;
                    if (messages[selectedStatus]) {
                        commentField.value = messages[selectedStatus];
                    } else {
                        commentField.value = "";
                    }
                });
            });
        </script>
    </div>
</x-app-layout>
