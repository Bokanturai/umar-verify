<x-app-layout>
       <title>Smart Idea - Services Management</title>

    <div class="container-fluid py-4">
        
        <!-- Alerts with Animation -->
        @if (session('status') || session('error') || $errors->any())
            <div class="row animate__animated animate__fadeInDown">
                <div class="col-12">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 bg-success text-white" role="alert">
                            <i class="ti ti-circle-check me-2"></i> {{ session('status') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 bg-danger text-white" role="alert">
                            <i class="ti ti-alert-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 bg-danger text-white" role="alert">
                            <ul class="mb-0 list-unstyled">
                                @foreach ($errors->all() as $error)
                                    <li><i class="ti ti-alert-triangle me-2"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="row g-4">

            <!-- LEFT COLUMN: Profile Overview -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 glass-card">
                    <!-- Glassmorphism Background -->
                    <div class="card-header position-relative border-0" style="height: 140px; background: linear-gradient(135deg, #032f35, #10dfe6); overflow: hidden;">
                        <div class="position-absolute w-100 h-100 top-0 start-0" style="background: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.1;"></div>
                        <div class="glass-orb orb-1"></div>
                        <div class="glass-orb orb-2"></div>
                    </div>

                    <div class="card-body text-center pt-0 position-relative">
                        <!-- Profile Photo with Enhanced Styling -->
                        <div class="position-relative d-inline-block mb-3 profile-photo-wrapper" style="margin-top: -70px;">
                            <div class="profile-photo-container">
                                <img src="{{ $user->photo ? asset($user->photo) : asset('assets/img/profiles/avatar-01.jpg') }}"
                                     alt="Profile Photo"
                                     class="rounded-circle shadow-lg bg-white profile-img"
                                     style="width:140px;height:140px;object-fit:cover; border: 5px solid #fff;">
                                <div class="photo-overlay d-flex align-items-center justify-content-center rounded-circle" data-bs-toggle="modal" data-bs-target="#photoModal">
                                    <i class="ti ti-camera fs-3 text-white"></i>
                                </div>
                            </div>
                        </div>

                        <h4 class="mb-1 text-dark">{{ $user->first_name }} {{ $user->last_name }}</h4>
                        <p class="text-muted mb-3 flex-grow-1"><i class="ti ti-mail me-1"></i>{{ $user->email }}</p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 border border-primary-subtle">
                                <i class="ti ti-shield-check me-1"></i> {{ ucfirst($user->role ?? 'User') }}
                            </span>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 border border-success-subtle">
                                <i class="ti ti-wallet me-1"></i> Limit: ₦{{ number_format($user->limit, 2) }}
                            </span>
                        </div>

                        <div class="p-3 bg-light rounded-4 mb-4 border text-start">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-box bg-white shadow-sm rounded-3 me-3 p-2 text-primary">
                                    <i class="ti ti-phone fs-15"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Phone Number</small>
                                    <span class="fw-semibold">{{ $user->phone_no }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-white shadow-sm rounded-3 me-3 p-2 text-primary">
                                    <i class="ti ti-building fs-15"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Business</small>
                                    <span class="fw-semibold">{{ $user->business_name ?? 'Personal Account' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reset Buttons -->
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-outline-primary w-100 rounded-4 py-3 hover-lift" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                    <i class="ti ti-lock me-1"></i> Password
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-danger w-100 rounded-4 py-3 hover-lift" data-bs-toggle="modal" data-bs-target="#pinModal">
                                    <i class="ti ti-key me-1"></i> Trans PIN
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: User Details & Settings -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 glass-card">
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 text-dark"><i class="ti ti-user-circle me-2 text-primary"></i>Identity Details</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-primary text-decoration-none">Dashboard</a></li>
                            </ol>
                        </nav>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <div class="row g-4">
                            <!-- Detail Items as Cards -->
                            <div class="col-md-6 text-md-start">
                                <div class="p-3 border rounded-4 hover-shadow transition-all bg-white shadow-sm">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ti ti-id me-2 text-primary"></i>
                                        <small class="text-muted text-uppercase tracking-wider fs-xs">Full Name</small>
                                    </div>
                                    <div class="text-dark fs-14">{{ $user->first_name }} {{ $user->middle_name ? $user->middle_name . ' ' : '' }}{{ $user->last_name }}</div>
                                </div>
                            </div>

                            <div class="col-md-6 text-md-start">
                                <div class="p-3 border rounded-4 hover-shadow transition-all bg-white shadow-sm">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ti ti-mail me-2 text-primary"></i>
                                        <small class="text-muted text-uppercase tracking-wider fs-xs">Email Address</small>
                                    </div>
                                    <div class="text-dark fs-14">{{ $user->email }}</div>
                                </div>
                            </div>

                            <div class="col-md-4 text-md-start">
                                <div class="p-3 border rounded-4 hover-shadow transition-all bg-white shadow-sm h-100 text-md-start">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ti ti-map-pin me-2 text-primary"></i>
                                        <small class="text-muted text-uppercase tracking-wider fs-xs">State</small>
                                    </div>
                                    <div class="text-dark fs-16">{{ $user->state ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <div class="col-md-4 text-md-start">
                                <div class="p-3 border rounded-4 hover-shadow transition-all bg-white shadow-sm h-100 text-md-start">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ti ti-building-community me-2 text-primary"></i>
                                        <small class="text-muted text-uppercase tracking-wider fs-xs">Local Government</small>
                                    </div>
                                    <div class="text-dark fs-16">{{ $user->lga ?? 'N/A' }}</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 text-md-start">
                                <div class="p-3 border rounded-4 hover-shadow transition-all bg-white shadow-sm h-100 text-md-start">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ti ti-calendar-event me-2 text-primary"></i>
                                        <small class="text-muted text-uppercase tracking-wider fs-xs">Member Since</small>
                                    </div>
                                    <div class="text-dark fs-16">{{ $user->created_at->format('M Y') }}</div>
                                </div>
                            </div>

                            <div class="col-12 text-md-start">
                                <div class="p-4 border rounded-4 hover-shadow transition-all bg-primary-subtle border-primary-subtle text-md-start">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ti ti-map-2 me-2 text-primary fs-16"></i>
                                        <small class="text-primary text-uppercase tracking-wider fs-xs">Residential Address</small>
                                    </div>
                                    <div class="text-dark fs-16">{{ $user->address ?? 'No address provided in profile' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 text-md-start">
                            <h5 class="mb-4 text-dark"><i class="ti ti-settings-2 me-2 text-primary"></i>Quick Actions</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-4 border rounded-4 bg-light position-relative overflow-hidden group hover:bg-white transition-all shadow-sm">
                                        <div class="d-flex align-items-center position-relative">
                                            <div class="bg-primary text-white p-3 rounded-4 me-3">
                                                <i class="ti ti-id-badge-2 fs-3"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">KYC Verification</h6>
                                                <p class="text-muted small mb-0">Update your identity documents</p>
                                            </div>
                                        </div>
                                        <a href="#" class="stretched-link" data-bs-toggle="modal" data-bs-target="#forceProfileModal"></a>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-start">
                                    <div class="p-4 border rounded-4 bg-light position-relative overflow-hidden group hover:bg-white transition-all shadow-sm text-md-start">
                                        <div class="d-flex align-items-center position-relative">
                                            <div class="bg-secondary text-white p-3 rounded-4 me-3">
                                                <i class="ti ti-help-hexagon fs-3"></i>
                                            </div>
                                            <div class="text-md-start">
                                                <h6 class="mb-1">Support Center</h6>
                                                <p class="text-muted small mb-0">Need help? Contact our team</p>
                                            </div>
                                        </div>
                                        <a href="#" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS -->
    
    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-5 shadow-2xl border-0 bg-white">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title text-dark d-flex align-items-center">
                        <span class="bg-primary-subtle p-2 rounded-3 me-2"><i class="ti ti-camera text-primary"></i></span>
                        Update Profile Photo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 text-center">
                        <div class="upload-zone p-5 border-2 border-dashed rounded-5 mb-4 bg-light hover:bg-primary-subtle transition-all cursor-pointer position-relative">
                            <div id="uploadPlaceholder">
                                <i class="ti ti-cloud-upload text-primary display-4 mb-3"></i>
                                <h6 class="mb-2">Drop your image here</h6>
                                <p class="text-muted small mb-3">or click to browse from files</p>
                            </div>
                            <img id="photoPreview" src="#" alt="Preview" class="d-none rounded-4 shadow-sm" style="max-width: 100%; max-height: 200px; object-fit: contain;">
                            <input type="file" name="photo" class="form-control d-none" id="photoInput" accept="image/*" required onchange="previewImage(this)">
                            <button type="button" class="btn btn-primary rounded-pill px-4 btn-sm mt-3" onclick="document.getElementById('photoInput').click()">Select File</button>
                        </div>
                        <script>
                            function previewImage(input) {
                                if (input.files && input.files[0]) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        document.getElementById('uploadPlaceholder').classList.add('d-none');
                                        var preview = document.getElementById('photoPreview');
                                        preview.src = e.target.result;
                                        preview.classList.remove('d-none');
                                    }
                                    reader.readAsDataURL(input.files[0]);
                                }
                            }
                        </script>
                        <div class="text-start p-3 bg-light rounded-4">
                            <small class="text-muted d-block mb-1"><i class="ti ti-info-circle me-1"></i>Requirements:</small>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Formats: JPG, PNG, WEBP</li>
                                <li>Max size: 2MB</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-5 shadow-2xl border-0">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title text-dark d-flex align-items-center">
                        <span class="bg-primary-subtle p-2 rounded-3 me-2"><i class="ti ti-lock text-primary"></i></span>
                        Security Upgrade
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small text-muted text-uppercase">Current Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-4"><i class="ti ti-shield-lock text-muted"></i></span>
                                <input type="password" name="current_password" class="form-control bg-light border-0 rounded-end-4 p-3 shadow-none" required placeholder="••••••••">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted text-uppercase">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-4"><i class="ti ti-lock-cog text-muted"></i></span>
                                <input type="password" name="password" class="form-control bg-light border-0 rounded-end-4 p-3 shadow-none" required placeholder="••••••••">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted text-uppercase">Verify New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-4"><i class="ti ti-lock-check text-muted"></i></span>
                                <input type="password" name="password_confirmation" class="form-control bg-light border-0 rounded-end-4 p-3 shadow-none" required placeholder="••••••••">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill p-3 text-md-start">
                            <span class="d-flex align-items-center justify-content-center">
                                <i class="ti ti-refresh me-2"></i> Update Security Credentials
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PIN Modal -->
    <div class="modal fade" id="pinModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-5 shadow-2xl border-0">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title text-dark d-flex align-items-center">
                        <span class="bg-danger-subtle p-2 rounded-3 me-2"><i class="ti ti-key text-danger"></i></span>
                        Transaction Security
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.pin') }}">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 rounded-4 mb-4 d-flex">
                            <i class="ti ti-alert-triangle-filled fs-4 me-2"></i>
                            <div class="small">The transaction PIN is required for sending funds and making purchases. <strong>Never share it with anyone.</strong></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted text-uppercase">Account Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-4"><i class="ti ti-lock text-muted"></i></span>
                                <input type="password" name="current_password" class="form-control bg-light border-0 rounded-end-4 p-3 shadow-none" required placeholder="Enter login password">
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label small text-muted text-uppercase">New 5-Digit PIN</label>
                                <input type="password" name="pin" maxlength="5" pattern="\d{5}" class="form-control bg-light border-0 rounded-4 p-3 shadow-none text-center fs-4 tracking-widest" required placeholder="•••••">
                            </div>
                            <div class="col-6 text-md-start">
                                <label class="form-label small text-muted text-uppercase">Verify PIN</label>
                                <input type="password" name="pin_confirmation" maxlength="5" pattern="\d{5}" class="form-control bg-light border-0 rounded-4 p-3 shadow-none text-center fs-4 tracking-widest text-md-start" required placeholder="•••••">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 rounded-pill p-3">
                            <i class="ti ti-shield-check me-2"></i> Set Secure Transaction PIN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .glass-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(30px);
            z-index: 0;
        }
        
        .orb-1 {
            width: 150px;
            height: 150px;
            background: rgba(16, 223, 230, 0.3);
            top: -50px;
            right: -20px;
        }
        
        .orb-2 {
            width: 100px;
            height: 100px;
            background: rgba(3, 47, 53, 0.2);
            bottom: -30px;
            left: -10px;
        }
        
        .profile-photo-container {
            position: relative;
            cursor: pointer;
            transition: all 0.4s ease;
        }
        
        .profile-photo-container:hover .profile-img {
            transform: scale(1.05);
            filter: brightness(0.8);
        }
        
        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 2;
        }
        
        .profile-photo-container:hover .photo-overlay {
            opacity: 1;
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        
        .hover-shadow:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
            border-color: var(--bs-primary) !important;
        }
        
        .tracking-wider { letter-spacing: 0.1em; }
        .fs-xs { font-size: 0.7rem; }
        .transition-all { transition: all 0.3s ease; }
        .cursor-pointer { cursor: pointer; }
        
        .upload-zone:hover {
            background-color: #f0f7f8;
            border-color: #10dfe6;
        }
        
        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .tracking-widest { letter-spacing: 0.5em; }
    </style>
    @endpush
</x-app-layout>


