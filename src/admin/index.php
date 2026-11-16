<?php
require __DIR__ . '/../includes/bootstrap.php';
$user = current_user();
if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    exit('403');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $status = $_POST['action'] === 'confirm' ? 'confirmed' : 'cancelled';
    db()->prepare('UPDATE enrollments SET status = ? WHERE id = ?')->execute([$status, (int)$_POST['id']]);
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
<?php require __DIR__ . '/../includes/footer.php';
