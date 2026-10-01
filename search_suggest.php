<?php
require_once 'includes/functions.php';
$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$province = $_GET['province'] ?? '';
$city = $_GET['city'] ?? '';
$locationSql = '';
$params = [':q' => "%$q%"];
if ($province !== '') {
    $locationSql .= " AND r.province = :province";
    $params[':province'] = $province;
}
if ($city !== '') {
    $locationSql .= " AND r.city = :city";
    $params[':city'] = $city;
}

$stmt = $pdo->prepare("
    SELECT r.name, c.name AS category_name, NULL AS category_id, 'place' AS suggestion_type
    FROM restaurants r
    LEFT JOIN categories c ON r.category_id = c.id
     WHERE (r.name LIKE :q OR r.description LIKE :q OR r.address LIKE :q OR c.name LIKE :q)
     $locationSql
    ORDER BY r.name
    LIMIT 5
");
$stmt->execute($params);
$suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categoryStmt = $pdo->prepare("SELECT DISTINCT c.id AS category_id, c.name, 'Food category' AS category_name, 'category' AS suggestion_type FROM categories c INNER JOIN restaurants r ON r.category_id = c.id WHERE c.name LIKE :q $locationSql ORDER BY c.name LIMIT 3");
$categoryStmt->execute($params);
$suggestions = array_merge($suggestions, $categoryStmt->fetchAll(PDO::FETCH_ASSOC));

header('Content-Type: application/json; charset=utf-8');
echo json_encode($suggestions, JSON_UNESCAPED_UNICODE);
