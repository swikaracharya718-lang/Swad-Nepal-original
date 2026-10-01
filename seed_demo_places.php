<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config/db.php';

$columns = $pdo->query('SHOW COLUMNS FROM restaurants')->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('province', $columns, true)) {
    $pdo->exec('ALTER TABLE restaurants ADD COLUMN province VARCHAR(100) DEFAULT NULL AFTER address');
}
if (!in_array('city', $columns, true)) {
    $pdo->exec('ALTER TABLE restaurants ADD COLUMN city VARCHAR(100) DEFAULT NULL AFTER province');
}
if (!in_array('is_demo', $columns, true)) {
    $pdo->exec('ALTER TABLE restaurants ADD COLUMN is_demo TINYINT(1) NOT NULL DEFAULT 0 AFTER city');
}

$provinceCities = [
    'Koshi Province' => ['Itahari', 'Biratnagar', 'Dharan', 'Birtamod', 'Damak', 'Dhankuta', 'Inaruwa', 'Urlabari'],
    'Madhesh Province' => ['Janakpur', 'Birgunj', 'Rajbiraj', 'Lahan', 'Kalaiya', 'Jaleshwar'],
    'Bagmati Province' => ['Kathmandu', 'Lalitpur', 'Bhaktapur', 'Hetauda', 'Bharatpur', 'Dhulikhel', 'Bidur'],
    'Gandaki Province' => ['Pokhara', 'Baglung', 'Gorkha', 'Besisahar', 'Damauli', 'Waling'],
    'Lumbini Province' => ['Butwal', 'Nepalgunj', 'Tulsipur', 'Ghorahi', 'Tansen', 'Siddharthanagar'],
    'Karnali Province' => ['Birendranagar', 'Jumla', 'Dailekh', 'Salyan', 'Rukumkot'],
    'Sudurpashchim Province' => ['Dhangadhi', 'Mahendranagar', 'Tikapur', 'Dadeldhura', 'Baitadi', 'Dipayal'],
];

$categories = [
    'Newari',
    'Momo',
    'Thakali',
    'Street Food',
    'Dessert',
    'South Indian',
    'Fast Food',
    'Local Thali',
];
$categoryInsert = $pdo->prepare('INSERT IGNORE INTO categories (name) VALUES (?)');
foreach ($categories as $categoryName) {
    $categoryInsert->execute([$categoryName]);
}

$categoryQuery = $pdo->query('SELECT id, name FROM categories');
$categoryIds = [];
foreach ($categoryQuery->fetchAll() as $category) {
    $categoryIds[$category['name']] = (int)$category['id'];
}

$locationPairs = [];
foreach ($provinceCities as $provinceName => $cities) {
    foreach ($cities as $cityName) {
        $locationPairs[] = [$provinceName, $cityName];
    }
}

$legacyLocationUpdate = $pdo->prepare(
    'UPDATE restaurants SET province = ?, city = ? WHERE province IS NULL AND address LIKE ?'
);
foreach ($locationPairs as [$provinceName, $cityName]) {
    $legacyLocationUpdate->execute([$provinceName, $cityName, '%' . $cityName . '%']);
}
$legacyDemoUpdate = $pdo->prepare('UPDATE restaurants SET is_demo = 1, latitude = NULL, longitude = NULL WHERE name = ?');
foreach (['Yeti Momo Corner', 'Newari Bhoj Ghar', 'Thakali Kitchen', 'Station Chatpat Stall', 'Mishti Ghar'] as $sampleName) {
    $legacyDemoUpdate->execute([$sampleName]);
}

$dishes = [
    'Newari' => ['bara and choila', 'samay baji', 'yomari'],
    'Momo' => ['steamed momo', 'jhol momo', 'chilli momo'],
    'Thakali' => ['Thakali dal bhat', 'buckwheat dhido', 'apple sadheko'],
    'Street Food' => ['chatpate', 'panipuri', 'sekwa'],
    'Dessert' => ['sel roti', 'lal mohan', 'kheer'],
    'South Indian' => ['masala dosa', 'idli sambar', 'uttapam'],
    'Fast Food' => ['veg chowmein', 'crispy burger', 'chicken sandwich'],
    'Local Thali' => ['Nepali dal bhat', 'gundruk thali', 'seasonal tarkari set'],
];

$pdo->exec("DELETE FROM restaurants WHERE name LIKE 'Demo Food Place %' AND id NOT IN (SELECT id FROM (SELECT MIN(id) AS id FROM restaurants WHERE name LIKE 'Demo Food Place %' GROUP BY name) AS unique_demo_rows)");
$existingNames = $pdo->query("SELECT name FROM restaurants WHERE name LIKE 'Demo Food Place %'")->fetchAll(PDO::FETCH_COLUMN);
$existingLookup = array_fill_keys($existingNames, true);
$insert = $pdo->prepare(
    'INSERT INTO restaurants (name, description, address, province, city, is_demo, latitude, longitude, price_range, category_id, cover_image, added_by)
     VALUES (?, ?, ?, ?, ?, 1, NULL, NULL, ?, ?, NULL, NULL)'
);
$update = $pdo->prepare(
    'UPDATE restaurants SET description = ?, address = ?, province = ?, city = ?, is_demo = 1, latitude = NULL, longitude = NULL, price_range = ?, category_id = ? WHERE name = ?'
);

$added = 0;
$pdo->beginTransaction();
try {
    for ($number = 1; $number <= 250; $number++) {
        $name = sprintf('Demo Food Place %03d', $number);
        $locationIndex = ($number - 1) % count($locationPairs);
        $locationRound = intdiv($number - 1, count($locationPairs));
        [$provinceName, $cityName] = $locationPairs[$locationIndex];
        $categoryName = $categories[$locationRound % count($categories)];
        $categoryDishes = $dishes[$categoryName];
        $dish = $categoryDishes[intdiv($locationRound, count($categories)) % count($categoryDishes)];
        $address = 'Demo listing area, ' . $cityName . ', ' . $provinceName . ', Nepal';
        $description = 'DEMO DATA: Synthetic sample listing for ' . $dish . '. This is not a real business and is not verified on Google Maps.';
        $price = ['cheap', 'mid', 'premium'][($number - 1) % 3];

        if (isset($existingLookup[$name])) {
            $update->execute([$description, $address, $provinceName, $cityName, $price, $categoryIds[$categoryName], $name]);
        } else {
            $insert->execute([
                $name,
                $description,
                $address,
                $provinceName,
                $cityName,
                $price,
                $categoryIds[$categoryName],
            ]);
            $added++;
        }
    }
    $pdo->commit();
} catch (Throwable $error) {
    $pdo->rollBack();
    throw $error;
}

$total = (int)$pdo->query('SELECT COUNT(*) FROM restaurants')->fetchColumn();
echo 'Added ' . $added . ' demo places. Total catalog places: ' . $total . PHP_EOL;
