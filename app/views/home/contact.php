<?php
/**
 * Public Contact Us Page
 * Submits to /api/contact (handled by AdminContactController::submitContact)
 */
$title = 'Contact Us - GoPlay';
$description = 'Get in touch with GoPlay. Send us your questions, feedback, or support requests.';
$additionalCSS = [];
$additionalJS = [];
$_base = defined('BASE_URL') ? BASE_URL : '';
?>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        .contact-hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
            color: #fff;
            padding: 60px 20px 80px;
            text-align: center;
        }
        .contact-hero h1 { font-size: 36px; font-weight: 700; margin-bottom: 12px; }
        .contact-hero p { font-size: 16px; color: #94a3b8; max-width: 500px; margin: 0 auto; }

        .contact-container {
            max-width: 1100px;
            margin: -40px auto 60px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 32px;
        }

        .contact-info-card {
            background: #fff;
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .contact-info-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
        .contact-info-card .subtitle { font-size: 14px; color: #64748b; margin-bottom: 28px; }

        .info-item { display: flex; gap: 16px; margin-bottom: 24px; }
        .info-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .info-icon.blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
        .info-icon.green { background: rgba(16,185,129,0.1); color: #10b981; }
        .info-icon.purple { background: rgba(139,92,246,0.1); color: #7c3aed; }
        .info-text h3 { font-size: 15px; font-weight: 600; margin-bottom: 4px; }
        .info-text p { font-size: 14px; color: #64748b; line-height: 1.5; }

        .social-links { display: flex; gap: 12px; margin-top: 28px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
        .social-link {
            width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            color: #475569; font-size: 16px; text-decoration: none;
            transition: all 0.2s;
        }
        .social-link:hover { background: #3b82f6; color: #fff; transform: translateY(-2px); }

        .contact-form-card {
            background: #fff;
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .contact-form-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 24px; }

        .form-row { display: flex; gap: 16px; }
        .form-group { margin-bottom: 20px; flex: 1; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px; }
        .required { color: #ef4444; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 10px;
            font-size: 14px; color: #0f172a; font-family: inherit; transition: all 0.2s;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .form-group textarea { resize: vertical; min-height: 120px; }

        .contact-submit-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 32px; background: #3b82f6; color: #fff;
            border: none; border-radius: 10px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: all 0.2s; width: 100%;
            justify-content: center;
        }
        .contact-submit-btn:hover { background: #2563eb; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.3); }
        .contact-submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .alert { padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; display: none; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        @media (max-width: 768px) {
            .contact-container { grid-template-columns: 1fr; }
            .form-row { flex-direction: column; gap: 0; }
            .contact-hero { padding: 40px 16px 60px; }
            .contact-hero h1 { font-size: 26px; }
            .contact-hero p { font-size: 14px; }
            .contact-info-card, .contact-form-card { padding: 24px; }
        }

        @media (max-width: 480px) {
            .contact-hero { padding: 32px 12px 50px; }
            .contact-hero h1 { font-size: 22px; }
            .contact-container { padding: 0 12px; margin-top: -30px; gap: 20px; }
            .contact-info-card, .contact-form-card { padding: 20px; }
            .info-icon { width: 38px; height: 38px; font-size: 16px; }
        }
    </style>

    <section class="contact-hero">
        <h1>Get In Touch</h1>
        <p>Have a question, feedback, or need support? We'd love to hear from you.</p>
    </section>

    <div class="contact-container">
        <!-- Info Card -->
        <div class="contact-info-card">
            <h2>Contact Information</h2>
            <p class="subtitle">Reach out to us through any of these channels</p>

            <div class="info-item">
                <div class="info-icon blue"><i class="fas fa-envelope"></i></div>
                <div class="info-text">
                    <h3>Email</h3>
                    <p>support@goplay.lk</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon green"><i class="fas fa-phone"></i></div>
                <div class="info-text">
                    <h3>Phone</h3>
                    <p>+94 11 234 5678</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon purple"><i class="fas fa-map-marker-alt"></i></div>
                <div class="info-text">
                    <h3>Address</h3>
                    <p>42 Sports Avenue, Colombo 03, Sri Lanka</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon blue"><i class="fas fa-clock"></i></div>
                <div class="info-text">
                    <h3>Business Hours</h3>
                    <p>Monday - Saturday: 9:00 AM - 6:00 PM</p>
                </div>
            </div>

            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="contact-form-card">
            <h2>Send Us a Message</h2>

            <div class="alert alert-success" id="successAlert">
                <i class="fas fa-check-circle"></i> Your message has been sent successfully! We'll get back to you soon.
            </div>
            <div class="alert alert-error" id="errorAlert">
                <i class="fas fa-exclamation-circle"></i> <span id="errorText">Something went wrong.</span>
            </div>

            <form id="contactForm" onsubmit="submitContactForm(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label for="contactName">Full Name <span class="required">*</span></label>
                        <input type="text" id="contactName" name="name" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label for="contactEmail">Email <span class="required">*</span></label>
                        <input type="email" id="contactEmail" name="email" placeholder="john@example.com" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contactPhone">Phone</label>
                        <input type="tel" id="contactPhone" name="phone" placeholder="077 123 4567">
                    </div>
                    <div class="form-group">
                        <label for="contactSubject">Subject <span class="required">*</span></label>
                        <select id="contactSubject" name="subject" required>
                            <option value="">Select a topic</option>
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Booking Issue">Booking Issue</option>
                            <option value="Payment Issue">Payment Issue</option>
                            <option value="Provider Registration">Provider Registration</option>
                            <option value="Bug Report">Bug Report</option>
                            <option value="Feedback">Feedback</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="contactMessage">Message <span class="required">*</span></label>
                    <textarea id="contactMessage" name="message" placeholder="Tell us how we can help you..." required></textarea>
                </div>
                <button type="submit" class="contact-submit-btn" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>

    <script>
        async function submitContactForm(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const success = document.getElementById('successAlert');
            const error = document.getElementById('errorAlert');
            success.style.display = 'none';
            error.style.display = 'none';

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            try {
                const formData = new FormData(document.getElementById('contactForm'));
                const res = await fetch((window.BASE_URL || '') + '/api/contact', { method: 'POST', body: formData });
                const result = await res.json();

                if (result.success) {
                    success.style.display = 'block';
                    document.getElementById('contactForm').reset();
                } else {
                    document.getElementById('errorText').textContent = result.message || 'Something went wrong.';
                    error.style.display = 'block';
                }
            } catch (err) {
                document.getElementById('errorText').textContent = 'Network error. Please try again.';
                error.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
            }
        }
    </script>
