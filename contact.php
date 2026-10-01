<?php
require_once 'includes/functions.php';
$pageTitle = 'Contact';
$sent = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please complete every field with a valid email address.';
    } else {
        $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, message TEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);
        $sent = true;
    }
}
require 'includes/header.php';
?>
<section class="page-hero"><div class="container"><p class="section-kicker text-warning mb-2">Get in touch</p><h1 class="mb-2">Let’s talk about good food.</h1><p class="mb-0">Have a local favourite to suggest or a question about SWAD NEPAL? Send us a note.</p></div></section>
<main class="container py-5"><div class="row g-4 justify-content-center"><div class="col-lg-7"><div class="card contact-card">
  <?php if ($sent): ?><div class="alert alert-success">Thanks for reaching out. Your message has been received.</div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?= clean($error) ?></div><?php endif; ?>
  <h2 class="mb-4">Send a message</h2><form method="POST"><div class="row g-3"><div class="col-md-6"><label class="form-label">Your name</label><input class="form-control" name="name" required></div><div class="col-md-6"><label class="form-label">Email address</label><input class="form-control" type="email" name="email" required></div><div class="col-12"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="5" required placeholder="Tell us about a place we should know..."></textarea></div><div class="col-12"><button class="btn btn-brand" type="submit">Send message</button></div></div></form>
  </div></div><div class="col-lg-4"><div class="card contact-card h-100"><p class="section-kicker mb-2">Suggestions welcome</p><h3>Know a hidden gem?</h3><p class="text-muted">We’re always keen to hear about the food spots that deserve more love.</p><hr><p class="mb-1 fw-semibold">For food lovers, by food lovers.</p><p class="text-muted mb-0">Explore, eat, and leave a helpful review for the community.</p></div></div></div></main>
<?php require 'includes/footer.php'; ?>
