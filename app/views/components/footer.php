<footer class="footer">
    <div class="footer-container">
        <!-- Footer Top -->
        <div class="footer-top">
            <div class="footer-section">
                <div class="footer-logo">
                    <img src="/public/assets/images/logo.jpeg" alt="GoPlay" class="footer-logo-img">
                    <h3>GoPlay</h3>
                </div>
                <p class="footer-description">
                    Your premier destination for sports facility booking, coach hiring, and equipment shopping.
                </p>
               
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/book-ground">Book Ground</a></li>
                    <li><a href="/book-coach">Book Coach</a></li>
                    <li><a href="/shop">Shop</a></li>
                    <li><a href="/news">News</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Services</h4>
                <ul class="footer-links">
                    <li><a href="/book-ground">Ground Booking</a></li>
                    <li><a href="/book-coach">Coach Hiring</a></li>
                    <li><a href="/shop">Equipment Shop</a></li>
                    <li><a href="/news">Sports News</a></li>
                </ul>
            </div>
            
           
            
            <div class="footer-section">
                <h4>Contact Info</h4>
                <div class="contact-info">
                    <p><strong>Phone:</strong> +94 11 123 4567</p>
                    <p><strong>Email:</strong> info@goplay.lk</p>
                    <p><strong>Address:</strong> 123 Sports Street, Colombo 03, Sri Lanka</p>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>&copy; 2024 GoPlay Sports Platform. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="/privacy">Privacy Policy</a>
                    <a href="/terms">Terms of Service</a>
                    <a href="/contact">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Unified Footer Styles with Design System */
:root {
    --primary-color: #2563eb;
    --primary-dark: #64748b;
    --primary-light: #f1f5f9;
    --secondary-color: #0891b2;
    --text-primary: #2c3e50;
    --text-secondary: #6c757d;
    --text-light: #adb5bd;
    --background-white: #ffffff;
    --background-light: #f8f9fa;
    --background-dark: #343a40;
    --border-color: #e9ecef;
    --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
    --border-radius: 8px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.footer {
    background: linear-gradient(135deg, var(--background-dark) 0%, var(--text-primary) 100%);
    color: #ffffff;
    margin-top: 3rem;
    position: relative;
    overflow: hidden;
}

.footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><path d="M0,10 Q25,0 50,10 T100,10 V20 H0 Z" fill="rgba(40,167,69,0.1)"/></svg>') repeat-x;
    background-size: 200px 20px;
    opacity: 0.3;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
    z-index: 1;
}

.footer-top {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
    padding: 3rem 0;
}

.footer-section h3,
.footer-section h4 {
    margin-bottom: 1.5rem;
    color: #fff;
    font-weight: 600;
    font-size: 1.2rem;
}

.footer-logo {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.footer-logo h3 {
    margin: 0;
    color: var(--primary-color);
    font-size: 1.5rem;
    font-weight: 700;
}

.footer-logo-img {
    width: 40px;
    height: 40px;
    margin-right: 0.75rem;
    border-radius: 50%;
    object-fit: cover;
}

.footer-description {
    margin-bottom: 1.5rem;
    color: #ccc;
    line-height: 1.6;
    max-width: 300px;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.75rem;
}

.footer-links a {
    color: #ccc;
    text-decoration: none;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0;
}

.footer-links a:hover {
    color: var(--primary-color);
    transform: translateX(5px);
}

.footer-links a::before {
    content: '→';
    opacity: 0;
    transform: translateX(-10px);
    transition: var(--transition);
}

.footer-links a:hover::before {
    opacity: 1;
    transform: translateX(0);
}

.contact-info p {
    margin-bottom: 0.75rem;
    color: #ccc;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.contact-info strong {
    color: var(--primary-color);
    min-width: 60px;
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1.5rem 0;
    background: rgba(0, 0, 0, 0.2);
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.footer-bottom-content p {
    margin: 0;
    color: #ccc;
    font-size: 0.9rem;
}

.footer-bottom-links {
    display: flex;
    gap: 1.5rem;
}

.footer-bottom-links a {
    color: #ccc;
    text-decoration: none;
    font-size: 0.9rem;
    transition: var(--transition);
    padding: 0.25rem 0;
}

.footer-bottom-links a:hover {
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .footer-container {
        padding: 0 1rem;
    }
    
    .footer-top {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 2rem 0;
    }
    
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .footer-bottom-links {
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .footer-top {
        padding: 1.5rem 0;
    }
    
    .footer-bottom-links {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>