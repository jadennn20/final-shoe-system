<?php
require_once __DIR__ . "/../core/models.php"; requireLogin();
$shoes = getShoes($pdo);
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Shoe Inventory</title>
<link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container">
<header>
    <h1>Shoe Inventory</h1>
<p>All Products and their Brands.</p>
</header>
<section class="panel">
    <div class="table-wrap">
    <table>
    <thead><tr>
         <th>ID</th>
         <th>Shoe</th>
         <th>Brand</th>
         <th>Price</th>
         <th>Size</th>
         <th>Stock</th>
         <th>Added by</th>
         <th>Updated by</th>
         <th>Last updated</th>
    </tr></thead>
<tbody><?php foreach($shoes as $s): ?><tr>
    <td><?=h($s["shoe_id"])?></td>
    <td><?=h($s["shoe_name"])?></td>
    <td><?=h($s["brand_name"])?></td>
    <td>₱<?=number_format((float)$s["price"],2)?></td>
    <td><?=h($s["shoe_size"])?></td>
    <td><?=h($s["stock_quantity"])?></td>
    <td><?=h($s["added_by"]??"Legacy")?></td>
    <td><?=h($s["updated_by"]??"—")?></td>
    <td><?=h($s["last_updated"])?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<p>
    <a href="index.php">
    ← Back to Management Page
</a>
</p>
</section>
</div>
</body>
</html>