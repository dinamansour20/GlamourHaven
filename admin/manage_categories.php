<?php
session_start();
require_once '../includes/db_config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }
$pdo = getDBConnection();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $description = trim($_POST['description'] ?? ''); $image = trim($_POST['image'] ?? 'category_face.jpg');
    if (!empty($name)) {
        if (isset($_POST['category_id']) && $_POST['category_id'] > 0) { $stmt = $pdo->prepare("UPDATE categories SET name=:name, description=:desc, image=:img WHERE category_id=:cid"); $stmt->execute([':name'=>$name,':desc'=>$description,':img'=>$image,':cid'=>(int)$_POST['category_id']]); $message = 'Category updated successfully!'; }
        else { $stmt = $pdo->prepare("INSERT INTO categories (name, description, image) VALUES (:name, :desc, :img)"); $stmt->execute([':name'=>$name,':desc'=>$description,':img'=>$image]); $message = 'Category added successfully!'; }
    }
}
if (isset($_GET['delete'])) { $catId = (int)$_GET['delete']; $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = :cid AND active = 1"); $check->execute([':cid'=>$catId]); if ($check->fetchColumn() > 0) { $message = 'Cannot delete category with active products.'; } else { $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = :cid"); $stmt->execute([':cid'=>$catId]); $message = 'Category deleted.'; } }
$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.category_id AND active = 1) as product_count FROM categories c ORDER BY c.name")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Categories | Glamour Haven Admin</title><link rel="icon" href="../images/favicon.ico"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="../css/style.css"></head><body>
<nav class="navbar navbar-dark bg-dark px-4"><a class="navbar-brand" href="dashboard.php"><i class="bi bi-gem me-2"></i>Glamour Haven Admin</a><div><a href="../index.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-shop"></i> View Store</a><a href="../logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a></div></nav>
<div class="container-fluid"><div class="row">
    <div class="col-md-2 admin-sidebar p-0"><nav class="nav flex-column mt-3"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a><a class="nav-link" href="manage_products.php"><i class="bi bi-box-seam"></i> Products</a><a class="nav-link" href="manage_orders.php"><i class="bi bi-receipt"></i> Orders</a><a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Users</a><a class="nav-link active" href="manage_categories.php"><i class="bi bi-tags"></i> Categories</a></nav></div>
    <div class="col-md-10 p-4"><h2 class="mb-4">Manage Categories</h2>
        <?php if ($message): ?><div class="alert alert-info alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-body"><h5>Add New Category</h5><form method="POST"><input type="hidden" name="category_id" value="0"><div class="mb-3"><label class="form-label">Name *</label><input type="text" class="form-control" name="name" required></div><div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"></textarea></div><div class="mb-3"><label class="form-label">Image Filename</label><input type="text" class="form-control" name="image" value="category_face.jpg"></div><button type="submit" class="btn btn-glamour w-100">Add Category</button></form></div></div></div>
            <div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-body p-0"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Image</th><th>Name</th><th>Description</th><th>Products</th><th>Actions</th></tr></thead><tbody>
            <?php foreach ($categories as $cat): ?>
            <tr><td><img src="../images/<?php echo htmlspecialchars($cat['image']); ?>" width="60" height="45" style="object-fit:cover; border-radius:5px;"></td><td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td><td><small><?php echo htmlspecialchars(substr($cat['description'] ?? '', 0, 60)); ?>...</small></td><td><?php echo $cat['product_count']; ?></td><td><a href="manage_categories.php?delete=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-outline-danger confirm-delete"><i class="bi bi-trash"></i></a></td></tr>
            <?php endforeach; ?>
            </tbody></table></div></div></div>
        </div>
    </div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script><script src="../js/main.js"></script></body></html>
