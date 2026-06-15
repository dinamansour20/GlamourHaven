<?php
//sign up as a new user page for a new acc
session_start();
require_once 'includes/db_config.php';
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$pageTitle = 'Create Account';
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (empty($firstName)) $errors[] = 'First name is required.';
    if (empty($lastName)) $errors[] = 'Last name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (empty($errors)) {
        $stmtCheck = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
        $stmtCheck->execute([':email' => $email]);
        if ($stmtCheck->fetch()) { $errors[] = 'An account with this email already exists.'; }
    }
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmtInsert = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES (:fn, :ln, :email, :phone, :pw, 'customer')");
        $stmtInsert->execute([':fn' => $firstName, ':ln' => $lastName, ':email' => $email, ':phone' => $phone, ':pw' => $hashedPassword]);
        $success = 'Account created successfully! You can now log in.';
    }
}
include 'includes/header.php';
?>
<div class="container py-5">
    <div class="auth-container">
        <h2><i class="bi bi-person-plus me-2"></i>Create Account</h2>
        <p class="text-center text-muted mb-4">Join Glamour Haven and start shopping!</p>
        <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?php echo $success; ?><br><a href="login.php" class="alert-link">Click here to login</a></div><?php endif; ?>
        <form method="POST" class="needs-validation" novalidate>
            <div class="row g-3"><div class="col-md-6"><label for="firstName" class="form-label">First Name *</label><input type="text" class="form-control" id="firstName" name="first_name" required value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"></div><div class="col-md-6"><label for="lastName" class="form-label">Last Name *</label><input type="text" class="form-control" id="lastName" name="last_name" required value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"></div></div>
            <div class="mb-3 mt-3"><label for="email" class="form-label">Email Address *</label><input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"></div>
            <div class="mb-3"><label for="phone" class="form-label">Phone Number</label><input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"></div>
            <div class="mb-3"><label for="regPassword" class="form-label">Password * <small class="text-muted">(min 8 characters)</small></label><input type="password" class="form-control" id="regPassword" name="password" minlength="8" required><div class="progress mt-2" style="height: 5px;"><div class="progress-bar" role="progressbar" id="passwordStrength" style="width: 0%;"></div></div></div>
            <div class="mb-4"><label for="confirmPassword" class="form-label">Confirm Password *</label><input type="password" class="form-control" id="confirmPassword" name="confirm_password" required></div>
            <button type="submit" class="btn btn-glamour w-100 py-2">Create Account</button>
        </form>
        <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
