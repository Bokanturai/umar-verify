<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" style="height: 35px;">
        </a>
        <a href="{{ route('dashboard') }}" class="logo-small">
            <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" style="height: 25px;">
        </a>
        <a href="{{ route('dashboard') }}" class="dark-logo">
            <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" style="height: 35px;">
        </a>
    </div>
    <!-- /Logo -->

    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- Main Menu -->
                <li class="menu-title"><span>Main Menu</span></li>

                <li @class(['active' => Request::is('dashboard')])>
                    <a href="{{ route('dashboard') }}" @class(['active' => Request::is('dashboard')])>
                        <i class="ti ti-smart-home"></i><span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('wallet') }}" class="{{ request()->routeIs('wallet') ? 'active' : '' }}">
                        <i class="ti ti-wallet"></i><span>Wallet</span>
                    </a>
                </li>

                <!-- Utility Bill Payment -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-receipt-2"></i>
                        <span>Bill Payment</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('airtime') }}"
                                class="{{ request()->routeIs('airtime') ? 'active' : '' }}">Buy Airtime</a></li>
                        <li><a href="{{ route('buy-data') }}"
                                class="{{ request()->routeIs('buy-data') ? 'active' : '' }}">Buy Data</a></li>
                        <li><a href="{{ route('buy-sme-data') }}"
                                class="{{ request()->routeIs('buy-sme-data') ? 'active' : '' }}">Buy SME Data</a></li>
                        <li><a href="{{ route('electricity') }}"
                                class="{{ request()->routeIs('electricity') ? 'active' : '' }}">Pay Electric</a></li>
                    </ul>
                </li>

                <!-- BVN Services -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-users-group"></i>
                        <span>BVN Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('modification') }}"
                                class="{{ request()->routeIs('modification') ? 'active' : '' }}">Modification</a></li>
                        <li><a href="{{ route('bvn-crm') }}"
                                class="{{ request()->routeIs('bvn-crm') ? 'active' : '' }}">CRM</a></li>
                        <li><a href="{{ route('phone.search.index') }}"
                                class="{{ request()->routeIs('phone.search.index') ? 'active' : '' }}">BVN Search</a></li>
                        <li><a href="{{ route('bvn-user.index') }}"
                                class="{{ request()->routeIs('bvn-user.index') ? 'active' : '' }}">BVN Users</a>
                        </li>
                    </ul>
                </li>

                <!-- NIN Services -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-user-check"></i>
                        <span>NIN Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('nin-modification') }}"
                                class="{{ request()->routeIs('nin-modification') ? 'active' : '' }}">Modification</a>
                        </li>
                        <li><a href="{{ route('nin-validation') }}"
                                class="{{ request()->routeIs('nin-validation') ? 'active' : '' }}">Validation</a></li>
                        <li><a href="{{ route('ipe.index') }}"
                                class="{{ request()->routeIs('ipe.index') ? 'active' : '' }}">IPE</a></li>
                        <li><a href="{{ route('nin-personalisation.index') }}"
                                class="{{ request()->routeIs('nin-personalisation.index') ? 'active' : '' }}">Personalisation</a></li>

                    </ul>
                </li>

                <!-- Verification -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-fingerprint"></i>
                        <span>Verification</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('nin.verification.index') }}"
                                class="{{ request()->routeIs('nin.verification.index') ? 'active' : '' }}">Verify
                                NIN</a></li>
                        <li><a href="{{ route('bvn.verification.index') }}"
                                class="{{ request()->routeIs('bvn.verification.index') ? 'active' : '' }}">Verify
                                BVN</a></li>
                        <li><a href="{{ route('tin.index') }}"
                                class="{{ request()->routeIs('tin.index') ? 'active' : '' }}">Verify TIN</a></li>
                        <li><a href="{{ route('nin.phone.index') }}"
                                class="{{ request()->routeIs('nin.phone.index') ? 'active' : '' }}">Verify NIN phone
                                No</a></li>
                        <li><a href="{{ route('nin.demo.index') }}"
                                class="{{ request()->routeIs('nin.demo.index') ? 'active' : '' }}">Verify NIN Demo</a>
                        </li>
                    </ul>
                </li>

                @if(auth()->user()->role === 'super_admin')
                    <li class="menu-title"><span>Administration</span></li>

                    <!-- Admin Dashboard -->
                    <li @class(['active' => Request::is('admin/dashboard')])>
                        <a href="{{ route('admin.dashboard') }}" @class(['active' => Request::is('admin/dashboard')])>
                            <i class="ti ti-dashboard"></i><span>Admin Dashboard</span>
                        </a>
                    </li>

                    <!-- Wallet-->
                    <li class="submenu">
                        <a href="javascript:void(0);">
                            <i class="ti ti-receipt-2"></i>
                            <span>Wallet</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('wallet') }}"
                                    class="{{ request()->routeIs('wallet') ? 'active' : '' }}">Wallet</a></li>
                            <li><a href="{{ route('admin.wallet.index') }}"
                                    class="{{ request()->routeIs('admin.wallet.index') ? 'active' : '' }}">Manual C/D</a>
                            </li>
                            <li><a href="{{ route('wallet.transfer') }}"
                                    class="{{ request()->routeIs('wallet.transfer') ? 'active' : '' }}">Balance Transfer</a>
                            <li><a href="{{ route('admin.wallet.summary') }}"
                                    class="{{ request()->routeIs('admin.wallet.summary') ? 'active' : '' }}">System Summary</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Services -->
                    <li class="submenu">
                        <a href="javascript:void(0);">
                            <i class="ti ti-home-2"></i>
                            <span>Services</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li>
                                <a href="{{ route('admin.services.index') }}"
                                    class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">Services</a>
                            </li>
                            <li><a href="{{ route('admin.data-variations.index') }}"
                                    class="{{ request()->routeIs('admin.data-variations.*') ? 'active' : '' }}">Data
                                    Services</a></li>
                            <li><a href="{{ route('admin.sme-data.index') }}"
                                    class="{{ request()->routeIs('admin.sme-data.index') ? 'active' : '' }}">SME Data</a>
                            </li>
                        </ul>
                    </li>

                    <!-- User management -->
                    <li class="submenu {{ request()->routeIs('admin.users.*', 'admin.announcements.*') ? 'active submenu-open' : '' }}">
                        <a href="javascript:void(0);" class="{{ request()->routeIs('admin.users.*', 'admin.announcements.*') ? 'active' : '' }}">
                            <i class="ti ti-users-group"></i>
                            <span>Users & Comms</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('admin.users.index') }}"
                                    class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">Manage Users</a>
                            </li>
                            <li><a href="{{ route('admin.announcements.index') }}"
                                    class="{{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">Manage Notifications</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Agency Services -->
                    <li class="submenu">
                        <a href="javascript:void(0);">
                            <i class="ti ti-credit-card"></i>
                            <span>BVN Services</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('admin.bvnmod.index') }}"
                                    class="{{ request()->routeIs('admin.bvnmod.*') ? 'active' : '' }}">BVN Modification</a>
                            </li>
                            <li><a href="{{ route('admin.ninmod.index') }}"
                                    class="{{ request()->routeIs('admin.ninmod.*') ? 'active' : '' }}">NIN Modification</a>
                            </li>
                            <li><a href="{{ route('admin.validation.index') }}"
                                    class="{{ request()->routeIs('admin.validation.*') ? 'active' : '' }}">Validation</a>
                            </li>
                            <li><a href="{{ route('admin.crm.index') }}"
                                    class="{{ request()->routeIs('admin.crm.*') ? 'active' : '' }}">CRM</a></li>
                            <li><a href="{{ route('admin.bvn-search.index') }}"
                                    class="{{ request()->routeIs('admin.bvn-search.*') ? 'active' : '' }}">P/N Search</a>
                            <li><a href="{{ route('admin.bvn-user.index') }}"
                                    class="{{ request()->routeIs('admin.bvn-user.*') ? 'active' : '' }}">BVN Users</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Verification -->
                    <li class="submenu">
                        <a href="javascript:void(0);">
                            <i class="ti ti-fingerprint"></i>
                            <span>Other Services</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('admin.ninipe.index') }}"
                                    class="{{ request()->routeIs('admin.ninipe.*') ? 'active' : '' }}">NIN IPE</a></li>
                            <li><a href="{{ route('admin.vnin-nibss.index') }}"
                                    class="{{ request()->routeIs('admin.vnin-nibss.*') ? 'active' : '' }}">VNIN to NIBSS</a>
                            </li>
                            <li><a href="{{ route('admin.nin-personalisation.index') }}"
                                    class="{{ request()->routeIs('admin.nin-personalisation.*') ? 'active' : '' }}">NIN
                                    Personalisation</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Account Section -->
                <li class="menu-title"><span>Account</span></li>

                <li @class(['active' => Request::is('profile*')])>
                    <a href="{{ route('profile.edit') }}" @class(['active' => Request::is('profile*')])>
                        <i class="ti ti-settings-2"></i><span>Settings</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('refferal') }}" @class(['active' => Request::is('refferal*')])>
                        <i class="ti ti-users-group"></i><span>Referral</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('transactions') }}" @class(['active' => Request::is('transactions*')])>
                        <i class="ti ti-history"></i><span>Transactions</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('support') }}" @class(['active' => Request::is('support*')])>
                        <i class="ti ti-headset"></i><span>Support</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="confirmLogout(event, 'sidebar-logout-form')">
                        <i class="ti ti-logout"></i><span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->

