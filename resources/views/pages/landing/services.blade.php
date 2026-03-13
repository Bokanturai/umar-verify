<!-- Services Section -->
    <section id="services" style="padding: 100px 0; background-color: #f9f9f9; position: relative;">
        <!-- Background Elements -->
        <div style="position: absolute; top: 0; right: 0; width: 300px; height: 300px; background: radial-gradient(circle, rgba(0, 47, 186, 0.1) 0%, rgba(255,255,255,0) 70%);"></div>
        <div style="position: absolute; bottom: 0; left: 0; width: 400px; height: 400px; background: radial-gradient(circle, rgba(17, 24, 39, 0.05) 0%, rgba(255,255,255,0) 70%);"></div>

        <div class="container">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <h4 style="color: #885c0a; font-weight: 600; letter-spacing: 2px; text-transform: uppercase;">What We Do</h4>
                <h2 style="color: #111827; font-weight: 800; font-size: 2.5rem;">Our Premium Services</h2>
                <hr style="width: 60px; height: 3px; background: #CBA135; margin: 15px auto; border: none;">
                <p class="mt-3 text-muted" style="max-width: 600px; margin: 0 auto; font-size: 1.1rem;">
                    We deliver cutting-edge solutions designed to elevate your business and streamline your operations.
                </p>
            </div>

            <div class="row g-4">
                <!-- Service 1 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card-premium">
                        <div class="icon-wrapper">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3>Web Development</h3>
                        <p>Custom, high-performance websites and web applications built with the latest technologies to drive your digital presence.</p>
                        <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card-premium">
                        <div class="icon-wrapper">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Mobile Apps</h3>
                        <p>Intuitive and engaging mobile applications for iOS and Android that provide seamless user experiences on the go.</p>
                        <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-card-premium">
                        <div class="icon-wrapper">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h3>NIN & BVN Services</h3>
                        <p>Authorized enrollment and verification services for National Identity Number (NIN) and Bank Verification Number (BVN).</p>
                        <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Service 4 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-card-premium">
                        <div class="icon-wrapper">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Digital Marketing</h3>
                        <p>Data-driven marketing strategies, SEO, and social media campaigns to increase your visibility and drive growth.</p>
                        <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Service 5 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="service-card-premium">
                        <div class="icon-wrapper">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Cybersecurity</h3>
                        <p>Comprehensive security audits and solutions to protect your digital assets and customer data from threats.</p>
                        <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Service 6 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="service-card-premium">
                        <div class="icon-wrapper">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>IT Consultancy</h3>
                        <p>Expert advice and technical support to help you navigate the complex technology landscape and make informed decisions.</p>
                        <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .service-card-premium {
                background: #fff;
                padding: 40px 30px;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                transition: all 0.4s ease;
                height: 100%;
                border: 1px solid rgba(0,0,0,0.03);
                position: relative;
                overflow: hidden;
                z-index: 1;
            }
            
            .service-card-premium::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 0;
                background: linear-gradient(135deg, #885c0a 0%, #6b4908 100%);
                transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                z-index: -1;
                border-radius: 20px;
            }

            .service-card-premium:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 40px rgba(27, 67, 50, 0.2);
            }

            .service-card-premium:hover::before {
                height: 100%;
            }

            .icon-wrapper {
                width: 70px;
                height: 70px;
                background: rgba(136, 92, 10, 0.1);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 25px;
                transition: all 0.4s ease;
            }

            .icon-wrapper i {
                font-size: 1.8rem;
                color: #885c0a;
                transition: all 0.4s ease;
            }

            .service-card-premium:hover .icon-wrapper i {
                color: #fff;
                transform: rotateY(360deg);
            }

            .service-card-premium h3 {
                font-size: 1.5rem;
                font-weight: 700;
                color: #333;
                margin-bottom: 15px;
                transition: all 0.4s ease;
            }

            .service-card-premium:hover h3 {
                color: #fff;
            }

            .service-card-premium p {
                color: #666;
                line-height: 1.7;
                margin-bottom: 25px;
                transition: all 0.4s ease;
            }

            .service-card-premium:hover p {
                color: rgba(255, 255, 255, 0.9);
            }

            .service-link {
                color: #885c0a;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                transition: all 0.4s ease;
            }

            .service-link i {
                margin-left: 8px;
                font-size: 0.8rem;
                transition: margin-left 0.3s ease;
            }

            .service-card-premium:hover .service-link {
                color: #fff;
            }

            .service-card-premium:hover .service-link i {
                margin-left: 12px;
            }
        </style>
    </section>