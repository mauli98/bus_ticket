<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Register';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || !$email || strlen($password) < 8) {
        $error = 'Enter a name, valid email, and password of at least 8 characters.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name,email,phone,password_hash,role) VALUES (?,?,?,?,?)');
            $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT), 'customer']);
            header('Location: login.php?registered=1'); exit;
        } catch (PDOException $e) {
            $error = 'That email may already be registered. Please try another email.';
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<h1>Create account</h1><div class="card">
<?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Full name</label><input name="name" required maxlength="100">
<label>Email</label><input type="email" name="email" required maxlength="190">
<label>Phone</label><input name="phone" maxlength="30">
<label>Password (minimum 8 characters)</label><input type="password" name="password" minlength="8" required>
<button type="submit">Register</button></form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
