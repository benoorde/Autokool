<?php
require __DIR__ . '/../includes/bootstrap.php';
$user = current_user();
if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    exit('403');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $enrollId = (int)$_POST['id'];
    $confirm = ($_POST['action'] ?? '') === 'confirm';
    $status = $confirm ? 'confirmed' : 'cancelled';
    db()->prepare('UPDATE enrollments SET status = ? WHERE id = ?')->execute([$status, $enrollId]);

    // Kirjutame kirja õppijale (logisse)
    $info = db()->prepare('SELECT u.email, c.title_ru, c.title_et FROM enrollments en
                           JOIN users u ON u.id = en.user_id JOIN courses c ON c.id = en.course_id
                           WHERE en.id = ?');
    $info->execute([$enrollId]);
    if ($row = $info->fetch()) {
        $subject = $confirm ? t('confirm_subject') : t('cancel_subject');
        $body = sprintf($confirm ? t('confirm_body') : t('cancel_body'), loc($row, 'title'));
        db()->prepare('INSERT INTO mail_log (to_email, subject, body) VALUES (?, ?, ?)')
            ->execute([$row['email'], $subject, $body]);
    }
    header('Location: /admin/index.php');
    exit;
}

require __DIR__ . '/../includes/header.php';
$rows = db()->query('SELECT en.id, en.status, en.created_at, u.name AS user_name, u.email,
                            c.title_ru, c.title_et
                     FROM enrollments en
                     JOIN users u ON u.id = en.user_id
                     JOIN courses c ON c.id = en.course_id
                     ORDER BY en.created_at DESC')->fetchAll();
$mails = db()->query('SELECT to_email, subject, created_at FROM mail_log ORDER BY id DESC LIMIT 20')->fetchAll();
?>
<section class="reveal">
  <h1><?= e(t('admin')) ?></h1>
  <table class="table">
    <thead><tr><th><?= e(t('user')) ?></th><th><?= e(t('course')) ?></th><th><?= e(t('date')) ?></th><th><?= e(t('status')) ?></th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td data-label="<?= e(t('user')) ?>"><?= e($r['user_name']) ?><br><small><?= e($r['email']) ?></small></td>
        <td data-label="<?= e(t('course')) ?>"><?= e(loc($r, 'title')) ?></td>
        <td data-label="<?= e(t('date')) ?>"><?= e(date('d.m.Y H:i', strtotime($r['created_at']))) ?></td>
        <td data-label="<?= e(t('status')) ?>"><span class="status s-<?= e($r['status']) ?>"><?= e(t('status_' . $r['status'])) ?></span></td>
        <td>
          <form method="post" class="inline">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button name="action" value="confirm" class="btn btn-small"><?= e(t('confirm')) ?></button>
            <button name="action" value="cancel" class="btn btn-small btn-ghost"><?= e(t('cancel')) ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section class="reveal">
  <h2><?= e(t('mail_log')) ?></h2>
  <?php if (!$mails): ?><p><?= e(t('mail_empty')) ?></p><?php endif; ?>
  <table class="table">
    <thead><tr><th><?= e(t('mail_to')) ?></th><th><?= e(t('mail_subject')) ?></th><th><?= e(t('mail_date')) ?></th></tr></thead>
    <tbody>
    <?php foreach ($mails as $m): ?>
      <tr>
        <td data-label="<?= e(t('mail_to')) ?>"><?= e($m['to_email']) ?></td>
        <td data-label="<?= e(t('mail_subject')) ?>"><?= e($m['subject']) ?></td>
        <td data-label="<?= e(t('mail_date')) ?>"><?= e(date('d.m.Y H:i', strtotime($m['created_at']))) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php require __DIR__ . '/../includes/footer.php';
