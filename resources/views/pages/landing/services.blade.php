<!-- Services Section -->
<section id="services" style="padding: 100px 0; background-color: #f9f9f9; position: relative;">
    <!-- Background Elements -->
    <div
        style="position: absolute; top: 0; right: 0; width: 300px; height: 300px; background: radial-gradient(circle, rgba(15, 118, 110, 0.1) 0%, rgba(255,255,255,0) 70%);">
    </div>
    <div
        style="position: absolute; bottom: 0; left: 0; width: 400px; height: 400px; background: radial-gradient(circle, rgba(15, 23, 42, 0.05) 0%, rgba(255,255,255,0) 70%);">
    </div>

    <div class="container px-0 px-md-3">
        <div class="section-title text-center mb-5" data-aos="fade-up">
            <h4 style="color: #0F766E; font-weight: 600; letter-spacing: 2px; text-transform: uppercase;">What We Do
            </h4>
            <h2 style="color: #111827; font-weight: 800; font-size: 2.5rem;">Our Premium Services</h2>
            <hr style="width: 60px; height: 3px; background: #0F766E; margin: 15px auto; border: none;">
            <p class="mt-3 text-muted" style="max-width: 600px; margin: 0 auto; font-size: 1.1rem;">
                We deliver cutting-edge identity and utility solutions designed to streamline your operations and personal needs.
            </p>
        </div>

        <div class="row g-0 g-md-4">
            <!-- Service 1: NIN Services -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card-premium rounded-0 rounded-md-4">
                    <div class="icon-wrapper">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3>NIN Services</h3>
                    <p>Comprehensive National Identity Number services including modification, validation, and official enrollment support.</p>
                    <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Service 2: BVN Services -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card-premium">
                    <div class="icon-wrapper">
                        <i class="fas fa-university"></i>
                    </div>
                    <h3>BVN Services</h3>
                    <p>Expert assistance with Bank Verification Number linking, modification, and secure identity search services.</p>
                    <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Service 3: Utility Bills -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card-premium">
                    <div class="icon-wrapper">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Utility Payments</h3>
                    <p>Fast and convenient options for electricity, TV cable subscriptions, and other essential utility bill payments.</p>
                    <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Service 4: Identity Verification -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="service-card-premium">
                    <div class="icon-wrapper">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h3>Identity Verification</h3>
                    <p>Secure and reliable identity validation solutions for businesses via professional CRM and validation systems.</p>
                    <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Service 5: Data & Airtime -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="service-card-premium">
                    <div class="icon-wrapper">
                        <i class="fas fa-signal"></i>
                    </div>
                    <h3>Data & Airtime</h3>
                    <p>Affordable SME data bundles and instant airtime top-ups across all major telecommunication networks instantly.</p>
                    <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Service 6: Consultancy -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                <div class="service-card-premium">
                    <div class="icon-wrapper">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Business Solutions</h3>
                    <p>Tailored digital solutions and consultancy designed to help businesses optimize operations and scale effectively.</p>
                    <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .service-card-premium {
            background: #fff;
            padding: 40px 30px;
            /* border-radius: 20px; handled by bootstrap classes */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.03);
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
            background: linear-gradient(135deg, #0F766E 0%, #0D5A54 100%);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            z-index: -1;
            /* border-radius: 20px; handled by bootstrap classes */
        }

        .service-card-premium:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(15, 118, 110, 0.15);
        }

        .service-card-premium:hover::before {
            height: 100%;
        }

        .icon-wrapper {
            width: 70px;
            height: 70px;
            background: rgba(15, 118, 110, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            transition: all 0.4s ease;
        }

        .icon-wrapper i {
            font-size: 1.8rem;
            color: #0F766E;
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
            color: #0F766E;
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