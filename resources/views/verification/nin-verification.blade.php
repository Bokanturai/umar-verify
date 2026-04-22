<x-app-layout>
    <title>Smart Idea - {{ $title ?? 'Verify NIN' }}</title>
    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <div class="page-title mb-3">
                <div class="row g-0 g-md-4">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">NIN Verification</h3>
                        <p class="text-muted small mb-0">Verify NIN instantly and download slips.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-0 px-md-3">
            <div class="row g-0 g-md-4 mt-3">
                <!-- NIN Verification Form -->
                <div class="col-12 col-xl-6 mb-4">
                    <div class="card shadow-sm border-0 rounded-0 rounded-md-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-0 rounded-top-md-4">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2"></i>Verify NIN</h5>
                            <span class="badge bg-light text-primary fw-semibold">Instant</span>
                        </div>

                        <div class="card-body">
                            <div class="text-center mb-3">
                                <p class="text-muted small mb-0">
                                    Enter the 11-digit NIN number below to verify.
                                </p>
                            </div>

                            {{-- Alerts --}}
                            @if (session('status') && session('message'))
                                <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 small text-start">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('nin.verification.store') }}">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">NIN Number <span class="text-danger">*</span></label>
                                        <input class="form-control text-center form-control-lg" name="number_nin" type="text"
                                            placeholder="Enter 11 Digit NIN" maxlength="11" minlength="11" pattern="[0-9]{11}"
                                            required value="{{ old('number_nin') }}">
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-info py-2 mb-0 d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold">Service Fee:</span>
                                            <strong class="fs-15">₦{{ number_format($verificationPrice ?? 0, 2) }}</strong>
                                        </div>
                                        <div class="text-end mt-1">
                                            <small class="text-muted">
                                                Wallet Balance: <strong class="text-success">₦{{ number_format($wallet->balance ?? 0, 2) }}</strong>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-12 d-grid mt-3">
                                        <button class="btn btn-primary btn-lg fw-semibold" type="submit">
                                            <i class="bi bi-search me-2"></i> Verify Now
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Verification Info -->
                <div class="col-12 col-xl-6 mt-2 mt-md-0">
                    <div class="card shadow-sm border-0 rounded-0 rounded-md-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center rounded-0 rounded-top-md-4">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Verification Result</h5>
                        </div>

                        <div class="card-body">
                            @if (session('verification'))
                                <div class="alert alert-success text-center mb-3">
                                    <i class="bi bi-check-circle-fill me-2"></i> <strong>Verification Successful!</strong>
                                </div>

                                @php
                                    $verificationData = session('verification')['data'] ?? [];
                                @endphp

                                <div class="text-center mb-4">
                                    <div class="d-inline-block p-1 border rounded bg-white shadow-sm">
                                        @if (!empty($verificationData['photo']))
                                            <img src="data:image/jpeg;base64,{{ $verificationData['photo'] }}"
                                                alt="ID Photo" class="img-fluid rounded"
                                                style="max-height:180px; min-width: 150px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('assets/images/corrupt.jpg') }}" alt="No Image"
                                                class="img-fluid rounded" style="max-height:180px;">
                                        @endif
                                    </div>
                                    <div class="mt-2 fw-bold text-muted small">PASSPORT PHOTOGRAPH</div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped align-middle">
                                        <tbody>
                                            <tr>
                                                <th class="w-40 bg-light">NIN Number</th>
                                                <td class="fw-bold text-primary">{{ $verificationData['nin'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">First Name</th>
                                                <td>{{ $verificationData['firstName'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">Last Name</th>
                                                <td>{{ $verificationData['surname'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">Middle Name</th>
                                                <td>{{ $verificationData['middleName'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">Date of Birth</th>
                                                <td>
                                                    {{ !empty($verificationData['birthDate'])
                                                        ? \Carbon\Carbon::parse($verificationData['birthDate'])->format('d M, Y')
                                                        : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">Gender</th>
                                                <td>{{ ucfirst($verificationData['gender'] ?? 'N/A') }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">Phone</th>
                                                <td>{{ $verificationData['telephoneNo'] ?? 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold mb-3 text-center text-secondary">Download Slips (Charges Apply)</h6>
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    @if (!empty($verificationData['nin']))
                                        <button onclick="confirmDownload('{{ route('standardSlip', $verificationData['nin']) }}', 'Standard Slip', {{ $standardSlipPrice ?? 0 }})" 
                                            class="btn btn-secondary btn-wave">
                                            <i class="bi bi-file-earmark-text me-1"></i> Standard <br>
                                            <small class="badge bg-dark bg-opacity-25">₦{{ number_format($standardSlipPrice ?? 0, 2) }}</small>
                                        </button>

                                        <button onclick="confirmDownload('{{ route('premiumSlip', $verificationData['nin']) }}', 'Premium Slip', {{ $premiumSlipPrice ?? 0 }})" 
                                            class="btn btn-primary btn-wave">
                                            <i class="bi bi-file-earmark-richtext me-1"></i> Premium <br>
                                            <small class="badge bg-dark bg-opacity-25">₦{{ number_format($premiumSlipPrice ?? 0, 2) }}</small>
                                        </button>
                                    @else
                                        <p class="text-muted small">NIN data not available for slip download.</p>
                                    @endif
                                </div>

                            @else
                                <div class="text-center py-5">
                                    <img src="{{ asset('assets/img/apps/thankyou.png') }}" width="120" alt="Search Icon" class="opacity-50 mb-3">
                                    <h6 class="text-muted">Verification results will appear here.</h6>
                                    <p class="small text-muted">Enter a NIN number on the left to get started.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Slip Download Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // AI Voice Notification for Success
        @if (session('status') === 'success')
            window.addEventListener('load', () => {
                const speak = () => {
                    const message = "wow verification is successful Id number is valid";
                    const utterance = new SpeechSynthesisUtterance(message);
                    
                    const voices = window.speechSynthesis.getVoices();
                    if (voices.length === 0) return false;

                    const femaleVoice = voices.find(voice => 
                        voice.name.toLowerCase().includes('female') || 
                        voice.name.toLowerCase().includes('google uk english female') ||
                        voice.name.toLowerCase().includes('samantha') ||
                        voice.name.toLowerCase().includes('victoria')
                    );
                    
                    if (femaleVoice) utterance.voice = femaleVoice;
                    utterance.rate = 1.0;
                    utterance.pitch = 1.1;
                    window.speechSynthesis.speak(utterance);
                    return true;
                };

                if (!speak()) {
                    window.speechSynthesis.onvoiceschanged = speak;
                }
            });
        @endif

        function confirmDownload(url, type, price) {
            Swal.fire({
                title: 'Are you sure?',
                text: `You will be charged ₦${price.toLocaleString()} for the ${type}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, download it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>

</x-app-layout>
