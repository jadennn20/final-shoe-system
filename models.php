<?php
require_once __DIR__ . "/dbConfig.php";
require_once __DIR__ . "/auth.php";

function getBrands(PDO $pdo): array {
    $stmt = $pdo->query(
        "SELECT b.brand_id, b.brand_name, b.added_by, b.last_updated,
                u.first_name, u.last_name, COUNT(s.shoe_id) AS shoe_count
         FROM brands b
         LEFT JOIN users u ON u.user_id = b.added_by
         LEFT JOIN shoes s ON s.brand_id = b.brand_id
         GROUP BY b.brand_id, b.brand_name, b.added_by, b.last_updated, u.first_name, u.last_name
         ORDER BY b.brand_name"
    );
    return $stmt->fetchAll();
}
function getBrand(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM brands WHERE brand_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}
function addBrand(PDO $pdo, string $name, int $userId): bool {
    $stmt = $pdo->prepare("INSERT INTO brands (brand_name, added_by) VALUES (?, ?)");
    $ok = $stmt->execute([$name, $userId]);
    if ($ok) logActivity($pdo, $userId, "CREATE", "brands", (int)$pdo->lastInsertId());
    return $ok;
}
function updateBrand(PDO $pdo, int $id, string $name, int $userId): bool {
    $stmt = $pdo->prepare("UPDATE brands SET brand_name = ?, updated_by = ? WHERE brand_id = ?");
    $ok = $stmt->execute([$name, $userId, $id]);
    if ($ok) logActivity($pdo, $userId, "UPDATE", "brands", $id);
    return $ok;
}
function deleteBrand(PDO $pdo, int $id, int $userId): bool {
    $stmt = $pdo->prepare("DELETE FROM brands WHERE brand_id = ?");
    $ok = $stmt->execute([$id]);
    if ($ok && $stmt->rowCount() > 0) logActivity($pdo, $userId, "DELETE", "brands", $id);
    return $ok;
}
function getShoes(PDO $pdo): array {
    $stmt = $pdo->query(
        "SELECT s.shoe_id, s.shoe_name, s.price, s.shoe_size, s.stock_quantity,
                s.brand_id, b.brand_name, s.added_by, s.updated_by, s.last_updated,
                ua.first_name AS added_first, ua.last_name AS added_last,
                uu.first_name AS updated_first, uu.last_name AS updated_last
         FROM shoes s
         INNER JOIN brands b ON b.brand_id = s.brand_id
         LEFT JOIN users ua ON ua.user_id = s.added_by
         LEFT JOIN users uu ON uu.user_id = s.updated_by
         ORDER BY s.shoe_id DESC"
    );
    return $stmt->fetchAll();
}
function getShoe(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM shoes WHERE shoe_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}
function addShoe(PDO $pdo, string $name, float $price, int $size, int $stock, int $brandId, int $userId): bool {
    $stmt = $pdo->prepare(
        "INSERT INTO shoes (shoe_name, price, shoe_size, stock_quantity, brand_id, added_by)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $ok = $stmt->execute([$name, $price, $size, $stock, $brandId, $userId]);
    if ($ok) logActivity($pdo, $userId, "CREATE", "shoes", (int)$pdo->lastInsertId());
    return $ok;
}
function updateShoe(PDO $pdo, int $id, string $name, float $price, int $size, int $stock, int $brandId, int $userId): bool {
    $stmt = $pdo->prepare(
        "UPDATE shoes SET shoe_name=?, price=?, shoe_size=?, stock_quantity=?, brand_id=?, updated_by=?
         WHERE shoe_id=?"
    );
    $ok = $stmt->execute([$name, $price, $size, $stock, $brandId, $userId, $id]);
    if ($ok) logActivity($pdo, $userId, "UPDATE", "shoes", $id);
    return $ok;
}
function deleteShoe(PDO $pdo, int $id, int $userId): bool {
    $stmt = $pdo->prepare("DELETE FROM shoes WHERE shoe_id = ?");
    $ok = $stmt->execute([$id]);
    if ($ok && $stmt->rowCount() > 0) logActivity($pdo, $userId, "DELETE", "shoes", $id);
    return $ok;
}
function logActivity(PDO $pdo, int $userId, string $action, string $entity, int $recordId): void {
    $stmt = $pdo->prepare(
        "INSERT INTO activity_logs (user_id, action_type, entity_name, record_id)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$userId, $action, $entity, $recordId]);
}
function getActivityLogs(PDO $pdo): array {
    $stmt = $pdo->query(
        "SELECT l.*, u.first_name, u.last_name
         FROM activity_logs l INNER JOIN users u ON u.user_id = l.user_id
         ORDER BY l.created_at DESC LIMIT 100"
    );
    return $stmt->fetchAll();
}
