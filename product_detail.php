<?php
//show products page
session_start();
require_once 'includes/db_config.php';
$pdo = getDBConnection();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($productId <= 0) { header('Location: products.php'); exit; }

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) { header('Location: login.php?redirect=product_detail.php?id=' . $productId); exit; }
    $qty = max(1, (int)$_POST['quantity']);
    $stmtCheck = $pdo->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = :uid AND product_id = :pid");
    $stmtCheck->execute([':uid' => $_SESSION['user_id'], ':pid' => $productId]);
    $existing = $stmtCheck->fetch();
    if ($existing) {
        $stmtUpdate = $pdo->prepare("UPDATE cart SET quantity = quantity + :qty WHERE cart_id = :cid");
        $stmtUpdate->execute([':qty' => $qty, ':cid' => $existing['cart_id']]);
    } else {
        $stmtInsert = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:uid, :pid, :qty)");
        $stmtInsert->execute([':uid' => $_SESSION['user_id'], ':pid' => $productId, ':qty' => $qty]);
    }
    $message = 'Product added to cart successfully!';
    $messageType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
    $rating = max(1, min(5, (int)$_POST['rating']));
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmtReview = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (:pid, :uid, :rating, :comment)");
        $stmtReview->execute([':pid' => $productId, ':uid' => $_SESSION['user_id'], ':rating' => $rating, ':comment' => $comment]);
        $message = 'Thank you for your review!';
        $messageType = 'success';
    }
}

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = :id AND p.active = 1");
$stmt->execute([':id' => $productId]);
$product = $stmt->fetch();
if (!$product) { header('Location: products.php'); exit; }
$pageTitle = $product['name'];

$stmtReviews = $pdo->prepare("SELECT r.*, u.first_name, u.last_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.product_id = :pid ORDER BY r.created_at DESC");
$stmtReviews->execute([':pid' => $productId]);
$reviews = $stmtReviews->fetchAll();

$avgRating = 0;
if (count($reviews) > 0) { $avgRating = round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1); }

$stmtRelated = $pdo->prepare("SELECT * FROM products WHERE category_id = :cid AND product_id != :pid AND active = 1 LIMIT 4");
$stmtRelated->execute([':cid' => $product['category_id'], ':pid' => $productId]);
$relatedProducts = $stmtRelated->fetchAll();

include 'includes/header.php';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php">Shop</a></li>
            <li class="breadcrumb-item"><a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo $message; ?>
            <?php if ($messageType === 'success' && isset($_POST['add_to_cart'])): ?><a href="cart.php" class="alert-link ms-2">View Cart</a><?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-5">
        <div class="col-lg-6">
            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-detail-img">
        </div>
        <div class="col-lg-6 product-detail-info">
            <span class="text-uppercase small" style="letter-spacing: 2px; color: var(--primary-dark);"><?php echo htmlspecialchars($product['brand']); ?></span>
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="mb-3">
                <span class="product-rating"><?php for ($i = 1; $i <= 5; $i++): ?><i class="bi bi-star<?php echo $i <= round($avgRating) ? '-fill' : ''; ?>"></i><?php endfor; ?></span>
                <span class="text-muted ms-2">(<?php echo count($reviews); ?> review<?php echo count($reviews) != 1 ? 's' : ''; ?>)</span>
            </div>
            <div class="mb-3">
                <?php if ($product['sale_price']): ?>
                    <span class="price-display">$<?php echo number_format($product['sale_price'], 2); ?></span>
                    <span class="original-price ms-2">$<?php echo number_format($product['price'], 2); ?></span>
                    <span class="badge" style="background:var(--accent);color:#fff;">Save <?php echo round(($product['price'] - $product['sale_price']) / $product['price'] * 100); ?>%</span>
                <?php else: ?>
                    <span class="price-display">$<?php echo number_format($product['price'], 2); ?></span>
                <?php endif; ?>
            </div>
            <p style="color: var(--gray);"><?php echo htmlspecialchars($product['description']); ?></p>
            <div class="mb-3">
                <?php if ($product['stock'] > 0): ?>
                    <span class="text-success"><i class="bi bi-check-circle me-1"></i> In Stock (<?php echo $product['stock']; ?> available)</span>
                <?php else: ?>
                    <span class="text-danger"><i class="bi bi-x-circle me-1"></i> Out of Stock</span>
                <?php endif; ?>
            </div>
            <p><strong>Category:</strong> <a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></p>
            <?php if ($product['stock'] > 0): ?>
            <form method="POST" class="mt-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <label class="fw-bold">Quantity:</label>
                    <div class="quantity-control">
                        <button type="button" class="qty-minus">-</button>
                        <input type="number" name="quantity" class="qty-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        <button type="button" class="qty-plus">+</button>
                    </div>
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-glamour btn-lg me-2"><i class="bi bi-cart-plus me-2"></i>Add to Cart</button>
                <a href="products.php" class="btn btn-glamour-outline btn-lg">Continue Shopping</a>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <hr class="my-5">

    <div class="row">
        <div class="col-lg-8">
            <h3 class="mb-4">Customer Reviews (<?php echo count($reviews); ?>)</h3>
            <?php if (empty($reviews)): ?>
                <p class="text-muted">No reviews yet. Be the first to review this product!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="reviewer"><?php echo htmlspecialchars($review['first_name'] . ' ' . substr($review['last_name'], 0, 1) . '.'); ?></span>
                            <div class="product-rating"><?php for ($i = 1; $i <= 5; $i++): ?><i class="bi bi-star<?php echo $i <= $review['rating'] ? '-fill' : ''; ?>"></i><?php endfor; ?></div>
                        </div>
                        <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                    </div>
                    <p class="mt-2 mb-0"><?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5>Write a Review</h5>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">5 - Excellent</option><option value="4">4 - Very Good</option><option value="3">3 - Good</option><option value="2">2 - Fair</option><option value="1">1 - Poor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Review</label>
                            <textarea name="comment" class="form-control" rows="4" required placeholder="Share your experience..."></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="btn btn-glamour w-100">Submit Review</button>
                    </form>
                    <?php else: ?>
                        <p class="text-muted">Please <a href="login.php">login</a> to write a review.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($relatedProducts)): ?>
    <hr class="my-5">
    <div class="section-heading"><h2>You May Also Like</h2></div>
    <div class="row g-4">
        <?php foreach ($relatedProducts as $rp): ?>
        <div class="col-lg-3 col-md-6">
            <div class="card product-card">
                <img src="images/<?php echo htmlspecialchars($rp['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($rp['name']); ?>">
                <div class="card-body">
                    <h5 class="product-name"><a href="product_detail.php?id=<?php echo $rp['product_id']; ?>"><?php echo htmlspecialchars($rp['name']); ?></a></h5>
                    <div class="product-price">$<?php echo number_format($rp['sale_price'] ?? $rp['price'], 2); ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<button id="backToTop" title="Back to Top"><i class="bi bi-chevron-up"></i></button>
<?php include 'includes/footer.php'; ?>
