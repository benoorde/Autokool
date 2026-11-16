<?php require __DIR__ . '/includes/header.php';
if (!$user) {
    header('Location: /login.php');
    exit;
}
$stmt = db()->prepare('SELECT en.id, en.status, en.created_at, c.title_ru, c.title_et, c.start_date
                       FROM enrollments en JOIN courses c ON c.id = en.course_id
                       WHERE en.user_id = ? ORDER BY en.created_at DESC');
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<section class="reveal">
  <h1><?= e(t('my_enrollments')) ?></h1>
  <?php if ($flash): ?><div class="flash"><?= e($flash) ?></div><?php endif; ?>
  <table class="table">
    <thead><tr><th><?= e(t('course')) ?></th><th><?= e(t('date')) ?></th><th><?= e(t('status')) ?></th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td data-label="<?= e(t('course')) ?>"><?= e(loc($r, 'title')) ?></td>
        <td data-label="<?= e(t('date')) ?>"><?= e(date('d.m.Y', strtotime($r['created_at']))) ?></td>
        <td data-label="<?= e(t('status')) ?>"><span class="status s-<?= e($r['status']) ?>"><?= e(t('status_' . $r['status'])) ?></span></td>
        <td>
          <?php if ($r['status'] !== 'cancelled'): ?>
          <form method="post" action="/cancel.php" class="inline" onsubmit="return confirm('<?= e(t('cancel_my')) ?>?');">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-small btn-ghost" type="submit"><?= e(t('cancel_my')) ?></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php require __DIR__ . '/includes/footer.php';
