<?php
require_once __DIR__ . "/../core/models.php"; requireLogin();
$logs = getActivityLogs($pdo);
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Activity Logs</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container">
            <header>
                <h1>User Activity Logs</h1>
                <p>Recent create, update, and delete actions.</p>
            </header>
            <section class="panel">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Entity</th>
                                <th>Record ID</th>
                                <th>Date and Time</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach($logs as $l): ?>
                            <tr>
                                <td><?=h($l["first_name"]." ".$l["last_name"])?></td>
                                <td><?=h($l["action_type"])?></td>
                                <td><?=h($l["entity_name"])?></td>
                                <td><?=h($l["record_id"])?></td>
                                <td><?=h($l["created_at"])?></td>
                            </tr><?php endforeach; ?><?php if(!$logs): ?>
                                <tr>
                                    <td colspan="5">No Activity Recorded yet.</td>
                                </tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <p>
                        <a href="index.php">← Back</a>
                    </p>
                </section>
            </div>
        </body>
        </html>
