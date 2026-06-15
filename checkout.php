<?php
//checkout the products to make purchase
session_start();
require_once 'includes/db_config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php?redirect=checkout.php'); exit; }
$pageTitle = 'Checkout';
$pdo = getDBConnection();
$userId = $_SESSION['user_id'];
$errors = [];
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.sale_price, p.image, p.stock FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = :uid");
$stmt->execute([':uid' => $userId]);
$cartItems = $stmt->fetchAll();
if (empty($cartItems)) { header('Location: cart.php'); exit; }
$subtotal = 0;
foreach ($cartItems as $item) { $subtotal += ($item['sale_price'] ?? $item['price']) * $item['quantity']; }
$shipping = $subtotal >= 50 ? 0 : 5.99;
$tax = round($subtotal * 0.08, 2);
$total = $subtotal + $shipping + $tax;
$stmtUser = $pdo->prepare("SELECT * FROM users WHERE user_id = :uid");
$stmtUser->execute([':uid' => $userId]);
$user = $stmtUser->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? ''); $city = trim($_POST['city'] ?? ''); $state = trim($_POST['state'] ?? ''); $zip = trim($_POST['zip'] ?? ''); $payment = $_POST['payment_method'] ?? 'credit_card';
    if (empty($address)) $errors[] = 'Shipping address is required.';
    if (empty($city)) $errors[] = 'City is required.';
    if (empty($state)) $errors[] = 'State is required.';
    if (empty($zip)) $errors[] = 'ZIP code is required.';
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, shipping_city, shipping_state, shipping_zip, status, payment_method) VALUES (:uid, :total, :addr, :city, :state, :zip, 'pending', :payment)");
            $stmtOrder->execute([':uid'=>$userId, ':total'=>$total, ':addr'=>$address, ':city'=>$city, ':state'=>$state, ':zip'=>$zip, ':payment'=>$payment]);
            $orderId = $pdo->lastInsertId();
            foreach ($cartItems as $item) { $itemPrice = $item['sale_price'] ?? $item['price']; $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:oid, :pid, :qty, :price)"); $stmtItem->execute([':oid'=>$orderId,':pid'=>$item['product_id'],':qty'=>$item['quantity'],':price'=>$itemPrice]); $stmtStock = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE product_id = :pid"); $stmtStock->execute([':qty'=>$item['quantity'],':pid'=>$item['product_id']]); }
            $stmtClear = $pdo->prepare("DELETE FROM cart WHERE user_id = :uid"); $stmtClear->execute([':uid'=>$userId]);
            $pdo->commit();
            header('Location: order_confirmation.php?order_id=' . $orderId); exit;
        } catch (PDOException $e) { $pdo->rollBack(); $errors[] = 'An error occurred. Please try again.'; error_log("Order Error: " . $e->getMessage()); }
    }
}
include 'includes/header.php';
?>
<div class="page-header"><div class="container"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item"><a href="cart.php">Cart</a></li><li class="breadcrumb-item active">Checkout</li></ol></nav><h1><i class="bi bi-lock me-2"></i>Checkout</h1></div></div>
<div class="container py-4">
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="POST" onsubmit="return confirmOrder();"><div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4"><h4 class="mb-3"><i class="bi bi-truck me-2"></i>Shipping Information</h4>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">First Name</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly></div>
                    <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly></div>
                    <div class="col-12"><label class="form-label">Street Address *</label><input type="text" class="form-control" name="address" required value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" placeholder="123 Main Street"></div>
                    <div class="col-md-5"><label class="form-label">City *</label><input type="text" class="form-control" name="city" required value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"></div>
                    <div class="col-md-4"><label class="form-label">State *</label><input type="text" class="form-control" name="state" required value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>"></div>
                    <div class="col-md-3"><label class="form-label">ZIP Code *</label><input type="text" class="form-control" name="zip" required value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>"></div>
                </div>
            </div></div>
            <div class="card border-0 shadow-sm"><div class="card-body p-4"><h4 class="mb-3"><i class="bi bi-credit-card me-2"></i>Payment Method</h4>
                <div class="form-check mb-2"><input class="form-check-input" type="radio" name="payment_method" value="credit_card" id="cc" checked><label class="form-check-label" for="cc"><i class="bi bi-credit-card me-1"></i> Credit Card</label></div>
                <div class="form-check mb-2"><input class="form-check-input" type="radio" name="payment_method" value="debit_card" id="dc"><label class="form-check-label" for="dc"><i class="bi bi-credit-card-2-front me-1"></i> Debit Card</label></div>
                <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" value="paypal" id="pp"><label class="form-check-label" for="pp"><i class="bi bi-paypal me-1"></i> PayPal</label></div>
                <p class="text-muted small mt-3"><i class="bi bi-info-circle me-1"></i>This is a demo. No real payment will be processed.</p>
            </div></div>
        </div>
        <div class="col-lg-4"><div class="cart-summary"><h4>Order Summary</h4>
            <?php foreach ($cartItems as $item): $itemPrice = $item['sale_price'] ?? $item['price']; ?>
            <div class="d-flex justify-content-between align-items-center mb-2"><div><small class="d-block"><?php echo htmlspecialchars($item['name']); ?></small><small class="text-muted">Qty: <?php echo $item['quantity']; ?></small></div><span>$<?php echo number_format($itemPrice * $item['quantity'], 2); ?></span></div>
            <?php endforeach; ?><hr>
            <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span>Shipping</span><span><?php echo $shipping > 0 ? '$' . number_format($shipping, 2) : 'FREE'; ?></span></div>
            <div class="d-flex justify-content-between mb-3"><span>Tax</span><span>$<?php echo number_format($tax, 2); ?></span></div><hr>
            <div class="d-flex justify-content-between mb-4"><strong class="fs-5">Total</strong><strong class="fs-5 text-glamour">$<?php echo number_format($total, 2); ?></strong></div>
            <button type="submit" class="btn btn-glamour w-100 py-2 fs-5"><i class="bi bi-bag-check me-2"></i>Place Order</button>
            <p class="text-center text-muted small mt-2"><i class="bi bi-shield-lock me-1"></i>Secure checkout</p>
        </div></div>
    </div></form>
</div>
<?php include 'includes/footer.php'; ?>
