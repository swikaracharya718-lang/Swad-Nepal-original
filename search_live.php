<?php
require_once 'includes/functions.php';
$q = trim($_GET['q'] ?? '');
$province = $_GET['province'] ?? '';
$city = $_GET['city'] ?? '';
$category = $_GET['category'] ?? '';
$price = $_GET['price'] ?? '';

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
if (in_array($price, ['cheap', 'mid', 'premium'], true)) {
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

$count = count($restaurants);
ob_start();
?>
<p class="text-muted" id="resultCount"><?= $count ?> place<?= $count === 1 ? '' : 's' ?> found</p>
<div class="row g-4" id="listingGrid">
  <?php if (empty($restaurants)): ?>
    <div class="empty-state">No matching places found. Try clearing some filters.</div>
  <?php endif; ?>
  <?php foreach ($restaurants as $r): ?>
    <div class="col-md-4">
      <div class="card card-restaurant">
        <img src="<?= $r['cover_image'] ? base_url('uploads/' . clean($r['cover_image'])) : restaurant_fallback_image($r['category_name'] ?? '') ?>" class="card-img-top" alt="<?= clean($r['name']) ?>" loading="lazy">
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
<?php
echo ob_get_clean();
