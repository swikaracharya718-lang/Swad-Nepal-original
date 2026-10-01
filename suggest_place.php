<?php
require_once 'includes/functions.php';
$pageTitle = 'Suggest a Place';
$sent  = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $place_name = trim($_POST['place_name'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $city       = trim($_POST['city'] ?? '');
    $food_type  = trim($_POST['food_type'] ?? '');
    $why        = trim($_POST['why'] ?? '');
    $your_name  = trim($_POST['your_name'] ?? '');
    $your_email = trim($_POST['your_email'] ?? '');

    if ($place_name === '' || $address === '' || $why === '' || $your_name === '' || !filter_var($your_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please fill in all required fields with a valid email.';
    } else {
        // Create table if it doesn't exist yet
        $pdo->exec("CREATE TABLE IF NOT EXISTS place_suggestions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            place_name VARCHAR(150) NOT NULL,
            address VARCHAR(255) NOT NULL,
            city VARCHAR(100),
            food_type VARCHAR(100),
            why TEXT NOT NULL,
            your_name VARCHAR(100) NOT NULL,
            your_email VARCHAR(150) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        $pdo->prepare("INSERT INTO place_suggestions (place_name, address, city, food_type, why, your_name, your_email) VALUES (?,?,?,?,?,?,?)")
            ->execute([$place_name, $address, $city, $food_type, $why, $your_name, $your_email]);
        $sent = true;
    }
}

require 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="section-kicker text-warning mb-2">Community</p>
    <h1 class="mb-2">Know a hidden gem?</h1>
    <p class="mb-0">Tell us about a local food spot that deserves more love. Our team reviews every suggestion.</p>
  </div>
</section>

<main class="container py-5">
  <div class="row g-4 justify-content-center">
    <div class="col-lg-7">
      <div class="card p-4 suggest-card">

        <?php if ($sent): ?>
          <div class="text-center py-4">
            <div class="suggest-success-icon mb-3">&#10003;</div>
            <h3>Thanks for the tip!</h3>
            <p class="text-muted">We'll review your suggestion and add it to the directory if it checks out.</p>
            <a href="listings.php" class="btn btn-brand mt-2">Explore food spots</a>
          </div>
        <?php else: ?>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= clean($error) ?></div>
          <?php endif; ?>

          <h3 class="mb-1">Suggest a food spot</h3>
          <p class="text-muted mb-4">Fields marked <span class="text-danger">*</span> are required.</p>

          <form method="POST">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold">Place name <span class="text-danger">*</span></label>
                <input type="text" name="place_name" class="form-control" placeholder="e.g. Yeti Momo Corner" value="<?= clean($_POST['place_name'] ?? '') ?>" required>
              </div>
              <div class="col-md-8">
                <label class="form-label fw-semibold">Address / Landmark <span class="text-danger">*</span></label>
                <input type="text" name="address" class="form-control" placeholder="e.g. Near Ratna Park, Kathmandu" value="<?= clean($_POST['address'] ?? '') ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">City</label>
                <input type="text" name="city" class="form-control" placeholder="e.g. Kathmandu" value="<?= clean($_POST['city'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Type of food</label>
                <input type="text" name="food_type" class="form-control" placeholder="e.g. Momo, Thakali, Street food..." value="<?= clean($_POST['food_type'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Why should we add it? <span class="text-danger">*</span></label>
                <textarea name="why" class="form-control" rows="4" placeholder="Tell us what makes this place special — the food, the vibe, the price..." required><?= clean($_POST['why'] ?? '') ?></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Your name <span class="text-danger">*</span></label>
                <input type="text" name="your_name" class="form-control" value="<?= clean($_POST['your_name'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Your email <span class="text-danger">*</span></label>
                <input type="email" name="your_email" class="form-control" value="<?= clean($_POST['your_email'] ?? '') ?>" required>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-brand px-4">Submit suggestion &rarr;</button>
              </div>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card p-4 h-100" style="border-top:4px solid var(--saffron);">
        <p class="section-kicker mb-2">How it works</p>
        <h4 class="mb-3">Your tip, their next meal</h4>
        <ul class="suggest-steps">
          <li><span>1</span> You submit a place you love</li>
          <li><span>2</span> Our team verifies the details</li>
          <li><span>3</span> It goes live on Swad Nepal</li>
          <li><span>4</span> Hungry people find it!</li>
        </ul>
        <hr class="my-3">
        <p class="text-muted small mb-0">We don't accept paid listings. Every place on Swad Nepal is here because someone genuinely recommends it.</p>
      </div>
    </div>
  </div>
</main>

<style>
  .suggest-card { border-top: 4px solid var(--red); box-shadow: 0 8px 24px rgba(43,35,24,.08); border-radius: 10px; }
  .suggest-success-icon { width: 64px; height: 64px; background: var(--green); color: #fff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; }
  .suggest-steps { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: .85rem; }
  .suggest-steps li { display: flex; align-items: center; gap: .75rem; font-size: .95rem; color: var(--ink); }
  .suggest-steps li span { width: 28px; height: 28px; background: var(--saffron); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: .75rem; font-weight: 700; flex-shrink: 0; }
</style>

<?php require 'includes/footer.php'; ?>
