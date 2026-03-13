<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Quick Slip - {{ $title ?? 'Welcome Sir All our services is going smothly' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo/favicon.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/logo/favicon.png') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Open Graph / WhatsApp Meta Tags -->
        <meta property="og:title" content="Quick Slip - Innovative Digital Solutions">
        <meta property="og:description" content="Empowering northern Nigeria through innovative digital solutions and smart technology services.">
        <meta property="og:image" content="{{ asset('assets/img/logo/logo.png') }}">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:type" content="website">
    </head>

    <body class="bg-white">
        <div id="global-loader" style="display: none;">
            <div class="page-loader"></div>
        </div>

        <!-- Header -->
        <header>
            <div class="container header-container">
                <a href="#" class="logo">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Quick Slip" style="height: 50px; margin-right: 10px;">
                </a>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
                <nav>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#partners">Partners</a></li>
                        <li><a href="#support">Support</a></li>
                        <li><a href="#about-us">About Us</a></li>
                        <li><a href="{{route ('login')}}" class="btn btn-primary text-white">Get Started</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero" id="home" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(136, 92, 10, 0.5)), 
             url('{{ asset('assets/images/logo/hero.png') }}') no-repeat center center/cover; min-height: 100vh; display: flex; align-items: center;">
            <div class="container hero-content text-center">
                <h1 class="mb-4" data-aos="fade-down" data-aos-duration="1000" style="font-size: 4rem; font-weight: 800; color: #885c0a; text-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                    Agency and Verification Services for Your Business
                </h1>
                <p class="mb-5" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000" style="font-size: 1.4rem; max-width: 850px; margin: 0 auto; line-height: 1.6; color: rgba(255, 255, 255, 0.95); text-shadow: 0 2px 10px rgba(0,0,0,0.4);">
                    Experience premium agency services tailored for growth. Affordable, reliable, and innovative solutions at your fingertips.
                </p>
                <div class="hero-btns" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                    <a href="{{route ('register')}}" class="btn btn-primary btn-lg me-3" style="padding: 15px 40px; font-size: 1.1rem;">
                        Get Started
                    </a>
                    <a href="{{route ('login')}}" class="btn btn-secondary-outline btn-lg" style="padding: 15px 40px; font-size: 1.1rem; border-color: #885c0a; color: #fff;">
                        Login Now
                    </a>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        @include('pages.landing.services')

        <!-- Testimonials Section -->
        <section id="testimonials" class="testimonials-section" style="padding: 100px 0; background: linear-gradient(135deg, #F7F3ED 0%, #f0ece1 100%);">
            <!-- Background Patterns -->
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.05; background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            

            <div class="container" style="position: relative; z-index: 2;">
                <div class="section-title text-center mb-5" data-aos="fade-up">
                    <h4 style="color: #885c0a; font-weight: 600; letter-spacing: 3px; text-transform: uppercase; font-size: 0.9rem;">Testimonials</h4>
                    <h2 style="color: #111827; font-weight: 800; font-size: 2.8rem; margin-top: 10px;">Trusted by Leaders</h2>
                    <hr style="width: 80px; height: 4px; background: #885c0a; margin: 20px auto; border: none; border-radius: 2px;">
                    <p class="text-muted" style="max-width: 650px; margin: 0 auto; font-size: 1.15rem;">
                        See what our partners and clients have to say about their experience working with Digital Verify.
                    </p>
                </div>

                <div class="row g-4">
                    <!-- Testimonial 1 -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="testimonial-card-premium">
                            <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                            <p class="review-text">"Quick Slip transformed our operations with cutting-edge solutions. Their support team is always responsive and professional! Truly a game changer for our business."</p>
                            <div class="reviewer-info">
                                <img src="{{ asset('assets/images/avatar/avatar-8.jpg') }}" alt="Abdulrahman Musa">
                                <div>
                                    <h4>Abdulrahman Musa</h4>
                                    <span>CEO, NorthernTech</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="testimonial-card-premium">
                            <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                            <p class="review-text">"Working with Quick Slip has been a seamless experience. Their expertise and attention to detail are unmatched. They delivered exactly what we needed, on time."</p>
                            <div class="reviewer-info">
                                <img src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt="Fatima Bello">
                                <div>
                                    <h4>Fatima Bello</h4>
                                    <span>Manager, Quick Slip Logistics</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="testimonial-card-premium">
                            <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                            <p class="review-text">"The quality of service and support we've received from Quick Slip is outstanding. Highly recommended for any business looking to scale digitally."</p>
                            <div class="reviewer-info">
                                <img src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt="Emeka Johnson">
                                <div>
                                    <h4>Emeka Johnson</h4>
                                    <span>IT Director, Quick Slip Ltd</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @include('pages.landing.support')
        
        <!-- Footer -->
        <footer>
            <!-- Background Decoration -->
            <div style="position: absolute; top: 0; right: 0; width: 400px; height: 400px; background: radial-gradient(circle, rgba(136, 92, 10, 0.1) 0%, rgba(0,0,0,0) 70%);"></div>

            <div class="container" style="position: relative; z-index: 2;">
                <div class="row g-5">
                    <!-- Company Info -->
                    <div class="col-lg-4 col-md-6">
                        <h2 style="color: #885c0a; font-weight: 800; margin-bottom: 25px; font-size: 2rem;">Quick Slip</h2>
                        <p style="color: rgba(255,255,255,0.7); line-height: 1.8; margin-bottom: 30px;">
                            Providing innovative technology solutions to help businesses thrive in the digital world. We are committed to excellence and sustainable growth in Northern Nigeria.
                        </p>
                        <div class="social-links">
                            <a href="#" class="footer-social"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="footer-social"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="footer-social"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="footer-social"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6">
                        <h3 style="color: #fff; font-size: 1.2rem; font-weight: 700; margin-bottom: 25px; display: inline-block;">Quick Links</h3>
                        <div style="width: 40px; height: 3px; background: #885c0a; margin-bottom: 20px;"></div>
                        <ul class="footer-links list-unstyled">
                            <li><a href="#home">Home</a></li>
                            <li><a href="#services">Services</a></li>
                            <li><a href="#partners">Partners</a></li>
                            <li><a href="#support">Support</a></li>
                            <li><a href="#about-us">About Us</a></li>
                            <li><a href="javascript:void(0)" onclick="openDataProtectionModal()">Privacy Policy</a></li>
                        </ul>
                    </div>
                    
                    <!-- Services -->
                    <div class="col-lg-3 col-md-6">
                        <h3 style="color: #fff; font-size: 1.2rem; font-weight: 700; margin-bottom: 25px; display: inline-block;">Our Services</h3>
                        <div style="width: 40px; height: 3px; background: #885c0a; margin-bottom: 20px;"></div>
                        <ul class="footer-links list-unstyled">
                            <li><a href="#">Web Development</a></li>
                            <li><a href="#">Mobile Apps</a></li>
                            <li><a href="#">BVN & NIN Services</a></li>
                            <li><a href="#">Digital Marketing</a></li>
                            <li><a href="#">IT Consultancy</a></li>
                        </ul>
                    </div>
                    
                    <!-- Contact -->
                    <div class="col-lg-3 col-md-6">
                        <h3 style="color: #fff; font-size: 1.2rem; font-weight: 700; margin-bottom: 25px; display: inline-block;">Contact Us</h3>
                        <div style="width: 40px; height: 3px; background: #885c0a; margin-bottom: 20px;"></div>
                        <ul class="footer-contact list-unstyled">
                            <li style="margin-bottom: 20px; display: flex;">
                                <div style="width: 30px; color: #885c0a; margin-top: 2px;"><i class="fas fa-map-marker-alt"></i></div>
                                <span style="color: rgba(255,255,255,0.8);">No 5 BMT GARDEN WUSE 2 ABUJA</span>
                            </li>
                            <li style="margin-bottom: 20px; display: flex;">
                                <div style="width: 30px; color: #885c0a; margin-top: 2px;"><i class="fas fa-phone"></i></div>
                                <span style="color: rgba(255,255,255,0.8);">07048932365</span>
                            </li>
                            <li style="margin-bottom: 20px; display: flex;">
                                <div style="width: 30px; color: #885c0a; margin-top: 2px;"><i class="fas fa-envelope"></i></div>
                                <span style="color: rgba(255,255,255,0.8);">help@quickslio.com.ng</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <hr style="border-color: rgba(255,255,255,0.1); margin: 60px 0 30px;">
                
                <div class="footer-bottom text-center">
                    <p style="color: rgba(255,255,255,0.6); margin: 0;">&copy; {{ date('Y') }} Quick Slip. All rights reserved. | Designed with <i class="fas fa-heart" style="color: #885c0a;"></i> by Quick Slip Team.</p>
                </div>
            </div>
        </footer>

        <!-- Privacy Banner (Footer) -->
        <div class="privacy-banner" id="privacyBanner">
            <div class="banner-content">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <i class="fas fa-shield-alt privacy-icon"></i>
                    <div class="privacy-text">
                        <h5>Your Privacy Matters</h5>
                        <p>We value your privacy and are committed to protecting your personal data in compliance with the Data Protection Regulation (NDPR). We collect data to provide verification services.</p>
                    </div>
                </div>
                <div class="banner-actions">
                    <a href="javascript:void(0)" class="link-primary" onclick="openDataProtectionModal()">Read Full Policy</a>
                    <button type="button" class="btn btn-outline-secondary" onclick="rejectPrivacy()">Reject</button>
                    <button type="button" class="btn btn-primary" onclick="acceptPrivacyPolicy()">Accept & Continue</button>
                </div>
            </div>
        </div>

        <!-- Data Protection Modal -->
        <div class="modal fade data-protection-modal" id="dataProtectionModal" tabindex="-1" aria-labelledby="dataProtectionModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dataProtectionModalLabel"><i class="fas fa-shield-alt me-2"></i> Data Protection & Privacy Policy</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo" style="height: 60px;">
                            <h4 class="mt-3 text-dark">Quick Slip Data Privacy Commitment</h4>
                        </div>

                        <p class="lead text-center mb-4" style="font-size: 1.1rem; color: #555;">
                            At Quick Slip, we are committed to protecting your personal data in compliance with the 
                            <strong>Nigeria Data Protection Regulation (NDPR) 2019</strong>.
                        </p>

                        <div class="policy-section">
                            <h5>1. Introduction</h5>
                            <p>This Privacy Policy explains how Quick Slip collects, uses, and protects your personal information when you use our digital solutions, including our website, mobile applications, and NIN/BVN services.</p>
                        </div>
                        
                        <div class="policy-section">
                            <h5>2. Data Collection</h5>
                            <p>We collect information you provide directly to us, such as when you create an account, request services, or contact customer support.</p>
                        </div>

                        <div class="alert alert-warning mt-4 text-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            By clicking "I Agree & Continue", you acknowledge that you have read and understood this Privacy Policy and agree to our Terms of Service.
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('register') }}" class="btn btn-primary px-5 py-2 fw-bold" onclick="acceptPrivacyPolicy()">
                            I Agree & Continue <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

        <!-- WhatsApp Floating Button -->
        <a href="https://wa.me/+2347067673296" class="whatsapp-float" target="_blank" title="Chat with us on WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <script src="{{ asset('assets/js/landing.js') }}"></script>
    </body>
</html>
