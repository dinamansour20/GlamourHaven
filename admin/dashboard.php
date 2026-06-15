<?php
session_start();
require_once '../includes/db_config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$pdo = getDBConnection();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock < 10 AND active = 1")->fetchColumn();
$recentOrders = $pdo->query("SELECT o.*, u.first_name, u.last_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
$recentUsers = $pdo->query("SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Dashboard | Glamour Haven</title><link rel="icon" href="../images/favicon.ico"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="../css/style.css"></head><body>
<nav class="navbar navbar-dark bg-dark px-4"><a class="navbar-brand" href="dashboard.php"><i class="bi bi-gem me-2"></i>Glamour Haven Admin</a><div class="d-flex align-items-center"><span class="text-light me-3">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?></span><a href="../index.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-shop"></i> View Store</a><a href="../logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a></div></nav>
<div class="container-fluid"><div class="row">
    <div class="col-md-2 admin-sidebar p-0"><nav class="nav flex-column mt-3"><a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a><a class="nav-link" href="manage_products.php"><i class="bi bi-box-seam"></i> Products</a><a class="nav-link" href="manage_orders.php"><i class="bi bi-receipt"></i> Orders</a><a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Users</a><a class="nav-link" href="manage_categories.php"><i class="bi bi-tags"></i> Categories</a><hr class="text-secondary mx-3"><a class="nav-link" href="../index.php"><i class="bi bi-shop"></i> View Store</a></nav></div>
    <div class="col-md-10 p-4"><h2 class="mb-4">Dashboard</h2>
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6"><div class="stat-card" style="background: linear-gradient(135deg, #C9A96E, #A8893E);"><div class="d-flex justify-content-between align-items-start"><div><h3><?php echo $totalProducts; ?></h3><p>Total Products</p></div><i class="bi bi-box-seam"></i></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="stat-card" style="background: linear-gradient(135deg, #28a745, #34ce57);"><div class="d-flex justify-content-between align-items-start"><div><h3>$<?php echo number_format($totalRevenue, 2); ?></h3><p>Total Revenue</p></div><i class="bi bi-currency-dollar"></i></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="stat-card" style="background: linear-gradient(135deg, #4A3C32, #6B5B4F);"><div class="d-flex justify-content-between align-items-start"><div><h3><?php echo $totalOrders; ?></h3><p>Total Orders</p></div><i class="bi bi-receipt"></i></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="stat-card" style="background: linear-gradient(135deg, #8B1A2B, #B03050);"><div class="d-flex justify-content-between align-items-start"><div><h3><?php echo $totalUsers; ?></h3><p>Customers</p></div><i class="bi bi-people"></i></div></div></div>
        </div>
        <?php if ($pendingOrders > 0): ?><div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i><strong><?php echo $pendingOrders; ?></strong> order(s) pending review. <a href="manage_orders.php" class="alert-link">View Orders</a></div><?php endif; ?>
        <?php if ($lowStock > 0): ?><div class="alert alert-info"><i class="bi bi-info-circle me-2"></i><strong><?php echo $lowStock; ?></strong> product(s) are low on stock. <a href="manage_products.php" class="alert-link">View Products</a></div><?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between align-items-center"><h5 class="mb-0">Recent Orders</h5><a href="manage_orders.php" class="btn btn-sm btn-glamour-outline">View All</a></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>
            <?php foreach ($recentOrders as $order): ?><tr><td>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></td><td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td><td>$<?php echo number_format($order['total_amount'], 2); ?></td><td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td><td><?php echo date('M j', strtotime($order['created_at'])); ?></td></tr><?php endforeach; ?>
            <?php if (empty($recentOrders)): ?><tr><td colspan="5" class="text-center text-muted py-3">No orders yet</td></tr><?php endif; ?>
            </tbody></table></div></div></div></div>
            <div class="col-lg-5"><div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between align-items-center"><h5 class="mb-0">New Customers</h5><a href="manage_users.php" class="btn btn-sm btn-glamour-outline">View All</a></div><div class="card-body p-0"><ul class="list-group list-group-flush">
            <?php foreach ($recentUsers as $u): ?><li class="list-group-item d-flex align-items-center"><i class="bi bi-person-circle fs-4 text-muted me-3"></i><div><strong><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small></div></li><?php endforeach; ?>
            <?php if (empty($recentUsers)): ?><li class="list-group-item text-center text-muted py-3">No customers yet</li><?php endif; ?>
            </ul></div></div></div>
        </div>
    </div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></body></html>
