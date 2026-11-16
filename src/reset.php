<?php
require __DIR__ . '/includes/bootstrap.php';
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$state = 'form';
$stmt = db()->prepare('SELECT id, user_id FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()');
$stmt->execute([$token]);
$reset = $stmt->fetch();
if (!$reset) {
    $state = 'bad';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $pass = $_POST['password'] ?? '';
    if (strlen($pass) < 6) {
        $state = 'short';
    } else {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($pass, PASSWORD_DEFAULT), $reset['user_id']]);
        db()->prepare('UPDATE password_resets SET used = 1 WHERE id = ?')->execute([$reset['id']]);
        $state = 'ok';
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card reveal">
  <h1><?= e(t('reset_title')) ?></h1>
  <?php if ($state === 'bad'): ?>
    <div class="flash error"><?= e(t('reset_bad')) ?></div>
  <?php elseif ($state === 'ok'): ?>
    <div class="flash"><?= e(t('reset_ok')) ?></div>
    <p><a href="/login.php"><?= e(t('login')) ?></a></p>
  <?php else: ?>
    <?php if ($state === 'short'): ?><div class="flash error"><?= e(t('invalid_form')) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <label><?= e(t('password')) ?><input type="password" name="password" minlength="6" required></label>
      <button class="btn" type="submit"><?= e(t('reset_btn')) ?></button>
    </form>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php';
