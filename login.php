<?php
require_once __DIR__ . "/../core/dbConfig.php";
require_once __DIR__ . "/../core/auth.php";
if (!empty($_SESSION["user_id"])) { header("Location: index.php"); exit; }
$error = ""; $message = $_GET["message"] ?? "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]); $user = $stmt->fetch();
    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int)$user["user_id"];
        $_SESSION["user_name"] = $user["first_name"] . " " . $user["last_name"];
        header("Location: index.php"); exit;
    }
    $error = "Invalid email or password.";
}
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Login | Shoe Store</title>
        <link rel="stylesheet" href="style.css"></head>
    <body>
        <div class="auth-wrap">
            <section class="panel auth-panel">
                <h1>Welcome back</h1>
                <p>Log in to the Shoe Store Management System.</p>
<?php if($message): ?><div class="notice success"><?=h($message)?></div><?php endif; ?>
<?php if($error): ?><div class="notice error"><?=h($error)?></div><?php endif; ?>
<form method="POST" class="form-grid">
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <button type="submit">Log In</button>
</form>
<p>Don't have an account? <a href="register.php">Register Here</a>
</p>
</section>
</div>
</body>
</html>
