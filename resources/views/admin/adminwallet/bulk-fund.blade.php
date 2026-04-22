<x-app-layout>
    @section('title', 'Smart Idea - Bulk Wallet Action')

    <div class="content">
        {{-- Success Message --}}
        @if (session('success'))
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
        @elseIf (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "{{ session('error') }}",
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                });
            </script>
        @endif

        {{-- Validation Error Messages --}}
        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let errorMsg = '';
                    @foreach ($errors->all() as $error)
                        errorMsg += '{{ $error }}\n';
                    @endforeach
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMsg,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                });
            </script>
        @endif
        <div class="row">
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-body">
                        <div class="border-bottom mb-3 pb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('admin.wallet.index') }}" class="btn btn-light btn-sm me-3">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>
                                    <h4 class="mb-0">Bulk Wallet Action (All Users)</h4>
                                </div>
                                <a href="{{ route('admin.wallet.fund.view') }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-user me-1"></i> Single User
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('admin.wallet.bulk-fund') }}" method="POST" id="bulkFundForm">
                            @csrf

                            <div class="border-bottom mb-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div>

                                            {{-- Warning Alert --}}
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <div class="alert alert-warning d-flex align-items-center">
                                                            <i class="ti ti-alert-triangle fs-4 me-3"></i>
                                                            <div>
                                                                <strong>Warning:</strong> This action will affect <strong>ALL</strong> registered users. Please proceed with caution.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Action Type --}}
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <h6>Action Type</h6>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="mb-3">
                                                        <select name="type" id="bulkType" class="form-control">
                                                            <option value="manual_credit">CREDIT</option>
                                                            <option value="manual_debit">DEBIT</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Amount --}}
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <h6>Amount</h6>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="mb-3">
                                                        <div class="input-group">
                                                            <span class="input-group-text">₦</span>
                                                            <input 
                                                                type="number" 
                                                                name="amount" 
                                                                id="bulkAmount"
                                                                class="form-control" 
                                                                step="0.01" 
                                                                min="0.01" 
                                                                placeholder="0.00" 
                                                                required
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Description --}}
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <h6>Description</h6>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="mb-3">
                                                        <textarea 
                                                            name="description" 
                                                            class="form-control" 
                                                            rows="3" 
                                                            placeholder="Reason for this bulk transaction (Optional)..."></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end">
                                <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-light border me-3">Cancel</a>
                                <button type="submit" class="btn btn-primary">Process Bulk Transaction</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById("bulkFundForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const amount = document.getElementById("bulkAmount").value;
            const type = document.getElementById("bulkType").value;
            const actionText = type === 'manual_credit' ? 'CREDIT' : 'DEBIT';
            const alertClass = type === 'manual_credit' ? 'text-success' : 'text-danger';

            Swal.fire({
                title: 'Confirm Bulk Action',
                html: `You are about to <strong class="${alertClass}">${actionText}</strong> ALL registered users with ₦<strong>${parseFloat(amount).toLocaleString()}</strong>.<br><br>This action cannot be undone. Are you absolutely sure?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed with ALL users!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
    @endpush

</x-app-layout>
