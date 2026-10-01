<?php
require_once 'includes/functions.php';
$pageTitle = 'Find Local Food';

// Read filters from GET
$q        = trim($_GET['q'] ?? '');
$price    = $_GET['price'] ?? '';
$category = $_GET['category'] ?? '';
$province = $_GET['province'] ?? '';
$city     = $_GET['city'] ?? '';

// Build query dynamically but safely (prepared statement)
$sql = "
    SELECT r.*, c.name AS category_name,
           (SELECT ROUND(AVG(rating),1) FROM reviews WHERE restaurant_id = r.id) AS avg_rating,
           (SELECT COUNT(*) FROM reviews WHERE restaurant_id = r.id) AS review_count
    FROM restaurants r
    LEFT JOIN categories c ON r.category_id = c.id
    WHERE 1=1
";
$params = [];

if ($q !== '') {
  $sql .= " AND (r.name LIKE :q OR r.description LIKE :q OR r.address LIKE :q OR c.name LIKE :q)";
  $params[':q'] = "%$q%";
}
if ($price !== '' && in_array($price, ['cheap', 'mid', 'premium'])) {
  $sql .= " AND r.price_range = :price";
  $params[':price'] = $price;
}
if ($category !== '' && ctype_digit($category)) {
  $sql .= " AND r.category_id = :category";
  $params[':category'] = $category;
}
if ($province !== '') {
  $sql .= " AND r.province = :province";
  $params[':province'] = $province;
}
if ($city !== '') {
  $sql .= " AND r.city = :city";
  $params[':city'] = $city;
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$restaurants = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$locations = $pdo->query("SELECT DISTINCT province, city FROM restaurants WHERE province IS NOT NULL AND city IS NOT NULL ORDER BY province, city")->fetchAll();
$provinces = array_values(array_unique(array_column($locations, 'province')));

require 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="section-kicker text-warning mb-2">Explore Nepal</p>
    <h1 class="mb-2">Good food is closer than you think.</h1>
    <p class="mb-0">Search independent kitchens, neighbourhood favourites, and dishes locals love.</p>
  </div>
</section>

<div class="container py-5">

  <form method="GET" class="row g-3 mb-4 bg-white p-3 filter-panel shadow-sm" id="listingFilters">
    <div class="col-md-6 col-lg-3 search-wrapper">
      <input type="text" id="searchInput" name="q" value="<?= clean($q) ?>" class="form-control" placeholder="Search by name, dish, or area..." autocomplete="off">
      <ul class="search-suggestions" id="searchSuggestions"></ul>
    </div>
    <div class="col-md-6 col-lg-2">
      <select name="province" id="provinceFilter" class="form-select">
        <option value="">All Provinces</option>
        <?php foreach ($provinces as $provinceName): ?>
          <option value="<?= clean($provinceName) ?>" <?= $province === $provinceName ? 'selected' : '' ?>><?= clean($provinceName) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6 col-lg-2">
      <select name="city" id="cityFilter" class="form-select">
        <option value="">All Places</option>
        <?php foreach ($locations as $location): ?>
          <option value="<?= clean($location['city']) ?>" data-province="<?= clean($location['province']) ?>" <?= $city === $location['city'] ? 'selected' : '' ?>><?= clean($location['city']) ?>, <?= clean($location['province']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6 col-lg-2">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $category == $c['id'] ? 'selected' : '' ?>><?= clean($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6 col-lg-1">
      <select name="price" class="form-select">
        <option value="">Any Price</option>
        <option value="cheap" <?= $price === 'cheap' ? 'selected' : '' ?>>Cheap</option>
        <option value="mid" <?= $price === 'mid' ? 'selected' : '' ?>>Mid-range</option>
        <option value="premium" <?= $price === 'premium' ? 'selected' : '' ?>>Premium</option>
      </select>
    </div>
    <div class="col-md-6 col-lg-2">
      <button type="submit" class="btn btn-brand w-100">Filter</button>
    </div>
  </form>

  <div id="liveListingArea">
    <p class="text-muted"><?= count($restaurants) ?> place<?= count($restaurants) === 1 ? '' : 's' ?> found</p>
    <div class="row g-4">
      <?php if (empty($restaurants)): ?>
        <div class="empty-state">No matching places found. Try clearing some filters.</div>
      <?php endif; ?>
      <?php foreach ($restaurants as $r): ?>
        <div class="col-md-4">
          <div class="card card-restaurant">
            <img src="<?= $r['cover_image'] ? base_url('uploads/' . clean($r['cover_image'])) : restaurant_fallback_image($r['category_name'] ?? '') ?>" class="card-img-top" alt="Nepali food at <?= clean($r['name']) ?>" loading="lazy">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <h5 class="card-title mb-1"><?= clean($r['name']) ?></h5>
                <span class="badge badge-price-<?= $r['price_range'] ?> text-white"><?= ucfirst($r['price_range']) ?></span>
              </div>
              <span class="badge badge-category mb-2"><?= clean($r['category_name'] ?? 'Uncategorized') ?></span>
              <?php if (!empty($r['is_demo'])): ?><span class="badge bg-warning text-dark mb-2">Demo listing · not verified</span><?php endif; ?>
              <p class="card-text small text-muted mb-1"><?= clean($r['address']) ?></p>
              <p class="card-text small mb-2"><?= clean(mb_strimwidth($r['description'] ?? '', 0, 80, '...')) ?></p>
              <?php if ($r['avg_rating']): ?>
                <div class="stars mb-2"><?= star_rating_html($r['avg_rating']) ?> <span class="text-muted small">(<?= $r['review_count'] ?>)</span></div>
              <?php else: ?>
                <div class="text-muted small mb-2">No reviews yet</div>
              <?php endif; ?>
              <a href="restaurant.php?id=<?= $r['id'] ?>" class="btn btn-brand btn-sm">View Details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>