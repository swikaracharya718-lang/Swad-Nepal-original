<?php
require_once 'includes/functions.php';

$id = $_GET['id'] ?? 0;
if (!ctype_digit((string)$id)) redirect(base_url('listings.php'));

$stmt = $pdo->prepare("SELECT r.*, c.name AS category_name FROM restaurants r LEFT JOIN categories c ON r.category_id = c.id WHERE r.id = ?");
$stmt->execute([$id]);
$restaurant = $stmt->fetch();
if (!$restaurant) redirect(base_url('listings.php'));

$pageTitle = $restaurant['name'];

$gallery = $pdo->prepare("SELECT * FROM restaurant_images WHERE restaurant_id = ?");
$gallery->execute([$id]);
$gallery = $gallery->fetchAll();

$revStmt = $pdo->prepare("SELECT rev.*, u.name AS user_name FROM reviews rev JOIN users u ON rev.user_id = u.id WHERE rev.restaurant_id = ? ORDER BY rev.created_at DESC");
$revStmt->execute([$id]);
$reviews = $revStmt->fetchAll();

$ratingInfo = $pdo->prepare("SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS total FROM reviews WHERE restaurant_id = ?");
$ratingInfo->execute([$id]);
$ratingInfo = $ratingInfo->fetch();

// Fetch review photos if table exists
$reviewPhotos = [];
try {
    $rpStmt = $pdo->prepare("SELECT * FROM review_images WHERE restaurant_id = ? ORDER BY created_at DESC");
    $rpStmt->execute([$id]);
    foreach ($rpStmt->fetchAll() as $rp) {
        $reviewPhotos[$rp['review_id']][] = $rp['image_path'];
    }
} catch (Exception $e) {}

require 'includes/header.php';
?>

