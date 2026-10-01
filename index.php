<?php
require_once 'includes/functions.php';
$pageTitle = 'Home';

// Featured / most recently added restaurants
$stmt = $pdo->query("
    SELECT r.*, c.name AS category_name,
           (SELECT ROUND(AVG(rating),1) FROM reviews WHERE restaurant_id = r.id) AS avg_rating,
           (SELECT COUNT(*) FROM reviews WHERE restaurant_id = r.id) AS review_count
    FROM restaurants r
    LEFT JOIN categories c ON r.category_id = c.id
    ORDER BY r.created_at DESC
    LIMIT 6
");
$featured = $stmt->fetchAll();

require 'includes/header.php';
?>

<style>
  .home-food-showcase {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    gap: 24px;
    align-items: stretch;
    background: #fffdf7;
    border: 4px solid #111111;
    box-shadow: 10px 10px 0 #111111;
    padding: 20px;
    margin-top: 28px;
    margin-bottom: 48px;
  }

  .home-food-copy {
    background: #f8f1e7;
    border: 4px solid #111111;
    padding: 22px 20px;
  }

  .home-food-copy h2 {
    margin-top: 10px;
    margin-bottom: 14px;
    font-size: clamp(2rem, 4vw, 3rem);
    line-height: 0.95;
    text-transform: uppercase;
    letter-spacing: -0.05em;
    color: #111111;
  }

  .home-food-copy p {
    margin-bottom: 18px;
    color: #3b3733;
    font-size: 1rem;
    line-height: 1.7;
  }

  .food-tag-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .food-tag-list span {
    display: inline-block;
    padding: 9px 12px;
    background: #ffcc4d;
    color: #111111;
    border: 3px solid #111111;
    box-shadow: 4px 4px 0 #111111;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .home-map-card {
    background: #f3efe7;
    border: 4px solid #111111;
    padding: 14px;
    box-shadow: 8px 8px 0 #111111;
    display: flex;
    flex-direction: column;
  }

  .home-map-card .map-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 8px 6px 12px;
    font-size: 0.7rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #111111;
  }

  .home-map-card iframe {
    width: 100%;
    min-height: 260px;
    border: 0;
    display: block;
    background: #e8dfd3;
    filter: grayscale(0.15) contrast(1.1) brightness(1.05);
  }

  @media (max-width: 820px) {
    .home-food-showcase {
      grid-template-columns: 1fr;
    }
  }
</style>

<section class="hero text-center">
  <div class="container">
    <p class="section-kicker mb-3">Nepal's local food guide</p>
    <h1>Follow your appetite<br>across Nepal.</h1>
    <p class="lead mb-4">From a steaming plate of momo to a slow Thakali lunch, discover the independent places that locals keep coming back to.</p>
    <div class="d-flex flex-wrap gap-2"><a class="btn btn-brand" href="listings.php">Explore food spots</a><a class="btn btn-outline-light rounded-1 fw-bold px-3 py-2" href="about.php">Our food story</a></div>
  </div>
</section>

<div class="container">
  <div class="search-card">
    <form action="listings.php" method="GET" class="row g-3 align-items-end">
      <div class="col-lg-5">
        <label class="form-label fw-semibold">What would you like to eat?</label>
        <input type="text" name="q" class="form-control" placeholder="Try momo, thakali, sel roti...">
      </div>
      <div class="col-lg-3">
        <label class="form-label fw-semibold">Your budget</label>
        <select name="price" class="form-select">
          <option value="">Any price</option>
          <option value="cheap">Cheap</option>
          <option value="mid">Mid-range</option>
          <option value="premium">Premium</option>
        </select>
      </div>
      <div class="col-lg-4">
        <button type="submit" class="btn btn-brand w-100">Find a food spot &rarr;</button>
      </div>
    </form>
    <div class="food-shortcuts"><span>Popular:</span><a href="listings.php?q=momo">Momo</a><a href="listings.php?q=thakali">Thakali</a><a href="listings.php?q=newari">Newari khaja</a><a href="listings.php?q=sel%20roti">Sel roti</a></div>
  </div>
