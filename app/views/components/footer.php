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
                <div class="social-links">
                    <a href="#" class="social-link">Facebook</a>
                    <a href="#" class="social-link">Twitter</a>
                    <a href="#" class="social-link">Instagram</a>
                    <a href="#" class="social-link">LinkedIn</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/grounds">Book Ground</a></li>
                    <li><a href="/coaches">Book Coach</a></li>
                    <li><a href="/shop">Shop</a></li>
                    <li><a href="/news">News</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Services</h4>
                <ul class="footer-links">
                    <li><a href="/app/views/booking/book-ground.php">book ground</a></li>
                    <li><a href="/coaches">boook coach</a></li>
                    <li><a href="/shop">shoop</a></li>
                     <li><a href="/shop">news</a></li>
                    
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
        
    </div>
</footer>

<style>
.footer {
    background-color: #1a1a1a;
    color: #ffffff;
    margin-top: auto;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-top {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    padding: 40px 0;
}

.footer-section h3,
.footer-section h4 {
    margin-bottom: 20px;
    color: #fff;
}

.footer-logo {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.footer-logo-img {
    width: 40px;
    height: 40px;
    margin-right: 10px;
}

.footer-description {
    margin-bottom: 20px;
    color: #ccc;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s;
}

.social-link:hover {
    color: #4CAF50;
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #4CAF50;
}

.contact-info p {
    margin-bottom: 10px;
    color: #ccc;
}

.footer-bottom {
    border-top: 1px solid #333;
    padding: 20px 0;
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.footer-bottom-links {
    display: flex;
    gap: 20px;
}

.footer-bottom-links a {
    color: #ccc;
    text-decoration: none;
    font-size: 14px;
}

.footer-bottom-links a:hover {
    color: #4CAF50;
}

@media (max-width: 768px) {
    .footer-top {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
}
</style>