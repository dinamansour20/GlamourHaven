<?php
session_start();
require_once '../includes/db_config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$pdo = getDBConnection();
$message = '';
if (isset($_GET['delete'])) { $delId = (int)$_GET['delete']; if ($delId != $_SESSION['user_id']) { $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :id AND role != 'admin'"); $stmt->execute([':id' => $delId]); $message = 'User account deleted.'; } }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) { $userId = (int)$_POST['user_id']; $newRole = $_POST['new_role'] === 'admin' ? 'admin' : 'customer'; $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE user_id = :uid"); $stmt->execute([':role'=>$newRole,':uid'=>$userId]); $message = 'User role updated.'; }
$users = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.user_id) as order_count FROM users u ORDER BY u.created_at DESC")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Users | Glamour Haven Admin</title><link rel="icon" href="../images/favicon.ico"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="../css/style.css"></head><body>
<nav class="navbar navbar-dark bg-dark px-4"><a class="navbar-brand" href="dashboard.php"><i class="bi bi-gem me-2"></i>Glamour Haven Admin</a><div><a href="../index.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-shop"></i> View Store</a><a href="../logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a></div></nav>
<div class="container-fluid"><div class="row">
    <div class="col-md-2 admin-sidebar p-0"><nav class="nav flex-column mt-3"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a><a class="nav-link" href="manage_products.php"><i class="bi bi-box-seam"></i> Products</a><a class="nav-link" href="manage_orders.php"><i class="bi bi-receipt"></i> Orders</a><a class="nav-link active" href="manage_users.php"><i class="bi bi-people"></i> Users</a><a class="nav-link" href="manage_categories.php"><i class="bi bi-tags"></i> Categories</a></nav></div>
    <div class="col-md-10 p-4"><h2 class="mb-4">Manage Users</h2>
        <?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <div class="card border-0 shadow-sm"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Orders</th><th>Joined</th><th>Actions</th></tr></thead><tbody>
        <?php foreach ($users as $u): ?>
        <tr><td><?php echo $u['user_id']; ?></td><td><strong><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></strong></td><td><?php echo htmlspecialchars($u['email']); ?></td><td><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td><td><span class="badge <?php echo $u['role']==='admin'?'bg-danger':'bg-primary'; ?>"><?php echo ucfirst($u['role']); ?></span></td><td><?php echo $u['order_count']; ?></td><td><small><?php echo date('M j, Y', strtotime($u['created_at'])); ?></small></td>
        <td><?php if ($u['user_id'] != $_SESSION['user_id']): ?><form method="POST" class="d-inline"><input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>"><input type="hidden" name="new_role" value="<?php echo $u['role']==='admin'?'customer':'admin'; ?>"><button type="submit" name="change_role" class="btn btn-sm btn-outline-warning" title="Toggle Role"><i class="bi bi-arrow-repeat"></i></button></form><?php if ($u['role'] !== 'admin'): ?><a href="manage_users.php?delete=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-outline-danger confirm-delete" title="Delete"><i class="bi bi-trash"></i></a><?php endif; ?><?php else: ?><small class="text-muted">Current User</small><?php endif; ?></td></tr>
        <?php endforeach; ?>
        </tbody></table></div></div></div>
    </div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script><script src="../js/main.js"></script></body></html>