</div>

<div class="container">
  <div class="home-food-showcase">
    <div class="home-food-copy">
      <p class="section-kicker mb-1">Food map</p>
      <h2>Explore Nepal by taste</h2>
      <p>From Kathmandu street snacks to mountain-side thakali dinners, find the best local dishes close to your favorite neighborhood and travel route.</p>
      <div class="food-tag-list">
        <span>Momo</span>
        <span>Thakali</span>
        <span>Sel Roti</span>
        <span>Chatamari</span>
        <span>Daal Bhat</span>
        <span>Newari Khaja</span>
      </div>
    </div>

    <div class="home-map-card">
      <div class="map-header">
        <span>Nearby food spots</span>
        <span>12+ live</span>
      </div>
      <iframe
        src="https://www.google.com/maps?q=27.7172,85.3240&z=12&output=embed"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Kathmandu food map with restaurant pin">
      </iframe>
    </div>
  </div>
</div>

<div class="container mt-5">
  <div class="discovery-strip mb-5">
    <div><span class="strip-icon">&#9670;</span><strong><?= count($featured) ?>+</strong><small>food spots</small></div>
    <div><span class="strip-icon">&#10022;</span><strong>8</strong><small>food traditions</small></div>
    <div><span class="strip-icon">&#9829;</span><strong>100%</strong><small>local love</small></div>
  </div>
  <div class="food-section-heading">
    <div>
      <p class="section-kicker mb-1">Fresh finds</p>
      <h2 class="section-heading mb-0">Recently added spots</h2>
    </div>
    <a class="d-none d-md-inline fw-semibold" href="listings.php">View all places →</a>
  </div>
  <div class="row g-4">
    <?php if (empty($featured)): ?>
      <div class="empty-state">No restaurants listed yet. Check back soon!</div>
    <?php endif; ?>
    <?php foreach ($featured as $r): ?>
      <div class="col-md-4">
        <div class="card card-restaurant">
          <img src="<?= $r['cover_image'] ? base_url('uploads/' . clean($r['cover_image'])) : restaurant_fallback_image($r['category_name'] ?? '') ?>" class="card-img-top" alt="Nepali food at <?= clean($r['name']) ?>" loading="lazy">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title mb-1"><?= clean($r['name']) ?></h5>
              <span class="badge badge-price-<?= $r['price_range'] ?> text-white"><?= ucfirst($r['price_range']) ?></span>
            </div>
            <span class="badge badge-category mb-2"><?= clean($r['category_name'] ?? 'Uncategorized') ?></span>
            <p class="card-text small text-muted mb-2"><?= clean(mb_strimwidth($r['description'] ?? '', 0, 90, '...')) ?></p>
            <?php if ($r['avg_rating']): ?>
              <div class="stars mb-2"><?= star_rating_html($r['avg_rating']) ?> <span class="text-muted small">(<?= $r['review_count'] ?>)</span></div>
            <?php endif; ?>
            <a href="restaurant.php?id=<?= $r['id'] ?>" class="btn btn-outline-brand btn-sm btn-brand">View Details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="text-center my-5">
    <a href="listings.php" class="btn btn-brand btn-lg px-4">Browse All Local Food Spots</a>
  </div>

  <section class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="card feature-card">
        <div class="feature-icon mb-3">01</div>
        <h4>Search your craving</h4>
        <p class="text-muted mb-0">Look for momo, thakali, sweets, or a neighbourhood you want to explore.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card feature-card">
        <div class="feature-icon mb-3">02</div>
        <h4>Find local favourites</h4>
        <p class="text-muted mb-0">Use honest details, price ranges, and community ratings to pick your next stop.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card feature-card">
        <div class="feature-icon mb-3">03</div>
        <h4>Share your experience</h4>
        <p class="text-muted mb-0">Leave a review to help more people discover the food worth travelling for.</p>
      </div>
    </div>
  </section>
</div>

<?php require 'includes/footer.php'; ?>