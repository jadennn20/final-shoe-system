<?php
require_once __DIR__ . "/models.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../sql/index.php"); exit;
}
$action = $_POST["action"] ?? "";
$userId = currentUserId();

try {
    if ($action === "add_brand" || $action === "update_brand") {
        $name = trim($_POST["brand_name"] ?? "");
        if ($name === "") throw new RuntimeException("Brand name is required.");
        if ($action === "add_brand") addBrand($pdo, $name, $userId);
        else {
            $id = filter_input(INPUT_POST, "brand_id", FILTER_VALIDATE_INT);
            if (!$id) throw new RuntimeException("Invalid brand ID.");
            updateBrand($pdo, $id, $name, $userId);
        }
        header("Location: ../sql/index.php?message=" . urlencode("Brand saved successfully.")); exit;
    }
    if ($action === "add_shoe" || $action === "update_shoe") {
        $name = trim($_POST["shoe_name"] ?? "");
        $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
        $size = filter_input(INPUT_POST, "shoe_size", FILTER_VALIDATE_INT);
        $stock = filter_input(INPUT_POST, "stock_quantity", FILTER_VALIDATE_INT);
        $brandId = filter_input(INPUT_POST, "brand_id", FILTER_VALIDATE_INT);
        if ($name === "" || $price === false || $price === null || $price < 0 ||
            !$size || $size < 1 || $stock === false || $stock === null || $stock < 0 || !$brandId) {
            throw new RuntimeException("Enter valid shoe details.");
        }
        if ($action === "add_shoe") addShoe($pdo, $name, (float)$price, (int)$size, (int)$stock, (int)$brandId, $userId);
        else {
            $id = filter_input(INPUT_POST, "shoe_id", FILTER_VALIDATE_INT);
            if (!$id) throw new RuntimeException("Invalid shoe ID.");
            updateShoe($pdo, $id, $name, (float)$price, (int)$size, (int)$stock, (int)$brandId, $userId);
        }
        header("Location: ../sql/index.php?message=" . urlencode("Shoe saved successfully.")); exit;
    }
    if ($action === "delete_brand" || $action === "delete_shoe") {
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        if (!$id) throw new RuntimeException("Invalid record ID.");
        if ($action === "delete_brand") deleteBrand($pdo, $id, $userId);
        else deleteShoe($pdo, $id, $userId);
        header("Location: ../sql/index.php?message=" . urlencode("Record deleted successfully.")); exit;
    }
    throw new RuntimeException("Unknown form action.");
} catch (PDOException $e) {
    $message = "Database operation failed. A brand with existing shoes cannot be deleted.";
    header("Location: ../sql/index.php?error=" . urlencode($message)); exit;
} catch (RuntimeException $e) {
    header("Location: ../sql/index.php?error=" . urlencode($e->getMessage())); exit;
}
