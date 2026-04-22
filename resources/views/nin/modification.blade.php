<x-app-layout>
    <title>Smart Idea - {{ $title ?? 'NIN Modification' }}</title>

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">NIN Modification Request</h3>
                        <p class="text-muted small mb-0">Update your National Identification Number details professionally.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-3">
            <div class="row">

                {{-- Request Form Column --}}
                <div class="col-xl-6 mb-4">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>New Request</h5>
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

                            <form method="POST" action="{{ route('nin-modification.store') }}" class="row g-4">
                                @csrf

                                {{-- Modification Type --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Modification Field <span class="text-danger">*</span></label>
                                    <select class="form-select border-primary-subtle" name="service_field_id" id="service_field" required>
                                        <option value="">-- Choose Field to Update --</option>
                                        @foreach ($serviceFields as $field)
                                            @php $price = $field->getPriceForUserType(auth()->user()->role); @endphp
                                            <option value="{{ $field->id }}"
                                                    data-price="{{ $price }}"
                                                    data-description="{{ $field->description }}"
                                                    {{ old('service_field_id') == $field->id ? 'selected' : '' }}>
                                                {{ $field->field_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 text-muted fst-italic small" id="field-description"></div>
                                </div>

                                {{-- NIN --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">NIN Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                                        <input class="form-control" name="nin" type="text" placeholder="11-digit NIN"
                                               value="{{ old('nin') }}" maxlength="11" minlength="11" pattern="[0-9]{11}" required>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#sampleInfoModal">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Generic Description --}}
                                <div class="col-12" id="generic-data-info">
                                    <label class="form-label fw-bold">Modification Details <span class="text-danger">*</span></label>
                                    <textarea class="form-control border-primary-subtle" name="description" id="description-field"
                                              rows="4" placeholder="Briefly describe what needs to be changed..."
                                              required>{{ old('description') }}</textarea>
                                </div>

                                {{-- DOB Wizard --}}
                                <div class="col-12 d-none" id="dob-wizard">
                                    <div class="card bg-light border-0 rounded-3">
                                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold text-primary mb-0"><i class="bi bi-clipboard-data me-2"></i>Attestation Wizard</h6>
                                            <span class="badge bg-primary-subtle text-primary border" id="step-badge">8 Steps</span>
                                        </div>
                                        <div class="card-body">
                                            <div class="progress mb-4" style="height: 10px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                     id="wizard-progress" role="progressbar" style="width: 12.5%;"></div>
                                            </div>

                                            {{-- Steps 1 through 8 --}}
                                            @for ($i = 1; $i <= 8; $i++)
                                                <div class="wizard-step {{ $i > 1 ? 'd-none' : '' }}" id="step-{{ $i }}">
                                                    <h6 class="text-dark fw-bold mb-3 border-bottom pb-2">
                                                        @if($i==1) Personal Details @elseif($i==2) DOB & Origin @elseif($i==3) Residence @elseif($i==4) Birth Info
                                                        @elseif($i==5) Socio-Economic @elseif($i==6) Father's Details @elseif($i==7) Mother's Details @elseif($i==8) Final Review @endif
                                                    </h6>
                                                    <div class="row g-3">
                                                        @if($i == 1)
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[first_name]" placeholder="First Name" disabled>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[surname]" placeholder="Surname" disabled>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[middle_name]" placeholder="Middle Name (Optional)" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-select form-select-sm dob-input" name="modification_data[gender]" disabled>
                                                                    <option value="">Select Gender</option>
                                                                    <option value="Male">Male</option>
                                                                    <option value="Female">Female</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-select form-select-sm dob-input" name="modification_data[marital_status]" disabled>
                                                                    <option value="">Marital Status</option>
                                                                    <option value="Single">Single</option>
                                                                    <option value="Married">Married</option>
                                                                    <option value="Divorced">Divorced</option>
                                                                </select>
                                                            </div>
                                                        @elseif($i == 2)
                                                            <div class="col-md-6">
                                                                <label class="small text-muted">New DOB</label>
                                                                <input type="date" class="form-control form-control-sm dob-input" name="modification_data[new_dob]" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="small text-muted">Nationality</label>
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[nationality]" value="Nigeria" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[state_of_origin]" placeholder="State of Origin" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[lga_of_origin]" placeholder="LGA of Origin" disabled>
                                                            </div>
                                                            <div class="col-12">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[town_of_origin]" placeholder="Town of Origin" disabled>
                                                            </div>
                                                        @elseif($i == 3)
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[residence_state]" placeholder="State (Residence)" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[residence_city]" placeholder="City (Residence)" disabled>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[residence_address]" placeholder="Full Address" disabled>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[phone_number]" placeholder="Phone Number" disabled>
                                                            </div>
                                                        @elseif($i == 4)
                                                            <div class="col-12">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[place_of_birth]" placeholder="Place of Birth" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[state_of_birth]" placeholder="State of Birth" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[lga_of_birth]" placeholder="LGA of Birth" disabled>
                                                            </div>
                                                        @elseif($i == 5)
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[occupation]" placeholder="Occupation" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[education_level]" placeholder="Education Level" disabled>
                                                            </div>
                                                            <div class="col-12">
                                                                <textarea class="form-control form-control-sm dob-input" name="modification_data[occupation_address]" placeholder="Occupation Address" disabled></textarea>
                                                            </div>
                                                            <div class="col-12">
                                                                <select class="form-select form-select-sm dob-input" name="modification_data[reason]" disabled>
                                                                    <option value="As Requirement for">As Requirement for...</option>
                                                                    <option value="Others">Others</option>
                                                                </select>
                                                            </div>
                                                        @elseif($i == 6)
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[father_surname]" placeholder="Father's Surname" disabled>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[father_firstname]" placeholder="Father's First Name" disabled>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[father_middlename]" placeholder="Father's Middle Name" disabled>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[father_town]" placeholder="Father's Origin Town" disabled>
                                                            </div>
                                                        @elseif($i == 7)
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[mother_surname]" placeholder="Mother's Surname" disabled>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[mother_firstname]" placeholder="Mother's First Name" disabled>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[mother_middlename]" placeholder="Mother's Middle Name" disabled>
                                                            </div>
                                                        @elseif($i == 8)
                                                            <div class="col-12">
                                                                <p class="text-muted small">By clicking submit, you confirm that all provided details above are correct for the NIN Modification process.</p>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <input type="text" class="form-control form-control-sm dob-input" name="modification_data[reg_centre]" placeholder="Expected Registration Centre" disabled>
                                                            </div>
                                                        @endif

                                                        <div class="col-12 d-flex justify-content-between mt-3">
                                                            @if($i > 1)
                                                                <button type="button" class="btn btn-secondary btn-sm prev-step" data-prev="step-{{ $i-1 }}"><i class="bi bi-arrow-left"></i> Back</button>
                                                            @else
                                                                <span></span>
                                                            @endif

                                                            @if($i < 8)
                                                                <button type="button" class="btn btn-primary btn-sm next-step" data-next="step-{{ $i+1 }}">Continue <i class="bi bi-arrow-right"></i></button>
                                                            @else
                                                                <button type="submit" class="btn btn-success btn-sm fw-bold px-4 shadow-sm">
                                                                    <i class="bi bi-send-check-fill me-1"></i> Confirm & Submit
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                {{-- Pricing & Balance --}}
                                <div class="col-md-6 text-center">
                                    <label class="form-label fw-bold">Service Fee</label>
                                    <div class="alert alert-secondary py-2 mb-0 border-0 shadow-sm">
                                        <span class="h5 fw-bold mb-0 text-primary" id="field-price">₦0.00</span>
                                    </div>
                                </div>

                                <div class="col-md-6 text-center">
                                    <label class="form-label fw-bold">Wallet Balance</label>
                                    <div class="alert alert-soft-success py-2 mb-0 border-0 shadow-sm">
                                        <span class="h5 fw-bold mb-0 text-success">₦{{ number_format($wallet->balance ?? 0, 2) }}</span>
                                    </div>
                                </div>

                                {{-- Terms --}}
                                <div class="col-12">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" id="termsCheckbox" type="checkbox" required>
                                        <label class="form-check-label small" for="termsCheckbox">
                                            I confirm that the provided information is accurate and agree to the modification policy.
                                        </label>
                                    </div>
                                </div>

                                {{-- Submit --}}
                                <div class="col-12 d-grid" id="generic-submit-btn">
                                    <button type="submit" class="btn btn-primary btn-lg shadow-sm hover-up">
                                        <i class="bi bi-check2-circle me-2"></i> Submit Request
                                    </button>
                                </div>

                                <div class="col-12 d-grid d-none" id="dob-proceed-btn">
                                    <button type="button" class="btn btn-primary btn-lg shadow-sm hover-up" id="proceed-attestation-btn">
                                        Start Attestation Wizard <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Submission History Column --}}
                <div class="col-xl-6">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0 text-white">
                                <i class="bi bi-clock-history me-2"></i> Request History
                            </h5>

                        </div>

                        <div class="card-body p-4">
                            <form class="row g-3 mb-4 bg-light p-3 rounded-3 border" method="GET" action="{{ route('nin-modification') }}">
                                <div class="col-md-5">
                                    <input class="form-control border-0 shadow-sm" name="search" type="text" placeholder="Search by NIN..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select border-0 shadow-sm" name="status">
                                        <option value="">All Statuses</option>
                                        @foreach (['pending','query','processing','resolved','successful','rejected'] as $status)
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

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>NIN</th>
                                            <th>Service</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($crmSubmissions as $submission)
                                            <tr>
                                                <td class="fw-bold text-muted">{{ $loop->iteration + ($crmSubmissions->currentPage() - 1) * $crmSubmissions->perPage() }}</td>
                                                <td><span class="text-primary fw-medium">{{ $submission->nin }}</span></td>
                                                <td><small class="fw-bold">{{ $submission->service_field_name ?? 'N/A' }}</small></td>
                                                <td>
                                                    <span class="badge rounded-pill bg-{{ match($submission->status) {
                                                        'resolved', 'successful' => 'success',
                                                        'processing'             => 'primary',
                                                        'rejected', 'failed'               => 'danger',
                                                        'query'                  => 'info',
                                                        default                  => 'warning',
                                                    } }} px-3">
                                                        {{ ucfirst($submission->status) }}
                                                    </span>
                                                </td>
                                                <td><small class="text-muted">{{ $submission->submission_date->format('M d, Y') }}</small></td>
                                                <td>
                                                    @php
                                                        $fileUrl = '';
                                                        if (!empty($submission->file_url)) {
                                                            $f = $submission->file_url;
                                                            if (preg_match('/^https?:\/\//', $f)) { $fileUrl = $f; }
                                                            elseif (str_starts_with($f, '/storage') || str_starts_with($f, 'storage')) { $fileUrl = asset(ltrim($f, '/')); }
                                                            else { $fileUrl = \Illuminate\Support\Facades\Storage::url($f); }
                                                        }
                                                    @endphp
                                                    <div class="d-flex gap-1">
                                                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary"
                                                                data-bs-toggle="modal" data-bs-target="#commentModal"
                                                                data-comment="{{ $submission->comment ?? 'No comment yet.' }}"
                                                                data-file-url="{{ $fileUrl }}" data-approved-by="{{ $submission->approved_by }}">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>

                                                        @if(!in_array($submission->status, ['successful', 'rejected']))
                                                            <a href="{{ route('nin-modification.check', $submission->id) }}" 
                                                               class="btn btn-sm btn-icon btn-outline-success" 
                                                               title="Check for Admin Update">
                                                                <i class="bi bi-arrow-clockwise"></i>
                                                            </a>
                                                        @elseif($submission->status === 'successful' && $fileUrl)
                                                            <a href="{{ $fileUrl }}" 
                                                               class="btn btn-sm btn-icon btn-success" 
                                                               target="_blank" 
                                                               title="Download Result">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-5">
                                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                    No modification requests found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
                                {{ $crmSubmissions->withQueryString()->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('pages.comment')

    <div class="modal fade" id="sampleInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i>Submission Help</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <h6 class="fw-bold"><i class="bi bi-lightbulb-fill me-2"></i>Pro-Tip:</h6>
                        <p class="small mb-0">For Date of Birth (DOB) modifications, you will be guided through an 8-step attestation wizard. Ensure you have all personal and family details ready.</p>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2"></i> NIN must be 11 digits.</li>
                        <li class="list-group-item d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2"></i> Processing takes 24-72 business hours.</li>
                        <li class="list-group-item d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2"></i> Wallet is charged upon successful submission.</li>
                    </ul>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom CSS --}}
    <style>
        .hover-up:hover { transform: translateY(-3px); transition: all 0.3s ease; }
        .alert-soft-success { background-color: #d1e7dd; color: #0f5132; }
        .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }
        .progress-bar { border-radius: 5px; }
        .wizard-step { padding: 10px 0; }
        .table thead th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    </style>

    <script src="{{ asset('assets/js/ninservices.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#3085d6',
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#d33',
                });
            @endif

            @if (session('status') && session('message'))
                Swal.fire({
                    icon: "{{ session('status') === 'success' ? 'success' : 'error' }}",
                    title: "{{ session('status') === 'success' ? 'Great!' : 'Oops!' }}",
                    text: "{{ session('message') }}",
                    confirmButtonColor: '#3085d6',
                });
            @endif
        });


    </script>
</x-app-layout>
