<?php
//search for products 
session_start();
require_once 'includes/db_config.php';
$pageTitle = 'Search Results';
$pdo = getDBConnection();
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];
if (!empty($query)) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.active = 1 AND (p.name LIKE :q1 OR p.description LIKE :q2 OR p.brand LIKE :q3 OR c.name LIKE :q4) ORDER BY p.name");
    $searchTerm = "%$query%";
    $stmt->execute([':q1'=>$searchTerm,':q2'=>$searchTerm,':q3'=>$searchTerm,':q4'=>$searchTerm]);
    $products = $stmt->fetchAll();
}
include 'includes/header.php';
?>
<div class="page-header search-results-page"><div class="container"><h1><i class="bi bi-search me-2"></i>Search Results</h1><?php if (!empty($query)): ?><p class="text-muted mb-0">Showing <?php echo count($products); ?> result(s) for "<strong><?php echo htmlspecialchars($query); ?></strong>"</p><?php endif; ?></div></div>
<div class="container py-4">
    <?php if (empty($query)): ?><div class="empty-state"><i class="bi bi-search d-block"></i><h3>Search for Products</h3><p>Enter a keyword to search our catalog.</p></div>
    <?php elseif (empty($products)): ?><div class="empty-state"><i class="bi bi-emoji-frown d-block"></i><h3>No Results Found</h3><p>We couldn't find any products matching "<strong><?php echo htmlspecialchars($query); ?></strong>".</p><a href="products.php" class="btn btn-glamour">Browse All Products</a></div>
    <?php else: ?>
        <div class="row g-4">
        <?php foreach ($products as $product): ?>
        <div class="col-lg-3 col-md-4 col-sm-6"><div class="card product-card position-relative">
            <?php if ($product['sale_price']): ?><span class="sale-badge">SALE</span><?php endif; ?>
            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="card-body"><span class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                <h5 class="product-name"><a href="product_detail.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h5>
                <div class="product-price"><?php if ($product['sale_price']): ?>$<?php echo number_format($product['sale_price'], 2); ?><span class="original-price">$<?php echo number_format($product['price'], 2); ?></span><?php else: ?>$<?php echo number_format($product['price'], 2); ?><?php endif; ?></div>
                <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-glamour btn-sm w-100 mt-3">View Details</a>
            </div></div></div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
