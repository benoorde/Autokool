<?php
require __DIR__ . '/includes/bootstrap.php';
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $u = $stmt->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$u['id'];
        header('Location: /my.php');
        exit;
    }
    $error = t('bad_login');
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card reveal">
  <h1><?= e(t('login')) ?></h1>
  <?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label><?= e(t('email')) ?><input type="email" name="email" required></label>
    <label><?= e(t('password')) ?><input type="password" name="password" required></label>
    <button class="btn" type="submit"><?= e(t('submit')) ?></button>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php';
