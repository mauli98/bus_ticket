<?php require_once __DIR__ . '/functions.php'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title ?? 'Bus Ticket Management') ?></title>
<style>
:root{font-family:Arial,sans-serif;color:#172033;background:#f4f7fb}
body{margin:0}header{background:#123b65;color:white;padding:16px 5%;display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap}
header a{color:white;text-decoration:none;margin-right:12px}main{max-width:1100px;margin:28px auto;padding:0 16px}
.card{background:white;border:1px solid #e0e7ef;border-radius:10px;padding:20px;margin:14px 0;box-shadow:0 2px 8px #1020300b}
input,select{padding:10px;width:100%;box-sizing:border-box;border:1px solid #bbc7d5;border-radius:6px;margin:6px 0 12px}
button,.btn{background:#1769aa;color:white;padding:10px 14px;border:0;border-radius:6px;text-decoration:none;cursor:pointer;display:inline-block}
.btn.secondary{background:#526273}.btn.danger{background:#b42318}
table{width:100%;border-collapse:collapse;background:white}th,td{padding:11px;border-bottom:1px solid #e4eaf1;text-align:left}th{background:#edf3f9}
.notice{padding:12px;border-radius:6px;background:#e8f5e9;margin:12px 0}.error{background:#ffebee}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
small,.muted{color:#586579}footer{text-align:center;color:#667085;padding:28px}
</style>
</head>
<body>
<header><strong>🚌 Bus Ticket Management</strong><nav>
<a href="index.php">Home</a>
<?php if (!empty($_SESSION['user_id'])): ?>
<a href="my_bookings.php">My Bookings</a>
<?php if (is_admin()): ?><a href="admin_buses.php">Manage Buses</a><?php endif; ?>
<a href="logout.php">Logout (<?= e($_SESSION['user_name'] ?? 'User') ?>)</a>
<?php else: ?><a href="login.php">Login</a><a href="register.php">Register</a><?php endif; ?>
</nav></header><main>
