<?php
//login page to use the application
session_start();
require_once 'includes/db_config.php';
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$pageTitle = 'Login';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) { $error = 'Please enter both email and password.'; }
    else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] === 'admin') { header('Location: admin/dashboard.php'); }
            else { header('Location: ' . (isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php')); }
            exit;
        } else { $error = 'Invalid email or password. Please try again.'; }
    }
}
include 'includes/header.php';
?>
<div class="container py-5">
    <div class="auth-container">
        <h2><i class="bi bi-box-arrow-in-right me-2"></i>Welcome Back</h2>
        <p class="text-center text-muted mb-4">Login to your Glamour Haven account</p>
        <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if (isset($_GET['registered'])): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Account created! Please login.</div><?php endif; ?>
        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-3"><label for="email" class="form-label">Email Address</label><input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="your@email.com"></div>
            <div class="mb-4"><label for="password" class="form-label">Password</label><input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password"></div>
            <button type="submit" class="btn btn-glamour w-100 py-2">Login</button>
        </form>
        <p class="text-center mt-3">Don't have an account? <a href="register.php">Create one</a></p>
        <hr>
        <div class="text-center"><small class="text-muted"><strong>Demo Accounts:</strong><br>Admin: admin@glamourhaven.com<br>Customer: jane@example.com<br>Password for all: Admin123!</small></div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
