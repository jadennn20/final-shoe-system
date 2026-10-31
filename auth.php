<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void {
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function currentUserId(): int {
    return (int)($_SESSION["user_id"] ?? 0);
}
