<?php
//orders details page
session_start();
require_once 'includes/db_config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php?redirect=orders.php'); exit; }
$pageTitle = 'My Orders';
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as item_count FROM orders o WHERE o.user_id = :uid ORDER BY o.created_at DESC");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll();
include 'includes/header.php';
?>
<div class="page-header"><div class="container"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">My Orders</li></ol></nav><h1><i class="bi bi-bag me-2"></i>My Orders</h1></div></div>
<div class="container py-4">
    <?php if (empty($orders)): ?>
        <div class="empty-state"><i class="bi bi-bag-x d-block"></i><h3>No Orders Yet</h3><p>You haven't placed any orders yet.</p><a href="products.php" class="btn btn-glamour">Start Shopping</a></div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
        <div class="order-card"><div class="row align-items-center">
            <div class="col-md-3"><small class="text-muted">Order #</small><br><strong><?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></strong></div>
            <div class="col-md-2"><small class="text-muted">Date</small><br><?php echo date('M j, Y', strtotime($order['created_at'])); ?></div>
            <div class="col-md-2"><small class="text-muted">Items</small><br><?php echo $order['item_count']; ?> item(s)</div>
            <div class="col-md-2"><small class="text-muted">Total</small><br><strong class="text-glamour">$<?php echo number_format($order['total_amount'], 2); ?></strong></div>
            <div class="col-md-2"><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></div>
            <div class="col-md-1 text-end"><a href="order_confirmation.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-glamour-outline"><i class="bi bi-eye"></i></a></div>
        </div></div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
