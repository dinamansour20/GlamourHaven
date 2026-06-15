<?php
//user profile with their info page 
session_start();
require_once 'includes/db_config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$pageTitle = 'My Profile';
$pdo = getDBConnection();
$message = ''; $messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? ''); $lastName = trim($_POST['last_name'] ?? ''); $phone = trim($_POST['phone'] ?? ''); $address = trim($_POST['address'] ?? ''); $city = trim($_POST['city'] ?? ''); $state = trim($_POST['state'] ?? ''); $zip = trim($_POST['zip'] ?? '');
    $newPassword = $_POST['new_password'] ?? ''; $confirmPassword = $_POST['confirm_password'] ?? '';
    if (!empty($newPassword)) {
        if (strlen($newPassword) < 8) { $message = 'New password must be at least 8 characters.'; $messageType = 'danger'; }
        elseif ($newPassword !== $confirmPassword) { $message = 'Passwords do not match.'; $messageType = 'danger'; }
        else { $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); $stmt = $pdo->prepare("UPDATE users SET first_name=:fn, last_name=:ln, phone=:ph, address=:addr, city=:city, state=:state, zip=:zip, password=:pw WHERE user_id=:uid"); $stmt->execute([':fn'=>$firstName,':ln'=>$lastName,':ph'=>$phone,':addr'=>$address,':city'=>$city,':state'=>$state,':zip'=>$zip,':pw'=>$hashedPassword,':uid'=>$_SESSION['user_id']]); $message = 'Profile and password updated successfully!'; $messageType = 'success'; }
    } else { $stmt = $pdo->prepare("UPDATE users SET first_name=:fn, last_name=:ln, phone=:ph, address=:addr, city=:city, state=:state, zip=:zip WHERE user_id=:uid"); $stmt->execute([':fn'=>$firstName,':ln'=>$lastName,':ph'=>$phone,':addr'=>$address,':city'=>$city,':state'=>$state,':zip'=>$zip,':uid'=>$_SESSION['user_id']]); $message = 'Profile updated successfully!'; $messageType = 'success'; }
    $_SESSION['first_name'] = $firstName; $_SESSION['last_name'] = $lastName;
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :uid"); $stmt->execute([':uid' => $_SESSION['user_id']]); $user = $stmt->fetch();
$stmtOrders = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = :uid"); $stmtOrders->execute([':uid' => $_SESSION['user_id']]); $orderCount = $stmtOrders->fetch()['count'];
include 'includes/header.php';
?>
<div class="container py-4">
    <div class="profile-header"><div class="row align-items-center"><div class="col-md-8"><h2><i class="bi bi-person-circle me-2"></i><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2><p class="mb-1"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?></p><p class="mb-0"><i class="bi bi-bag me-2"></i><?php echo $orderCount; ?> order(s) placed</p><p class="mb-0"><small>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></small></p></div><div class="col-md-4 text-end d-none d-md-block"><i class="bi bi-person-circle"></i></div></div></div>
    <?php if ($message): ?><div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <div class="row"><div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h4 class="mb-3">Edit Profile</h4>
        <form method="POST">
            <div class="row g-3"><div class="col-md-6"><label class="form-label">First Name</label><input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required></div><div class="col-md-6"><label class="form-label">Last Name</label><input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required></div><div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly><small class="text-muted">Email cannot be changed.</small></div><div class="col-md-6"><label class="form-label">Phone</label><input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"></div><div class="col-12"><label class="form-label">Address</label><input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"></div><div class="col-md-5"><label class="form-label">City</label><input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"></div><div class="col-md-4"><label class="form-label">State</label><input type="text" class="form-control" name="state" value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>"></div><div class="col-md-3"><label class="form-label">ZIP</label><input type="text" class="form-control" name="zip" value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>"></div></div>
            <hr class="my-4"><h5>Change Password <small class="text-muted">(leave blank to keep current)</small></h5>
            <div class="row g-3"><div class="col-md-6"><label class="form-label">New Password</label><input type="password" class="form-control" name="new_password" minlength="8"></div><div class="col-md-6"><label class="form-label">Confirm New Password</label><input type="password" class="form-control" name="confirm_password"></div></div>
            <button type="submit" class="btn btn-glamour mt-4">Save Changes</button>
        </form>
    </div></div></div>
    <div class="col-lg-4"><div class="card border-0 shadow-sm mb-3"><div class="card-body text-center p-4"><h5>Quick Links</h5><a href="orders.php" class="btn btn-glamour-outline w-100 mb-2"><i class="bi bi-bag me-1"></i> My Orders</a><a href="cart.php" class="btn btn-glamour-outline w-100 mb-2"><i class="bi bi-cart me-1"></i> My Cart</a><a href="products.php" class="btn btn-glamour-outline w-100"><i class="bi bi-shop me-1"></i> Continue Shopping</a></div></div></div></div>
</div>
<?php include 'includes/footer.php'; ?>
