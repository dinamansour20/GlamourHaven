    <footer class="site-footer mt-5">
        <div class="container">
            <div class="row py-5">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="footer-heading">Glamour Haven</h5>
                    <p class="footer-text">Your premier destination for high-quality makeup and beauty products. We believe everyone deserves to feel beautiful and confident.</p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-heading">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Shop All</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-heading">Categories</h5>
                    <ul class="footer-links">
                        <li><a href="products.php?category=1">Face</a></li>
                        <li><a href="products.php?category=2">Eyes</a></li>
                        <li><a href="products.php?category=3">Lips</a></li>
                        <li><a href="products.php?category=4">Tools & Brushes</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="footer-heading">Customer Service</h5>
                    <ul class="footer-links">
                        <li><i class="bi bi-envelope me-2"></i> support@glamourhaven.com</li>
                        <li><i class="bi bi-telephone me-2"></i> 1-800-GLAMOUR</li>
                        <li><i class="bi bi-geo-alt me-2"></i> 123 Beauty Boulevard, Suite 100</li>
                        <li><i class="bi bi-clock me-2"></i> Mon-Fri: 9AM - 6PM EST</li>
                    </ul>
                    <h6 class="mt-3 footer-heading">Newsletter</h6>
                    <form class="d-flex mt-2" onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
                        <input type="email" class="form-control form-control-sm me-2" placeholder="Your email" required>
                        <button type="submit" class="btn btn-sm btn-glamour">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom text-center py-3">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Glamour Haven. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
