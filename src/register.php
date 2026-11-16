<?php
require __DIR__ . '/includes/bootstrap.php';
$error = null;
$ok = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        $error = 'invalid';
    } else {
        $exists = db()->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$email]);
        if ($exists->fetch()) {
            $error = 'email_taken';
        } else {
            db()->prepare('INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)')
                ->execute([$name, $email, $phone, password_hash($pass, PASSWORD_DEFAULT)]);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            header('Location: /my.php');
            exit;
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card reveal">
  <h1><?= e(t('register')) ?></h1>
  <?php if ($error): ?><div class="flash error"><?= e($error === 'email_taken' ? t('email_taken') : t('invalid_form')) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label><?= e(t('name')) ?><input name="name" required></label>
    <label><?= e(t('email')) ?><input type="email" name="email" required></label>
    <label><?= e(t('phone')) ?><input name="phone"></label>
    <label><?= e(t('password')) ?><input type="password" name="password" minlength="6" required></label>
    <button class="btn" type="submit"><?= e(t('submit')) ?></button>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php';
