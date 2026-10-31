<?php
require_once __DIR__ . "/../core/models.php";
requireLogin();
$type = $_GET["type"] ?? ""; $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || !in_array($type, ["brand","shoe"], true)) { header("Location: index.php?error=Invalid%20record"); exit; }
$record = $type === "brand" ? getBrand($pdo,$id) : getShoe($pdo,$id);
if (!$record) { header("Location: index.php?error=Record%20not%20found"); exit; }
$brands = getBrands($pdo);
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Edit <?=h($type)?></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container narrow">
            <section class="panel">
                <h1>Edit <?=h($type)?></h1>
<?php if($type==="brand"): ?>
    <form action="../core/handleForms.php" method="POST" class="form-grid">
        <input type="hidden" name="action" value="update_brand">
        <input type="hidden" name="brand_id" value="<?=h($record["brand_id"])?>">
        <label>Brand name<input name="brand_name" value="<?=h($record["brand_name"])?>" required>
    </label>
    <button>Save Changes</button>
</form>
<?php else: ?>
    <form action="../core/handleForms.php" method="POST" class="form-grid">
        <input type="hidden" name="action" value="update_shoe">
        <input type="hidden" name="shoe_id" value="<?=h($record["shoe_id"])?>">
        <label>Shoe name<input name="shoe_name" value="<?=h($record["shoe_name"])?>" required>
    </label>
    <label>Price<input type="number" step="0.01" min="0" name="price" value="<?=h($record["price"])?>" required></label>
    <label>Size<input type="number" min="1" max="60" name="shoe_size" value="<?=h($record["shoe_size"])?>" required></label>
    <label>Stock<input type="number" min="0" name="stock_quantity" value="<?=h($record["stock_quantity"])?>" required></label>
    <label>Brand<select name="brand_id"><?php foreach($brands as $b): 
        ?><option value="<?=h($b["brand_id"])?>"<?=((int)$b["brand_id"]===(int)$record["brand_id"])?"selected":""?>><?=h($b["brand_name"])?></option>
        <?php endforeach; ?></select>
    </label>
    <button>Save Changes</button>
</form><?php endif; ?>
<p>
    <a href="index.php">← Back</a>
</p>
</section>
</div>
</body>
</html>
