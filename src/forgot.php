<?php
require __DIR__ . '/includes/bootstrap.php';
$sent = false;
$link = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim($_POST['email'] ?? '');
    $stmt = db()->prepare('SELECT id, email FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u) {
        $token = bin2hex(random_bytes(24));
        db()->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))')
            ->execute([$u['id'], $token]);
        $link = 'http://localhost:8080/reset.php?token=' . $token;
        db()->prepare('INSERT INTO mail_log (to_email, subject, body) VALUES (?, ?, ?)')
            ->execute([$u['email'], t('reset_subject'), sprintf(t('reset_body'), $link)]);
    }
    $sent = true;
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card reveal">
  <h1><?= e(t('forgot_title')) ?></h1>
  <p><?= e(t('forgot_text')) ?></p>
  <?php if ($sent): ?>
    <div class="flash"><?= e(t('forgot_sent')) ?></div>
    <?php if ($link): ?>
      <p><?= e(t('reset_link')) ?><br><a href="<?= e($link) ?>"><?= e($link) ?></a></p>
    <?php endif; ?>
  <?php else: ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label><?= e(t('email')) ?><input type="email" name="email" required></label>
    <button class="btn" type="submit"><?= e(t('forgot_btn')) ?></button>
  </form>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php';
