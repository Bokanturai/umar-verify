<x-app-layout>
    @section('title', 'Smart Idea - Fund/Debit User')

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

        {{-- Error Messages --}}
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
                                    <h4 class="mb-0">Wallet Management - Single User</h4>
                                </div>
                                <a href="{{ route('admin.wallet.bulk-fund.view') }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-users me-1"></i> Bulk Action
                                </a>
                            </div>
                        </div>

                        {{-- Transaction Form --}}
                        <form action="{{ route('admin.wallet.fund') }}" method="POST" id="walletForm">
                            @csrf
                            <input type="hidden" name="user_id" id="selectedUserId">

                            <div class="border-bottom mb-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div>
                                            
                                            {{-- Search User --}}
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <h6>Search User</h6>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="mb-3">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="ti ti-user-search"></i>
                                                            </span>
                                                            <input 
                                                                type="text" 
                                                                id="userSearch" 
                                                                class="form-control"
                                                                placeholder="Search by name, email, phone, or BVN..."
                                                                autocomplete="off"
                                                            >
                                                            <button type="button" id="searchBtn" class="btn btn-primary">
                                                                <i class="ti ti-search me-1"></i>Search
                                                            </button>
                                                        </div>
                                                        <small class="form-text text-muted">Type at least 3 characters to search.</small>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Search Results --}}
                                            <div id="searchResults" class="d-none">
                                                <div class="row">
                                                    <div class="col-md-3"></div>
                                                    <div class="col-md-9">
                                                        <div class="mb-3">
                                                            <div class="card shadow-sm">
                                                                <div class="card-header bg-light py-2">
                                                                    <small class="fw-semibold text-muted">Search Results</small>
                                                                </div>
                                                                <div class="list-group list-group-flush" 
                                                                     id="userList" 
                                                                     style="max-height:300px;overflow-y:auto;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Selected User --}}
                                            <div id="selectedUserCard" class="d-none">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <h6>Selected User</h6>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="mb-3">
                                                            <div class="d-flex align-items-center flex-wrap row-gap-3 bg-light w-100 rounded p-3">
                                                                <div id="userAvatar" class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle me-3 fw-bold" style="width:50px;height:50px;">
                                                                    U
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 id="selectedUserName" class="fw-bold mb-1"></h6>
                                                                    <p id="selectedUserEmail" class="small mb-0"></p>
                                                                </div>
                                                                <button type="button" class="btn btn-light btn-sm" onclick="clearSelection()">
                                                                    <i class="ti ti-x"></i> Clear
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Transaction Type --}}
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <h6>Transaction Type</h6>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="mb-3">
                                                        <select name="type" class="form-control">
                                                            <option value="manual_credit">Credit (Fund)</option>
                                                            <option value="manual_debit">Debit</option>
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
                                                                id="amountInput" 
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
                                                            placeholder="Enter reason for this transaction (Optional)..."></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end">
                                <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-light border me-3">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Process Transaction</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>


    @push('scripts')
    {{-- SweetAlert CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const allUsers = @json($users);
        let selectedUser = null;

        const searchInput = document.getElementById("userSearch");
        const searchBtn = document.getElementById("searchBtn");
        const searchResults = document.getElementById("searchResults");
        const userList = document.getElementById("userList");

        searchBtn.addEventListener("click", searchUsers);
        searchInput.addEventListener("keyup", e => (e.key === "Enter" && searchUsers()));

        function searchUsers() {
            const q = searchInput.value.trim().toLowerCase();
            if (q.length < 3) {
                Swal.fire({
                    icon: 'info',
                    title: 'Search Query Too Short',
                    text: 'Please enter at least 3 characters to search.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const results = allUsers.filter(u =>
                (u.first_name ?? '').toLowerCase().includes(q) ||
                (u.last_name ?? '').toLowerCase().includes(q) ||
                (u.email ?? '').toLowerCase().includes(q) ||
                (u.phone_no ?? '').toLowerCase().includes(q)
            );

            renderResults(results);
        }

        function renderResults(results) {
            searchResults.classList.remove("d-none");
            if (!results.length) {
                userList.innerHTML = `
                    <div class="list-group-item text-center py-4">
                        <i class="ti ti-user-off fs-1 text-muted mb-2"></i>
                        <p class="text-muted">No users found</p>
                    </div>`;
                return;
            }
            userList.innerHTML = results.map(u => `
                <div class="list-group-item list-group-item-action user-item" onclick="selectUser(${u.id})">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;">
                            ${(u.first_name || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-semibold mb-1">${u.first_name} ${u.last_name}</h6>
                            <p class="small text-muted mb-0">${u.email} | ${u.phone_no || 'No Phone'}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-soft-success text-success">₦${parseFloat(u.balance).toLocaleString()}</span>
                            <i class="ti ti-chevron-right text-muted ms-2"></i>
                        </div>
                    </div>
                </div>
            `).join("");
        }

        function selectUser(id) {
            selectedUser = allUsers.find(u => u.id === id);
            if (!selectedUser) return;

            document.getElementById("selectedUserId").value = id;
            document.getElementById("selectedUserName").textContent = selectedUser.first_name + " " + selectedUser.last_name;
            document.getElementById("selectedUserEmail").innerHTML = `${selectedUser.email} | ${selectedUser.phone_no || 'No Phone'} | <span class="fw-bold text-success">Balance: ₦${parseFloat(selectedUser.balance).toLocaleString()}</span>`;
            document.getElementById("userAvatar").textContent = (selectedUser.first_name || 'U').charAt(0).toUpperCase();

            document.getElementById("selectedUserCard").classList.remove("d-none");
            document.getElementById("submitBtn").disabled = false;

            document.querySelectorAll(".user-item").forEach(el => el.classList.remove("selected"));
        }

        function clearSelection() {
            selectedUser = null;
            document.getElementById("selectedUserId").value = "";
            document.getElementById("selectedUserCard").classList.add("d-none");
            document.getElementById("submitBtn").disabled = true;
            searchInput.value = "";
            searchResults.classList.add("d-none");
        }

        document.getElementById("walletForm").addEventListener("submit", function(e) {
        if (!selectedUser) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'No User Selected',
                text: 'Please select a user first by searching and clicking on their name.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const amount = parseFloat(document.getElementById("amountInput").value);
        if (isNaN(amount) || amount <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Amount',
                text: 'Please enter a valid amount greater than zero.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        e.preventDefault();
        const type = document.querySelector('select[name="type"]').value;
        const actionText = type === 'manual_credit' ? 'credit' : 'debit';
        
        Swal.fire({
            title: 'Confirm Transaction',
            html: `Are you sure you want to <strong>${actionText}</strong> ₦${amount.toLocaleString()} ${actionText === 'credit' ? 'to' : 'from'} <strong>${selectedUser.first_name} ${selectedUser.last_name}</strong>'s wallet?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, process it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    </script>
    @endpush

</x-app-layout>
