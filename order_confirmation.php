<?php
//confirm order details
session_start();
require_once 'includes/db_config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$pageTitle = 'Order Confirmation';
$pdo = getDBConnection();
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$stmt = $pdo->prepare("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.order_id = :oid AND o.user_id = :uid");
$stmt->execute([':oid' => $orderId, ':uid' => $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) { header('Location: index.php'); exit; }
$stmtItems = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = :oid");
$stmtItems->execute([':oid' => $orderId]);
$orderItems = $stmtItems->fetchAll();
include 'includes/header.php';
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: var(--primary);"></i>
        <h1 class="mt-3">Order Confirmed!</h1>
        <p class="text-muted fs-5">Thank you for your purchase, <?php echo htmlspecialchars($order['first_name']); ?>!</p>
        <p class="text-muted">Order #<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></p>
    </div>
    <div class="row justify-content-center"><div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
            <h4 class="mb-3">Order Details</h4>
            <div class="row mb-3"><div class="col-md-6"><strong>Order Date:</strong><br><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></div><div class="col-md-6"><strong>Status:</strong><br><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></div></div>
            <div class="row mb-3"><div class="col-md-6"><strong>Shipping To:</strong><br><?php echo htmlspecialchars($order['shipping_address']); ?><br><?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state'] . ' ' . $order['shipping_zip']); ?></div><div class="col-md-6"><strong>Payment Method:</strong><br><?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></div></div>
            <hr><h5 class="mb-3">Items Ordered</h5>
            <?php foreach ($orderItems as $item): ?>
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div class="d-flex align-items-center"><img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-img me-3"><div><strong><?php echo htmlspecialchars($item['name']); ?></strong><br><small class="text-muted">Qty: <?php echo $item['quantity']; ?> x $<?php echo number_format($item['price'], 2); ?></small></div></div>
                <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
            </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between mt-3"><strong class="fs-5">Total</strong><strong class="fs-5 text-glamour">$<?php echo number_format($order['total_amount'], 2); ?></strong></div>
        </div></div>
        <div class="text-center"><a href="orders.php" class="btn btn-glamour me-2"><i class="bi bi-bag me-1"></i> View My Orders</a><a href="products.php" class="btn btn-glamour-outline">Continue Shopping</a><button onclick="printOrder()" class="btn btn-outline-secondary ms-2"><i class="bi bi-printer me-1"></i> Print</button></div>
    </div></div>
</div>
<?php include 'includes/footer.php'; ?>
