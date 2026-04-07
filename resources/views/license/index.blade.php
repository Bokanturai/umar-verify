<x-app-layout>
    <x-slot name="title">License & Permit Registration</x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="fw-bold mb-2">Government License and Permit Processing</h3>
                                <p class="opacity-75 mb-0">We assist individuals and businesses to submit applications for various government licenses and permits in Nigeria. Submit your request and specify the license you need. Our team will guide you on the requirements and processing procedures.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">
                                    Submission fee: ₦1,000
                                </span>
                            </div>
                        </div>
                        <!-- Decorative circle -->
                        <div class="position-absolute top-0 end-0 translate-middle mt-n4 me-n4 bg-white opacity-10 rounded-circle" style="width: 200px; height: 200px;"></div>
                    </div>
                </div>

                @include('pages.alart')

                <div class="row g-4">
                    <!-- Form Section -->
                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4">
                                <h5 class="fw-bold mb-0">Submit New Request</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('license.store') }}" method="POST" id="licenseForm">
                                    @csrf

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">First Name</label>
                                            <input type="text" name="first_name" class="form-control border-light rounded-3 bg-light" placeholder="First Name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Last Name</label>
                                            <input type="text" name="last_name" class="form-control border-light rounded-3 bg-light" placeholder="Last Name" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-muted small text-uppercase">Middle Name (Optional)</label>
                                        <input type="text" name="middle_name" class="form-control border-light rounded-3 bg-light" placeholder="Middle Name">
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Email Address</label>
                                            <input type="email" name="email" class="form-control border-light rounded-3 bg-light" placeholder="Email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted small text-uppercase">Phone Number</label>
                                            <input type="text" name="phone_number" class="form-control border-light rounded-3 bg-light" placeholder="Phone" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-muted small text-uppercase">License Category</label>
                                        <select name="category" id="category" class="form-select border-light rounded-3 bg-light" required>
                                            <option value="" disabled selected>Select a category</option>
                                            <option value="Business and Corporate">Business and Corporate</option>
                                            <option value="Financial and Fintech">Financial and Fintech</option>
                                            <option value="Import and Export">Import and Export</option>
                                            <option value="Food, Drugs and Health">Food, Drugs and Health</option>
                                            <option value="Telecom and Technology">Telecom and Technology</option>
                                            <option value="Oil and Gas">Oil and Gas</option>
                                            <option value="Agriculture">Agriculture</option>
                                            <option value="Education">Education</option>
                                            <option value="Construction and Real Estate">Construction and Real Estate</option>
                                            <option value="Transport and Logistics">Transport and Logistics</option>
                                            <option value="Media and Entertainment">Media and Entertainment</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-muted small text-uppercase">Service / License Type</label>
                                        <select name="license_type" id="license_type" class="form-select border-light rounded-3 bg-light" required disabled>
                                            <option value="" disabled selected>First select a category</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold text-muted small text-uppercase">Request Details / Description</label>
                                        <textarea name="description" class="form-control border-light rounded-3 bg-light" rows="4" placeholder="Briefly describe your requirements..." required>{{ old('description') }}</textarea>
                                    </div>

                                    <div class="bg-light p-3 rounded-3 mb-4 d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted small d-block">Submission Fee</span>
                                            <h5 class="fw-bold mb-0">₦1,000.00</h5>
                                        </div>
                                        <div>
                                            <span class="text-muted small d-block">Wallet Balance</span>
                                            <h5 class="fw-bold mb-0 text-{{ ($wallet->balance ?? 0) >= 1000 ? 'success' : 'danger' }}">₦{{ number_format(($wallet->balance ?? 0), 2) }}</h5>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm" {{ ($wallet->balance ?? 0) < 1000 ? 'disabled' : '' }}>
                                        Submit Request
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- History Section -->
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0">Recent Requests</h5>
                                <button onclick="batchCheck()" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm">
                                    <i class="ti ti-refresh me-1"></i> Check Status
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 text-muted small text-uppercase fw-bold">Service</th>
                                                <th class="text-muted small text-uppercase fw-bold">Details</th>
                                                <th class="text-muted small text-uppercase fw-bold">Status</th>
                                                <th class="text-muted small text-uppercase fw-bold">Date</th>
                                                <th class="text-muted small text-uppercase fw-bold text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($submissions as $sub)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="fw-semibold text-dark">{{ $sub->field_name }}</div>
                                                    <div class="text-muted small">{{ $sub->bank ?? $sub->service_name }}</div>
                                                </td>
                                                <td>
                                                    <div class="small text-dark">{{ $sub->first_name }} {{ $sub->last_name }}</div>
                                                    <div class="text-muted xsmall">{{ $sub->phone_number }}</div>
                                                </td>
                                                <td>
                                                    @if($sub->status == 'successful')
                                                        <span class="badge bg-success-subtle text-success px-3 rounded-pill text-capitalize">{{ $sub->status }}</span>
                                                    @elseif($sub->status == 'pending' || $sub->status == 'processing')
                                                        <span class="badge bg-warning-subtle text-warning px-3 rounded-pill text-capitalize">{{ $sub->status }}</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger px-3 rounded-pill text-capitalize">{{ $sub->status }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted small">
                                                    {{ $sub->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-light rounded-circle shadow-none" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#commentModal" 
                                                            data-comment="{{ $sub->comment }}" 
                                                            data-file-url="{{ $sub->file_url }}" 
                                                            data-approved-by="{{ $sub->approved_by }}"
                                                            title="View Comment">
                                                        <i class="ti ti-message-dots fs-20 text-primary"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="opacity-25 mb-2">
                                                        <i class="ti ti-receipt-off fs-1"></i>
                                                    </div>
                                                    <p class="text-muted mb-0">No license requests found.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($submissions->hasPages())
                                    <div class="p-4">
                                        {{ $submissions->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.comment')

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Success Message
            @if(session('status') == 'success')
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('message') }}",
                    confirmButtonColor: '#0d6efd'
                });
            @endif

            // Error Message
            @if(session('status') == 'error' || $errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('message') ?? $errors->first() }}",
                    confirmButtonColor: '#0d6efd'
                });
            @endif

            const licenseData = {
                'Business and Corporate': [
                    'CAC Business Name Registration',
                    'Limited Company Registration',
                    'NGO / Incorporated Trustee Registration',
                    'Tax Identification Number (TIN) Registration',
                    'VAT Registration',
                    'Business Premises Permit',
                    'Company Annual Return Filing'
                ],
                'Financial and Fintech': [
                    'Money Lending License',
                    'Microfinance Bank License',
                    'Payment Service Provider License',
                    'POS Agent Registration',
                    'Financial Consulting License',
                    'Loan Company Registration'
                ],
                'Import and Export': [
                    'Import License',
                    'Export License',
                    'Customs Clearing Agent License',
                    'Bonded Warehouse Permit',
                    'Ship Chandler License'
                ],
                'Food, Drugs and Health': [
                    'NAFDAC Product Registration',
                    'Food Business Permit',
                    'Pharmacy License',
                    'Hospital or Clinic Operating License',
                    'Medical Laboratory License'
                ],
                'Telecom and Technology': [
                    'Internet Service Provider License',
                    'Bulk SMS License',
                    'Value Added Service License',
                    'Telecom Operator License'
                ],
                'Oil and Gas': [
                    'Oil Prospecting License',
                    'Oil Mining Lease',
                    'LPG Distribution License',
                    'Gas Processing Permit'
                ],
                'Agriculture': [
                    'Fertilizer Distribution License',
                    'Agricultural Export Permit',
                    'Livestock or Veterinary Permit'
                ],
                'Education': [
                    'Private School License',
                    'Training Institute Registration',
                    'Educational Consulting License'
                ],
                'Construction and Real Estate': [
                    'Building Permit',
                    'Environmental Impact Assessment Permit',
                    'Real Estate Developer License'
                ],
                'Transport and Logistics': [
                    'Logistics or Courier License',
                    'Road Transport Operator License',
                    'Freight Forwarding License'
                ],
                'Media and Entertainment': [
                    'Broadcasting License',
                    'Event Permit',
                    'Music or Film Distribution License'
                ]
            };

            const categorySelect = document.getElementById('category');
            const licenseSelect = document.getElementById('license_type');

            categorySelect.addEventListener('change', function() {
                const category = this.value;
                const licenses = licenseData[category] || [];

                licenseSelect.innerHTML = '<option value="" disabled selected>Select specific license</option>';
                
                licenses.forEach(function(license) {
                    const option = document.createElement('option');
                    option.value = license;
                    option.textContent = license;
                    licenseSelect.appendChild(option);
                });

                licenseSelect.disabled = false;
            });

            // Form Submission Confirmation
            const licenseForm = document.getElementById('licenseForm');
            if (licenseForm) {
                licenseForm.addEventListener('submit', function(e) {
                    if (this.dataset.confirmed) return;

                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Ready to submit?',
                        text: "₦1,000 will be deducted from your wallet balance.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, submit it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.dataset.confirmed = "true";
                            this.submit();
                        }
                    });
                });
            }
            }
        });

        // Batch Status Check
        function batchCheck() {
            Swal.fire({
                title: 'Refresh Status?',
                text: "This will check for any updates on your pending license requests.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes, check now!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Checking your requests, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route('license.batch-check') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Refresh Complete',
                                text: data.message,
                                confirmButtonText: 'Reload Page'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: data.message || 'Something went wrong during the check.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'A network error occurred. Please try again.'
                        });
                    });
                }
            });
        }
    </script>
    @endpush

    @push('styles')
    <style>
        .form-select:focus, .form-control:focus {
            background-color: #fff !important;
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.1) !important;
        }
        .xsmall {
            font-size: 0.75rem;
        }
    </style>
    @endpush
</x-app-layout>
