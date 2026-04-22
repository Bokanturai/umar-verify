<x-app-layout>
    <title>Smart Idea - {{ $title ?? 'NIN Personalisation Form' }}</title>
    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">NIN Personalisation</h3>
                        <p class="text-muted small mb-0">Submit your NIN personalisation request for manual review.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-3">
            <div class="row">
                <!-- NIN Personalisation Form -->
                <div class="col-xl-6 mb-4">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-badge me-2"></i>New Personalisation Request</h5>
                        </div>

                        <div class="card-body p-4">
                            {{-- Alerts --}}
                            @if (session('status'))
                                <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show border-0 shadow-sm mb-4">
                                    <i class="bi bi-{{ session('status') === 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                                    <ul class="mb-0 small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                <p class="text-muted">
                                    Ensure the Tracking ID and NIN provided are correct to avoid delays.
                                </p>
                            </div>

                            {{-- Form --}}
                            <form method="POST" action="{{ route('nin-personalisation.store') }}" class="row g-4">
                                @csrf

                                <!-- Service Category -->
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Service Category <span class="text-danger">*</span></label>
                                    <select class="form-select border-primary-subtle" name="field_code" id="service_field" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach ($fieldname as $field)
                                            @php
                                                $price = $field->prices
                                                    ->where('user_type', auth()->user()->role)
                                                    ->first()?->price ?? $field->base_price;
                                            @endphp
                                            <option value="{{ $field->id }}"
                                                    data-price="{{ $price }}"
                                                    data-description="{{ $field->description }}"
                                                    {{ old('field_code') == $field->id ? 'selected' : '' }}>
                                                {{ $field->field_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2">
                                        <small class="text-muted fst-italic" id="field-description"></small>
                                    </div>
                                </div>

                                <!-- Tracking ID -->
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Tracking ID <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                                        <input class="form-control" name="tracking_id" type="text" required
                                               placeholder="Enter Tracking ID"
                                               value="{{ old('tracking_id') }}">
                                    </div>
                                </div>


                                <!-- Pricing Info -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Service Fee</label>
                                    <div class="alert alert-secondary py-2 mb-0 text-center border-0 shadow-sm">
                                        <span class="h5 fw-bold mb-0 text-primary" id="field-price">₦0.00</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Wallet Balance</label>
                                    <div class="alert alert-soft-success py-2 mb-0 text-center border-0 shadow-sm">
                                        <span class="h5 fw-bold mb-0 text-success">₦{{ number_format($wallet->balance ?? 0, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="col-12 d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg shadow-sm hover-up">
                                        <i class="bi bi-send-fill me-2"></i> Submit Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Submission History -->
                <div class="col-xl-6">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="fw-bold mb-0 text-white">
                                <i class="bi bi-clock-history me-2"></i> Request History
                            </h5>
                        </div>

                        <div class="card-body p-4">
                            <!-- Filter Form -->
                            <form method="GET" class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                                <div class="col-md-5">
                                    <input class="form-control border-0 shadow-sm"
                                           name="search"
                                           type="text"
                                           placeholder="Tracking ID..."
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select border-0 shadow-sm" name="status">
                                        <option value="">All Statuses</option>
                                        @foreach(['pending','processing','successful','query','resolved','rejected','remark'] as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary w-100 shadow-sm" type="submit">
                                        <i class="bi bi-filter"></i> Filter
                                    </button>
                                </div>
                            </form>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>Ref / Tracking ID</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($submissions as $submission)
                                            <tr>
                                                <td class="fw-bold text-muted">{{ $loop->iteration + $submissions->firstItem() - 1 }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-primary small fw-medium">{{ $submission->reference }}</span>
                                                        <span class="text-dark fw-bold">{{ $submission->tracking_id }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = match($submission->status) {
                                                            'resolved', 'successful' => 'success',
                                                            'processing', 'in-progress' => 'primary',
                                                            'rejected', 'failed'     => 'danger',
                                                            'query'                  => 'info',
                                                            'remark'                 => 'secondary',
                                                            default                  => 'warning'
                                                        };
                                                    @endphp
                                                    <span class="badge rounded-pill bg-{{ $statusClass }}">
                                                        {{ ucfirst($submission->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @php
                                                            $fileUrl = $submission->file_url ? \Illuminate\Support\Facades\Storage::url($submission->file_url) : '';
                                                        @endphp
                                                        <button type="button"
                                                                class="btn btn-sm btn-icon btn-outline-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#commentModal"
                                                                data-comment="{{ $submission->comment ?? 'Your request is in review.' }}"
                                                                data-file-url="{{ $fileUrl }}">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                        @if($fileUrl)
                                                        <a href="{{ $fileUrl }}" 
                                                           class="btn btn-sm btn-icon btn-outline-success" 
                                                           download 
                                                           target="_blank"
                                                           title="Download Document">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-5">
                                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                    No personalisation requests found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $submissions->withQueryString()->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.comment')

    <style>
        .hover-up:hover { transform: translateY(-3px); transition: all 0.3s ease; }
        .alert-soft-success { background-color: #d1e7dd; color: #0f5132; }
        .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }
        .table thead th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fieldSelect = document.getElementById('service_field');
            const priceDisplay = document.getElementById('field-price');
            const descArea = document.getElementById('field-description');

            if (fieldSelect) {
                fieldSelect.addEventListener('change', function() {
                    let selectedOption = this.options[this.selectedIndex];
                    let price = selectedOption.getAttribute('data-price');
                    let description = selectedOption.getAttribute('data-description');

                    if (price) {
                        priceDisplay.textContent = '₦' + parseFloat(price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    } else {
                        priceDisplay.textContent = '₦0.00';
                    }

                    if (description) {
                        descArea.textContent = description;
                    } else {
                        descArea.textContent = '';
                    }
                });
                fieldSelect.dispatchEvent(new Event('change'));
            }

            @if (session('status') && session('message'))
                Swal.fire({
                    icon: "{{ session('status') === 'success' ? 'success' : 'error' }}",
                    title: "{{ session('status') === 'success' ? 'Success!' : 'Oops!' }}",
                    text: "{{ session('message') }}",
                    confirmButtonColor: '#3085d6',
                });
            @endif
        });
    </script>
</x-app-layout>
