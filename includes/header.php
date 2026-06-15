<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :uid");
        $stmt->execute([':uid' => $_SESSION['user_id']]);
        $result = $stmt->fetch();
        $cartCount = $result['total'] ?? 0;
    } catch (PDOException $e) {
        $cartCount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?>Glamour Haven</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="announcement-bar text-center py-2">
        <small>FREE SHIPPING on orders over $50 &nbsp;|&nbsp; Use code <strong>GLAM20</strong> for 20% off your first order</small>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Glamour Haven" height="45" class="d-inline-block">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Shop All</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown">Categories</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="products.php?category=1">Face</a></li>
                            <li><a class="dropdown-item" href="products.php?category=2">Eyes</a></li>
                            <li><a class="dropdown-item" href="products.php?category=3">Lips</a></li>
                            <li><a class="dropdown-item" href="products.php?category=4">Tools & Brushes</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <form class="d-flex me-3" action="search.php" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" name="q" placeholder="Search products..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="admin/dashboard.php"><i class="bi bi-gear"></i> Admin</a></li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="bi bi-bag"></i> My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="cart.php">
                                <i class="bi bi-cart3"></i>
                                <?php if ($cartCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge"><?php echo $cartCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-person"></i> Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
