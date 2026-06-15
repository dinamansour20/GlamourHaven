<?php
//business info for contacts page 
session_start();
require_once 'includes/db_config.php';
$pageTitle = 'Contact Us';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $body = trim($_POST['message'] ?? '');
    if (!empty($name) && !empty($email) && !empty($body)) { $message = 'Thank you for your message! We will get back to you within 24-48 hours.'; }
}
include 'includes/header.php';
?>
<div class="page-header"><div class="container"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Contact Us</li></ol></nav><h1>Contact Us</h1><p class="text-muted mb-0">We would love to hear from you!</p></div></div>
<div class="container py-5">
    <?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <div class="row g-5"><div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h3 class="mb-4">Send Us a Message</h3>
        <form method="POST" class="needs-validation" novalidate>
            <div class="row g-3"><div class="col-md-6"><label class="form-label">Your Name *</label><input type="text" class="form-control" name="name" required value="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) : ''; ?>"></div><div class="col-md-6"><label class="form-label">Email Address *</label><input type="email" class="form-control" name="email" required value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>"></div><div class="col-12"><label class="form-label">Subject</label><select name="subject" class="form-select"><option value="general">General Inquiry</option><option value="order">Order Issue</option><option value="product">Product Question</option><option value="return">Return / Exchange</option><option value="feedback">Feedback</option></select></div><div class="col-12"><label class="form-label">Your Message *</label><textarea class="form-control" name="message" rows="6" required placeholder="How can we help you?"></textarea></div></div>
            <button type="submit" class="btn btn-glamour mt-3"><i class="bi bi-send me-2"></i>Send Message</button>
        </form>
    </div></div></div>
    <div class="col-lg-5"><div class="contact-info-card"><h3 class="mb-4">Get in Touch</h3>
        <div class="mb-4"><p><i class="bi bi-geo-alt"></i> 123 Beauty Boulevard, Suite 100<br><span class="ms-4">Los Angeles, CA 90001</span></p></div>
        <div class="mb-4"><p><i class="bi bi-telephone"></i> 1-800-GLAMOUR (1-800-452-6687)</p></div>
        <div class="mb-4"><p><i class="bi bi-envelope"></i> support@glamourhaven.com</p></div>
        <div class="mb-4"><p><i class="bi bi-clock"></i> Monday - Friday: 9AM - 6PM EST<br><span class="ms-4">Saturday: 10AM - 4PM EST</span><br><span class="ms-4">Sunday: Closed</span></p></div>
    </div></div></div>
</div>
<?php include 'includes/footer.php'; ?>