<div class="container py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('index.php') ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('listings.php') ?>">Find Food</a></li>
      <li class="breadcrumb-item active"><?= clean($restaurant['name']) ?></li>
    </ol>
  </nav>

  <div class="restaurant-hero-card card row g-4">
    <div class="col-lg-7">
      <img id="mainRestaurantImage"
        src="<?= $restaurant['cover_image'] ? base_url('uploads/' . clean($restaurant['cover_image'])) : restaurant_fallback_image($restaurant['category_name'] ?? '') ?>"
        class="img-fluid restaurant-main-image w-100" style="max-height:420px;object-fit:cover;" alt="<?= clean($restaurant['name']) ?>">

      <?php if (!empty($gallery)): ?>
        <div class="d-flex gap-2 mt-2 flex-wrap">
          <?php foreach ($gallery as $g): ?>
            <img src="<?= base_url('uploads/' . clean($g['image_path'])) ?>" class="gallery-thumb" alt="gallery photo">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-5">
      <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <h1 class="mb-1"><?= clean($restaurant['name']) ?></h1>
        <span class="badge badge-price-<?= $restaurant['price_range'] ?> text-white fs-6"><?= ucfirst($restaurant['price_range']) ?></span>
      </div>
      <div class="restaurant-meta">
        <span class="badge badge-category"><?= clean($restaurant['category_name'] ?? 'Uncategorized') ?></span>
        <?php if (!empty($restaurant['is_demo'])): ?>
          <span class="badge bg-warning text-dark">Demo listing · not verified</span>
        <?php else: ?>
          <span class="badge bg-light text-dark border">&#10003; Local favourite</span>
        <?php endif; ?>
      </div>
      <p class="restaurant-address mb-2"><span aria-hidden="true">&#9670;</span> <span><?= clean($restaurant['address']) ?></span></p>

      <?php if ($ratingInfo['total'] > 0): ?>
        <div class="rating-summary mb-3">
          <span class="stars fs-5"><?= star_rating_html($ratingInfo['avg_rating']) ?></span>
          <span class="text-muted"><?= $ratingInfo['avg_rating'] ?> &middot; <?= $ratingInfo['total'] ?> review<?= $ratingInfo['total'] == 1 ? '' : 's' ?></span>
        </div>
      <?php else: ?>
        <p class="text-muted mb-3">No reviews yet — be the first!</p>
      <?php endif; ?>

      <p class="mt-2"><?= nl2br(clean($restaurant['description'])) ?></p>

      <?php if ($restaurant['latitude'] && $restaurant['longitude']): ?>
        <iframe class="map-embed mt-3" loading="lazy" allowfullscreen
          src="https://www.google.com/maps?q=<?= $restaurant['latitude'] ?>,<?= $restaurant['longitude'] ?>&z=15&output=embed">
        </iframe>
      <?php endif; ?>
    </div>
  </div>

  <div class="review-section mt-5 row g-4">
    <div class="col-lg-7">
      <h3 class="mb-3">Reviews <?php if ($ratingInfo['total'] > 0): ?><span class="text-muted fs-6 fw-normal">(<?= $ratingInfo['total'] ?>)</span><?php endif; ?></h3>

      <?php if (empty($reviews)): ?>
        <p class="text-muted">No reviews yet. Be the first to share your experience!</p>
      <?php endif; ?>

      <?php foreach ($reviews as $rev): ?>
        <div class="review-item">
          <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
            <div>
              <strong><?= clean($rev['user_name']) ?></strong>
              <small class="text-muted ms-2"><?= date('d M Y', strtotime($rev['created_at'])) ?></small>
            </div>
            <span class="stars"><?= star_rating_html($rev['rating']) ?></span>
          </div>
          <p class="mb-2 mt-1"><?= nl2br(clean($rev['comment'])) ?></p>
          <?php if (!empty($reviewPhotos[$rev['id']])): ?>
            <div class="d-flex flex-wrap gap-2 mt-1">
              <?php foreach ($reviewPhotos[$rev['id']] as $rp): ?>
                <img src="<?= base_url('uploads/' . clean($rp)) ?>" class="review-photo-thumb" alt="Review photo">
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="col-lg-5">
      <div class="card review-form-card p-3 p-md-4">
        <h5 class="mb-3">Leave a Review</h5>
        <?php if (!is_logged_in()): ?>
          <p class="text-muted">Please <a href="<?= base_url('login.php') ?>">log in</a> to leave a review.</p>
          <a href="<?= base_url('login.php') ?>" class="btn btn-brand w-100">Log in to review</a>
        <?php else: ?>
          <form action="add_review.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="restaurant_id" value="<?= $restaurant['id'] ?>">
            <input type="hidden" name="rating" id="ratingInput" value="5">

            <div class="mb-3">
              <label class="form-label fw-semibold">Your rating</label><br>
              <span id="starWidget" class="fs-2 stars" style="cursor:pointer;letter-spacing:4px;">
                <span data-value="1">★</span><span data-value="2">★</span><span data-value="3">★</span><span data-value="4">★</span><span data-value="5">★</span>
              </span>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Your experience</label>
              <textarea name="comment" class="form-control" rows="4" placeholder="How was the food, service, and vibe?" required></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Add a photo <span class="text-muted fw-normal">(optional)</span></label>
              <input type="file" name="review_photo" class="form-control" accept="image/jpeg,image/png,image/webp">
              <div class="form-text">JPG, PNG or WebP · max 5 MB</div>
            </div>

            <button type="submit" class="btn btn-brand w-100">Submit Review</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
  .review-photo-thumb { width: 90px; height: 72px; object-fit: cover; border-radius: 5px; border: 1px solid var(--line); cursor: pointer; transition: transform .15s; }
  .review-photo-thumb:hover { transform: scale(1.05); }
  @media (max-width: 575px) {
    .restaurant-hero-card { margin-left: 0; margin-right: 0; }
    .restaurant-main-image { max-height: 260px !important; }
  }
</style>

<?php require 'includes/footer.php'; ?>
