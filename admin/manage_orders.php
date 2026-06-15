<?php
session_start();
require_once '../includes/db_config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$pdo = getDBConnection();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id']; $status = $_POST['status']; $allowed = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) { $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE order_id = :oid"); $stmt->execute([':status'=>$status,':oid'=>$orderId]); $message = 'Order #' . str_pad($orderId, 6, '0', STR_PAD_LEFT) . ' status updated to ' . ucfirst($status) . '.'; }
}
$orders = $pdo->query("SELECT o.*, u.first_name, u.last_name, u.email, (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as item_count FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.created_at DESC")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Orders | Glamour Haven Admin</title><link rel="icon" href="../images/favicon.ico"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="../css/style.css"></head><body>
<nav class="navbar navbar-dark bg-dark px-4"><a class="navbar-brand" href="dashboard.php"><i class="bi bi-gem me-2"></i>Glamour Haven Admin</a><div><a href="../index.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-shop"></i> View Store</a><a href="../logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a></div></nav>
<div class="container-fluid"><div class="row">
    <div class="col-md-2 admin-sidebar p-0"><nav class="nav flex-column mt-3"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a><a class="nav-link" href="manage_products.php"><i class="bi bi-box-seam"></i> Products</a><a class="nav-link active" href="manage_orders.php"><i class="bi bi-receipt"></i> Orders</a><a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Users</a><a class="nav-link" href="manage_categories.php"><i class="bi bi-tags"></i> Categories</a></nav></div>
    <div class="col-md-10 p-4"><h2 class="mb-4">Manage Orders</h2>
        <?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <div class="card border-0 shadow-sm"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Update</th></tr></thead><tbody>
        <?php foreach ($orders as $o): ?>
        <tr><td><strong>#<?php echo str_pad($o['order_id'], 6, '0', STR_PAD_LEFT); ?></strong></td><td><?php echo htmlspecialchars($o['first_name'] . ' ' . $o['last_name']); ?><br><small class="text-muted"><?php echo htmlspecialchars($o['email']); ?></small></td><td><?php echo $o['item_count']; ?></td><td class="fw-bold">$<?php echo number_format($o['total_amount'], 2); ?></td><td><small><?php echo ucwords(str_replace('_', ' ', $o['payment_method'])); ?></small></td><td><span class="status-badge status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td><td><small><?php echo date('M j, Y', strtotime($o['created_at'])); ?></small></td>
        <td><form method="POST" class="d-flex gap-1"><input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>"><select name="status" class="form-select form-select-sm" style="width:130px;"><option value="pending" <?php echo $o['status']=='pending'?'selected':''; ?>>Pending</option><option value="processing" <?php echo $o['status']=='processing'?'selected':''; ?>>Processing</option><option value="shipped" <?php echo $o['status']=='shipped'?'selected':''; ?>>Shipped</option><option value="delivered" <?php echo $o['status']=='delivered'?'selected':''; ?>>Delivered</option><option value="cancelled" <?php echo $o['status']=='cancelled'?'selected':''; ?>>Cancelled</option></select><button type="submit" name="update_status" class="btn btn-sm btn-glamour">Update</button></form></td></tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?><tr><td colspan="8" class="text-center text-muted py-4">No orders found.</td></tr><?php endif; ?>
        </tbody></table></div></div></div>
    </div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></body></html>