<form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
    /* Clean Sidebar Styling - Green Theme */
    .sidebar {
        background: #ffffff;
        border-right: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        z-index: 1041;
    }

    @media (max-width: 991.98px) {
        .sidebar {
            margin-left: -252px;
            /* Hidden by default on mobile */
            width: 252px;
            position: fixed;
            top: 0;
            bottom: 0;
        }

        .slide-nav .sidebar {
            margin-left: 0 !important;
            /* Slide into view */
        }
    }

    .sidebar-logo {
        padding: 10px 20px;
        height: 60px;
        background: #ffffff;
        border-bottom: 1px solid #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-menu {
        padding: 10px 0;
    }

    .sidebar-menu li a {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin: 4px 15px;
        border-radius: 8px;
        color: #555;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .sidebar-menu li a i {
        font-size: 1.2rem;
        margin-right: 12px;
        width: 24px;
        text-align: center;
    }

    .sidebar-menu li a:hover {
        background: rgba(15, 118, 110, 0.05);
        color: #0F766E;
    }

    /* Active Menu Item */
    .sidebar-menu li.active>a,
    .sidebar-menu li a.active {
        background: #0F766E !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 118, 110, 0.15);
    }

    .sidebar-menu li.active>a i,
    .sidebar-menu li a.active i {
        color: #ffffff !important;
    }

    /* Submenu Styles */
    .sidebar-menu .submenu ul {
        display: none;
        background: #f9fafb;
        margin: 5px 15px;
        border-radius: 8px;
        list-style: none;
        padding: 5px 0;
    }

    .sidebar-menu .submenu.submenu-open>ul {
        display: block;
    }

    .sidebar-menu .submenu ul li a {
        padding-left: 45px;
        font-size: 0.85rem;
        margin: 2px 0;
        color: #666;
    }

    .sidebar-menu .submenu ul li a:hover {
        color: #0F766E;
        background: transparent;
        text-decoration: underline;
    }

    /* Menu Titles */
    .menu-title {
        padding: 15px 25px 5px 25px;
        font-size: 10px;
        text-transform: uppercase;
        color: #aaa;
        font-weight: 700;
        letter-spacing: 1px;
    }
    }
</style>

<script>
    function confirmLogout(event, formId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of your account.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0F766E',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>