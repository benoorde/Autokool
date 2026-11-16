<?php require __DIR__ . '/includes/header.php';
if (!$user) {
    header('Location: /login.php');
    exit;
}
$stmt = db()->prepare('SELECT en.status, en.created_at, c.title_ru, c.title_et, c.start_date
                       FROM enrollments en JOIN courses c ON c.id = en.course_id
                       WHERE en.user_id = ? ORDER BY en.created_at DESC');
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();
?>
<section class="reveal">
  <h1><?= e(t('my_enrollments')) ?></h1>
  <table class="table">
    <thead><tr><th><?= e(t('course')) ?></th><th><?= e(t('date')) ?></th><th><?= e(t('status')) ?></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e(loc($r, 'title')) ?></td>
        <td><?= e(date('d.m.Y', strtotime($r['created_at']))) ?></td>
        <td><span class="status s-<?= e($r['status']) ?>"><?= e(t('status_' . $r['status'])) ?></span></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php require __DIR__ . '/includes/footer.php';
