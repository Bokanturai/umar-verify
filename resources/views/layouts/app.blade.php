<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="SmartHR - An advanced Bootstrap 5 admin dashboard template.">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">

      <title>Quick Slip - {{ $title ?? 'dashboard and services' }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo/logo.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('assets/img/logo/logo.png') }}" type="image/x-icon" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <!-- Custom App CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('styles')
</head>

<body>
    <!-- Loader -->
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        @include('layouts.partials.header')
        @include('layouts.partials.sidebar')

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                @isset($header)
                    <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="page-title">{{ $header }}</h3>
                            </div>
                        </div>
                    </div>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>

            <!-- ===== Footer Start ===== -->
            <footer class="footer bg-primary text-light py-3 mt-auto">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-between">
                        <!-- Left Side: Copyright -->
                        <div class="col-md-5 text-center text-md-start mb-2 mb-md-0">
                            <p class="mb-0 small">
                                © <script>document.write(new Date().getFullYear())</script> 
                                <strong class="text-white"> Digital Verify </strong>. 
                                All Rights Reserved.
                            </p>
                        </div>

                        <!-- Right Side: Social & Quick Links -->
                        <div class="col-md-6 text-center text-md-end">
                            <div class="d-inline-flex align-items-center gap-3">
                                <a href="#" target="_blank" class="text-light text-decoration-none footer-social">
                                    <i class="ti ti-brand-facebook fs-18"></i>
                                </a>
                                <a href="#" target="_blank" class="text-light text-decoration-none footer-social">
                                    <i class="ti ti-brand-twitter fs-18"></i>
                                </a>
                                <a href="#" target="_blank" class="text-light text-decoration-none footer-social">
                                    <i class="ti ti-brand-whatsapp fs-18"></i>
                                </a>
                                <a href="#" class="text-light text-decoration-none footer-social">
                                    <i class="ti ti-mail fs-18"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- ===== Footer End ===== -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->

    <!-- KYC Modal -->
    @include('pages.dashboard.kyc')

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/todo.js') }}"></script>
    <script src="{{ asset('assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('assets/js/bokanturai.js') }}"></script>
    <script src="{{ asset('assets/js/data.js') }}"></script>
    <script src="{{ asset('assets/js/bvnservices.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        // Auto-dismiss alerts after 4 seconds
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(() => {
                document.querySelectorAll('.alert.alert-dismissible').forEach(alert => {
                    if (alert && document.body.contains(alert)) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                            const alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                            if (alertInstance) {
                                alertInstance.close();
                            }
                        }
                    }
                });
            }, 4000);
        });
    </script>

    @stack('scripts')
</body>
</html>
