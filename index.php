<?php
require_once __DIR__ . "/../core/models.php";
requireLogin();
$brands = getBrands($pdo); $shoes = getShoes($pdo);
$message = $_GET["message"] ?? ""; $error = $_GET["error"] ?? "";
function h($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shoe Store Management System</title>
        <link rel="stylesheet" href="style.css">
    </head>
<body>
    <div class="container">
<header class="topbar">
    <div>
        <h1>Shoe Store Management System</h1>
        <p>Welcome, <?=h($_SESSION["user_name"] ?? "User")?>!</p>
    </div>
    <nav>
        <a class="button" href="activity_logs.php">Activity Logs</a>
        <a class="button danger" href="logout.php">Logout</a></nav></header>
<?php if($message): ?>
    <div class="notice success"><?=h($message)?></div>
    <?php endif; ?><?php if($error): ?>
        <div class="notice error"><?=h($error)?></div><?php endif; ?>
<section class="panel">
    <h2>Add a Brand</h2>
    <form action="../core/handleForms.php" method="POST" class="form-grid">
        <input type="hidden" name="action" value="add_brand">
        <label>Brand name<input name="brand_name" maxlength="100" placeholder="e.g. Nike" required></label>
        <button>Add Brand</button>
    </form>
</section>
<section class="panel">
    <h2>Brands — Parent Entity</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Brand</th>
                    <th>Shoes</th>
                    <th>Added by (User ID)</th>
                    <th>Last updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
<?php foreach($brands as $b): ?>
    <tr>
        <td><?=h($b["brand_id"])?></td>
        <td><?=h($b["brand_name"])?></td>
        <td><?=h($b["shoe_count"])?></td>
        <td><?=h($b["added_by"] ?? "Legacy record")?></td>
        <td><?=h($b["last_updated"])?></td>
        <td class="actions">
            <a class="button small" href="editwebdev.php?type=brand&id=<?=h($b["brand_id"])?>">Edit</a>
            <form method="POST" action="../core/handleForms.php" onsubmit="return confirm('Delete this brand?');">
                <input type="hidden" name="action" value="delete_brand">
                <input type="hidden" name="id" value="<?=h($b["brand_id"])?>">
                <button class="small danger">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
<?php if(!$brands): ?>
    <tr>
        <td colspan="6">No brands yet.</td>
    </tr>
    <?php endif; ?>
</tbody>
</table>
</div>
</section>
<section class="panel">
    <h2>Add a Shoe</h2>
    <?php if(!$brands): ?>
        <p>Add a brand first.</p>
        <?php else: ?>
            <form action="../core/handleForms.php" method="POST" class="form-grid">
                <input type="hidden" name="action" value="add_shoe">
                <label>Shoe name<input name="shoe_name" maxlength="100" required></label>
                <label>Price (₱)<input type="number" name="price" min="0" step="0.01" required></label>
                <label>Shoe size<input type="number" name="shoe_size" min="1" max="60" required></label>
                <label>Stock quantity<input type="number" name="stock_quantity" min="0" required></label>
                <label>Brand<select name="brand_id" required><?php foreach($brands as $b): ?>
                    <option value="<?=h($b["brand_id"])?>"><?=h($b["brand_name"])?></option>
                    <?php endforeach; ?>
                </select>
                </label>
                <button>Add Shoe</button>
            </form><?php endif; ?>
        </section>
<section class="panel">
    <h2>Shoes — Child Entity</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Shoe</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Size</th>
                    <th>Stock</th>
                    <th>Added by</th>
                    <th>Updated by</th>
                    <th>Last updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
<?php foreach($shoes as $s): ?>
    <tr>
        <td><?=h($s["shoe_id"])?></td>
        <td><?=h($s["shoe_name"])?></td>
        <td><?=h($s["brand_name"])?></td>
        <td>₱<?=number_format((float)$s["price"],2)?></td>
        <td><?=h($s["shoe_size"])?></td>
        <td><?=h($s["stock_quantity"])?></td>
        <td><?=h($s["added_by"] ?? "Legacy")?></td>
        <td><?=h($s["updated_by"] ?? "—")?></td>
        <td><?=h($s["last_updated"])?></td>
        <td class="actions">
            <a class="button small" href="editwebdev.php?type=shoe&id=<?=h($s["shoe_id"])?>">Edit</a>
            <form method="POST" action="../core/handleForms.php" onsubmit="return confirm('Delete this shoe?');">
                <input type="hidden" name="action" value="delete_shoe">
                <input type="hidden" name="id" value="<?=h($s["shoe_id"])?>">
                <button class="small danger">Delete</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
<?php if(!$shoes): ?>
    <tr>
        <td colspan="10">No shoes yet.</td>
    </tr><?php endif; ?>
</tbody>
</table>
</div>
<p>
    <a href="viewprojects.php">Open Shoe Inventory View</a>
</p>
</section>
<footer>PHP PDO + MySQL · User authentication and activity tracking</footer>
</div>
</body>
</html>
