<?php
// Compatibility redirect for old bookmarks. Actual deletion is handled by POST in handleForms.php.
require_once __DIR__ . "/../core/auth.php";
requireLogin();
header("Location: index.php?error=" . urlencode("Please use the Delete button on the management page."));
exit;
