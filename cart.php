<?php
//access cart for items and edit the cart or go to checkout page
session_start();
require_once 'includes/db_config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php?redirect=cart.php'); exit; }
$pageTitle = 'Shopping Cart';
$pdo = getDBConnection();
$userId = $_SESSION['user_id'];
$message = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'remove' && isset($_GET['cart_id'])) { $stmt = $pdo->prepare("DELETE FROM cart WHERE cart_id = :cid AND user_id = :uid"); $stmt->execute([':cid' => (int)$_GET['cart_id'], ':uid' => $userId]); $message = 'Item removed from cart.'; }
    if ($action === 'update' && isset($_GET['cart_id']) && isset($_GET['quantity'])) { $qty = max(1, (int)$_GET['quantity']); $stmt = $pdo->prepare("UPDATE cart SET quantity = :qty WHERE cart_id = :cid AND user_id = :uid"); $stmt->execute([':qty' => $qty, ':cid' => (int)$_GET['cart_id'], ':uid' => $userId]); $message = 'Cart updated.'; }
    if ($action === 'clear') { $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :uid"); $stmt->execute([':uid' => $userId]); $message = 'Cart cleared.'; }
}
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.sale_price, p.image, p.stock FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = :uid ORDER BY c.added_at DESC");
$stmt->execute([':uid' => $userId]);
$cartItems = $stmt->fetchAll();
$subtotal = 0;
foreach ($cartItems as $item) { $subtotal += ($item['sale_price'] ?? $item['price']) * $item['quantity']; }
$shipping = $subtotal >= 50 ? 0 : 5.99;
$tax = round($subtotal * 0.08, 2);
$total = $subtotal + $shipping + $tax;
include 'includes/header.php';
?>
<div class="page-header"><div class="container"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Shopping Cart</li></ol></nav><h1><i class="bi bi-cart3 me-2"></i>Shopping Cart</h1></div></div>
<div class="container py-4">
    <?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if (empty($cartItems)): ?>
        <div class="empty-state"><i class="bi bi-cart-x d-block"></i><h3>Your Cart is Empty</h3><p>Looks like you haven't added any products yet.</p><a href="products.php" class="btn btn-glamour">Start Shopping</a></div>
    <?php else: ?>
        <div class="row"><div class="col-lg-8">
            <div class="table-responsive"><table class="table cart-table"><thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr></thead><tbody>
            <?php foreach ($cartItems as $item): $itemPrice = $item['sale_price'] ?? $item['price']; $lineTotal = $itemPrice * $item['quantity']; ?>
            <tr><td><div class="d-flex align-items-center"><img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-img me-3"><div><a href="product_detail.php?id=<?php echo $item['product_id']; ?>" class="text-dark fw-bold text-decoration-none"><?php echo htmlspecialchars($item['name']); ?></a></div></div></td>
            <td>$<?php echo number_format($itemPrice, 2); ?></td>
            <td><div class="quantity-control"><button onclick="updateCartQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>-</button><input type="text" value="<?php echo $item['quantity']; ?>" readonly class="qty-input"><button onclick="updateCartQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button></div></td>
            <td class="fw-bold">$<?php echo number_format($lineTotal, 2); ?></td>
            <td><a href="cart.php?action=remove&cart_id=<?php echo $item['cart_id']; ?>" class="btn btn-sm btn-outline-danger confirm-delete" title="Remove"><i class="bi bi-trash"></i></a></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
            <div class="d-flex justify-content-between"><a href="products.php" class="btn btn-glamour-outline"><i class="bi bi-arrow-left me-1"></i> Continue Shopping</a><a href="cart.php?action=clear" class="btn btn-outline-danger confirm-delete">Clear Cart</a></div>
        </div>
        <div class="col-lg-4"><div class="cart-summary"><h4>Order Summary</h4>
            <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span>Shipping</span><span><?php echo $shipping > 0 ? '$' . number_format($shipping, 2) : '<span class="text-success">FREE</span>'; ?></span></div>
            <div class="d-flex justify-content-between mb-3"><span>Estimated Tax</span><span>$<?php echo number_format($tax, 2); ?></span></div><hr>
            <div class="d-flex justify-content-between mb-4"><strong class="fs-5">Total</strong><strong class="fs-5 text-glamour">$<?php echo number_format($total, 2); ?></strong></div>
            <a href="checkout.php" class="btn btn-glamour w-100 py-2"><i class="bi bi-lock me-2"></i>Proceed to Checkout</a>
            <?php if ($subtotal < 50): ?><p class="text-center text-muted small mt-2">Add $<?php echo number_format(50 - $subtotal, 2); ?> more for FREE shipping!</p><?php endif; ?>
        </div></div></div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
