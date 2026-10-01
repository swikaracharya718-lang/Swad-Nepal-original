<?php
require_once 'includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$restaurantId = $_POST['restaurant_id'] ?? 0;
$rating       = (int)($_POST['rating'] ?? 0);
$comment      = trim($_POST['comment'] ?? '');

if (!ctype_digit((string)$restaurantId) || $rating < 1 || $rating > 5 || $comment === '') {
    flash_set('Please provide a valid rating and comment.', 'error');
    redirect("restaurant.php?id=$restaurantId");
}

$check = $pdo->prepare("SELECT id FROM restaurants WHERE id = ?");
$check->execute([$restaurantId]);
if (!$check->fetch()) redirect('listings.php');

// Insert review
$stmt = $pdo->prepare("INSERT INTO reviews (restaurant_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->execute([$restaurantId, current_user_id(), $rating, $comment]);
$reviewId = $pdo->lastInsertId();

// Handle optional photo upload
if (!empty($_FILES['review_photo']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext     = strtolower(pathinfo($_FILES['review_photo']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed) && $_FILES['review_photo']['size'] <= 5 * 1024 * 1024) {
        $newName = uniqid('rev_', true) . '.' . $ext;
        $destDir = __DIR__ . '/uploads/';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        if (move_uploaded_file($_FILES['review_photo']['tmp_name'], $destDir . $newName)) {
            // Ensure review_images table has review_id column variant
            $pdo->exec("CREATE TABLE IF NOT EXISTS review_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                review_id INT NOT NULL,
                restaurant_id INT NOT NULL,
                image_path VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            $pdo->prepare("INSERT INTO review_images (review_id, restaurant_id, image_path) VALUES (?,?,?)")
                ->execute([$reviewId, $restaurantId, $newName]);
        }
    }
}

flash_set('Thanks for your review!', 'success');
redirect("restaurant.php?id=$restaurantId");
