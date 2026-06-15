<?php
session_start();
require_once 'includes/db_config.php';
$pageTitle = 'Home';
$pdo = getDBConnection();

$stmtFeatured = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.featured = 1 AND p.active = 1 ORDER BY p.created_at DESC LIMIT 8");
$featuredProducts = $stmtFeatured->fetchAll();

$stmtCategories = $pdo->query("SELECT * FROM categories ORDER BY category_id");
$categories = $stmtCategories->fetchAll();

$stmtAll = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.active = 1 ORDER BY p.created_at DESC");
$allProducts = $stmtAll->fetchAll();

include 'includes/header.php';
?>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 hero-content">
                <h1 class="fade-in">Discover Your <br>Perfect Look</h1>
                <p class="mb-4 fade-in">Premium makeup and beauty products curated for every skin tone and style. Where beauty meets confidence.</p>
                <div class="fade-in">
                    <a href="products.php" class="btn btn-glamour btn-lg me-3">Shop Now <i class="bi bi-arrow-right"></i></a>
                    <a href="about.php" class="btn btn-glamour-outline btn-lg" style="color:#fff;border-color:#fff;">Learn More</a>
                </div>
            </div>
            <div class="col-lg-7 text-center d-none d-lg-block">
                <img src="images/hero_banner.png" alt="Glamour Haven Beauty Products" class="hero-img img-fluid" style="max-height: 440px;">
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="section-heading">
            <h2>Shop by Category</h2>
            <p>Find the perfect products for every part of your beauty routine</p>
        </div>
        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
            <div class="col-lg-3 col-md-6">
                <a href="products.php?category=<?php echo $cat['category_id']; ?>" class="text-decoration-none">
                    <div class="category-card">
                        <img src="images/<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                        <div class="category-overlay">
                            <h4><?php echo htmlspecialchars($cat['name']); ?></h4>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--light-gray);">
    <div class="container">
        <div class="section-heading">
            <h2>Featured Products</h2>
            <p>Our most-loved bestsellers handpicked for you</p>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card product-card position-relative">
                    <?php if ($product['sale_price']): ?>
                        <span class="sale-badge">SALE</span>
                    <?php endif; ?>
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body">
                        <span class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                        <h5 class="product-name"><a href="product_detail.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h5>
                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <div class="product-price">
                            <?php if ($product['sale_price']): ?>
                                $<?php echo number_format($product['sale_price'], 2); ?>
                                <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php else: ?>
                                $<?php echo number_format($product['price'], 2); ?>
                            <?php endif; ?>
                        </div>
                        <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-glamour btn-sm w-100 mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="featured-banner">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2>New Customer Special</h2>
                    <p class="mb-3">Sign up today and get <strong>20% off</strong> your first order. Plus, enjoy free shipping on orders over $50!</p>
                    <a href="register.php" class="btn btn-glamour">Create Account <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="bi bi-gift" style="font-size: 6rem; color: var(--primary); opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="section-heading">
            <h2>All Products</h2>
            <p>Browse our complete collection</p>
        </div>
        <div class="row g-4">
            <?php foreach ($allProducts as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card product-card position-relative">
                    <?php if ($product['sale_price']): ?>
                        <span class="sale-badge">SALE</span>
                    <?php endif; ?>
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body">
                        <span class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                        <h5 class="product-name"><a href="product_detail.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h5>
                        <div class="product-price">
                            <?php if ($product['sale_price']): ?>
                                $<?php echo number_format($product['sale_price'], 2); ?>
                                <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php else: ?>
                                $<?php echo number_format($product['price'], 2); ?>
                            <?php endif; ?>
                        </div>
                        <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-glamour btn-sm w-100 mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--light-gray);">
    <div class="container">
        <div class="section-heading">
            <h2>Why Choose Glamour Haven</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6"><div class="value-card"><i class="bi bi-truck d-block"></i><h5>Free Shipping</h5><p class="text-muted small">On all orders over $50</p></div></div>
            <div class="col-lg-3 col-md-6"><div class="value-card"><i class="bi bi-shield-check d-block"></i><h5>100% Authentic</h5><p class="text-muted small">Guaranteed genuine products</p></div></div>
            <div class="col-lg-3 col-md-6"><div class="value-card"><i class="bi bi-arrow-repeat d-block"></i><h5>Easy Returns</h5><p class="text-muted small">30-day return policy</p></div></div>
            <div class="col-lg-3 col-md-6"><div class="value-card"><i class="bi bi-heart d-block"></i><h5>Cruelty Free</h5><p class="text-muted small">Never tested on animals</p></div></div>
        </div>
    </div>
</section>

<button id="backToTop" title="Back to Top"><i class="bi bi-chevron-up"></i></button>

<?php include 'includes/footer.php'; ?>
