<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Login'; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id,name,email,password_hash,role FROM users WHERE email = ?');
    $stmt->execute([$email]); $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php'); exit;
    }
    $error = 'Invalid email or password.';
}
require __DIR__ . '/includes/header.php';
?>
<h1>Login</h1><div class="card">
<?php if (isset($_GET['registered'])): ?><p class="notice">Registration successful. Please log in.</p><?php endif; ?>
<?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" required>
<button type="submit">Login</button></form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
