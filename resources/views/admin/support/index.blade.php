<x-app-layout>
    <title>Smart Idea - Support Center</title>

    <div class="page-body">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-title mb-4">
                <div class="row">
                    <div class="col-12">
                        <h3 class="fw-bold text-primary">Support & Help Center</h3>
                        <p class="text-muted small mb-0">
                            Get in touch with us or find answers to common questions.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                
                <!-- Social Media Channels (Left Column) -->
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-people-fill me-2"></i>Connect With Us</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-4">Follow us on social media for updates, or reach out directly for support.</p>
                            
                            <div class="d-grid gap-3">
                                <!-- WhatsApp -->
                                <a href="#" target="_blank" class="btn btn-outline-success border-2 fw-semibold d-flex align-items-center justify-content-center py-2">
                                    <i class="bi bi-whatsapp fs-5 me-2"></i> WhatsApp
                                </a>

                                <!-- Facebook -->
                                <a href="#" target="_blank" class="btn btn-outline-primary border-2 fw-semibold d-flex align-items-center justify-content-center py-2">
                                    <i class="bi bi-facebook fs-5 me-2"></i> Facebook
                                </a>

                                <!-- TikTok -->
                                <a href="#" target="_blank" class="btn btn-outline-dark border-2 fw-semibold d-flex align-items-center justify-content-center py-2">
                                    <i class="bi bi-tiktok fs-5 me-2"></i> TikTok
                                </a>

                                <!-- X (Twitter) -->
                                <a href="#" target="_blank" class="btn btn-outline-dark border-2 fw-semibold d-flex align-items-center justify-content-center py-2">
                                    <i class="bi bi-twitter-x fs-5 me-2"></i> X (Twitter)
                                </a>
                            </div>

                            <div class="mt-4 p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-envelope me-2"></i>Email Support</h6>
                                <p class="small text-muted mb-0">support@smartlink.com.ng</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQs (Right Column) -->
                <div class="col-12 col-md-8">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-question-circle me-2"></i>Frequently Asked Questions</h5>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="faqAccordion">
                                
                                <!-- FAQ Item 1 -->
                                <div class="accordion-item border-0 mb-2 shadow-sm rounded overflow-hidden">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            How do I fund my wallet?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            To fund your wallet, navigate to the dashboard and copy your dedicated virtual account number. Transfer any amount to this account number from your bank app, and your wallet will be credited instantly.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ Item 2 -->
                                <div class="accordion-item border-0 mb-2 shadow-sm rounded overflow-hidden">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            What should I do if a transaction involves a failed status but I was debited?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            If you were debited for a failed transaction, please wait for 24 hours for an automatic reversal. If it's not reversed, contact our support team via WhatsApp with your transaction reference and proof of payment.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ Item 3 -->
                                <div class="accordion-item border-0 mb-2 shadow-sm rounded overflow-hidden">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            How can I reset my transaction PIN?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            Go to your Profile page and select the "Security" tab. There you will find an option to change or reset your transaction PIN. You may need your login password to confirm the change.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ Item 4 -->
                                <div class="accordion-item border-0 mb-2 shadow-sm rounded overflow-hidden">
                                    <h2 class="accordion-header" id="headingFour">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                            Can I upgrade my account level?
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            Yes, you can upgrade your account to become an Agent or Vendor. This usually attracts cheaper rates on services. Contact support for more details on the requirements and benefits.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ Item 5 -->
                                <div class="accordion-item border-0 mb-2 shadow-sm rounded overflow-hidden">
                                    <h2 class="accordion-header" id="headingFive">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                            Is my personal information safe?
                                        </button>
                                    </h2>
                                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            Absolutely. We use industry-standard encryption to protect your personal and financial data. We do not share your information with third parties without your consent.
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
