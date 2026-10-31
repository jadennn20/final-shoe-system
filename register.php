<?php
require_once __DIR__ . "/../core/dbConfig.php";
require_once __DIR__ . "/../core/auth.php";
if (!empty($_SESSION["user_id"])) { header("Location: index.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first = trim($_POST["first_name"] ?? "");
    $last = trim($_POST["last_name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $address = trim($_POST["address"] ?? "");
    $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);
    $password = $_POST["password"] ?? "";
    if (!$first || !$last || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$address ||
        !$age || $age < 1 || $age > 120 || strlen($password) < 8) {
        $error = "Please complete all fields. Password must be at least 8 characters.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (first_name,last_name,email,address,age,password) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$first,$last,$email,$address,$age,password_hash($password, PASSWORD_DEFAULT)]);
            header("Location: login.php?message=" . urlencode("Registration successful. Please log in.")); exit;
        } catch (PDOException $e) { $error = "That email address may already be registered."; }
    }
}
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register | Shoe Store</title><link rel="stylesheet" href="style.css"></head>
<body><div class="auth-wrap">
    <section class="panel auth-panel">
        <h1>Create an Account</h1>
        <p>Register to manage the Shoe Store Inventory.</p>
<?php if($error): ?><div class="notice error"><?=h($error)?></div><?php endif; ?>
<form method="POST" class="form-grid">
<label>First Name<input name="first_name" maxlength="80" required></label>
<label>Last Name<input name="last_name" maxlength="80" required></label>
<label>Email<input type="email" name="email" maxlength="150" required></label>
<label>Address<input name="address" maxlength="255" required></label>
<label>Age<input type="number" name="age" min="1" max="120" required></label>
<label>Password (at least 8 characters)<input type="password" name="password" minlength="8" required></label>
<button type="submit">Register</button></form><p>Already registered? <a href="login.php">Log In</a></p></section></div></body></html>
