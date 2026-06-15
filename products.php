<?php
//products listings page
session_start();
require_once 'includes/db_config.php';
$pageTitle = 'Shop All Products';
$pdo = getDBConnection();

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.active = 1";
$params = [];

if ($categoryId > 0) { $sql .= " AND p.category_id = :cat_id"; $params[':cat_id'] = $categoryId; }
if (!empty($searchQuery)) { $sql .= " AND (p.name LIKE :search OR p.description LIKE :search2 OR p.brand LIKE :search3)"; $params[':search'] = "%$searchQuery%"; $params[':search2'] = "%$searchQuery%"; $params[':search3'] = "%$searchQuery%"; }

switch ($sortBy) {
    case 'price_low': $sql .= " ORDER BY COALESCE(p.sale_price, p.price) ASC"; break;
    case 'price_high': $sql .= " ORDER BY COALESCE(p.sale_price, p.price) DESC"; break;
    case 'name': $sql .= " ORDER BY p.name ASC"; break;
    default: $sql .= " ORDER BY p.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$stmtCats = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmtCats->fetchAll();

$currentCategory = '';
if ($categoryId > 0) { foreach ($categories as $cat) { if ($cat['category_id'] == $categoryId) { $currentCategory = $cat['name']; $pageTitle = $cat['name']; } } }

include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <?php if ($currentCategory): ?>
                    <li class="breadcrumb-item"><a href="products.php">Shop All</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($currentCategory); ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item active">Shop All</li>
                <?php endif; ?>
            </ol>
        </nav>
        <h1><?php echo $currentCategory ? htmlspecialchars($currentCategory) : 'All Products'; ?></h1>
        <p class="text-muted mb-0"><?php echo count($products); ?> product(s) found</p>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3" style="font-family: var(--font-display);">Filters</h5>
                    <h6 class="text-uppercase small fw-bold mb-2">Category</h6>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-1"><a href="products.php" class="text-decoration-none <?php echo $categoryId == 0 ? 'fw-bold text-glamour' : 'text-dark'; ?>">All Products</a></li>
                        <?php foreach ($categories as $cat): ?>
                        <li class="mb-1"><a href="products.php?category=<?php echo $cat['category_id']; ?>" class="text-decoration-none <?php echo $categoryId == $cat['category_id'] ? 'fw-bold text-glamour' : 'text-dark'; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <h6 class="text-uppercase small fw-bold mb-2">Sort By</h6>
                    <form method="GET" action="products.php" id="sortForm">
                        <?php if ($categoryId > 0): ?><input type="hidden" name="category" value="<?php echo $categoryId; ?>"><?php endif; ?>
                        <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('sortForm').submit();">
                            <option value="newest" <?php echo $sortBy == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="price_low" <?php echo $sortBy == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sortBy == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name" <?php echo $sortBy == 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <?php if (empty($products)): ?>
                <div class="empty-state"><i class="bi bi-search d-block"></i><h3>No products found</h3><p>Try adjusting your filters or search query.</p><a href="products.php" class="btn btn-glamour">View All Products</a></div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card product-card position-relative">
                            <?php if ($product['sale_price']): ?><span class="sale-badge">SALE</span><?php endif; ?>
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <span class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                                <h5 class="product-name"><a href="product_detail.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h5>
                                <p class="text-muted small mb-2"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="product-price">
                                        <?php if ($product['sale_price']): ?>$<?php echo number_format($product['sale_price'], 2); ?><span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <?php else: ?>$<?php echo number_format($product['price'], 2); ?><?php endif; ?>
                                    </div>
                                    <?php if ($product['stock'] > 0): ?><span class="badge bg-success">In Stock</span><?php else: ?><span class="badge bg-secondary">Out of Stock</span><?php endif; ?>
                                </div>
                                <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-glamour btn-sm w-100 mt-3">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<button id="backToTop" title="Back to Top"><i class="bi bi-chevron-up"></i></button>
<?php include 'includes/footer.php'; ?>
