<?php require __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT c.*, cat.name_ru AS cat_ru, cat.name_et AS cat_et,
                              i.full_name AS instructor, i.bio_ru AS bio_ru, i.bio_et AS bio_et
                       FROM courses c
                       JOIN categories cat ON cat.id = c.category_id
                       LEFT JOIN instructors i ON i.id = c.instructor_id
                       WHERE c.id = ?');
$stmt->execute([$id]);
$course = $stmt->fetch();
if (!$course) {
    http_response_code(404);
    echo '<p>' . e(t('not_found')) . '</p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
$rs = db()->prepare('SELECT * FROM reviews WHERE course_id = ? ORDER BY created_at DESC');
$rs->execute([$id]);
$reviews = $rs->fetchAll();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<section class="detail reveal">
  <a href="/index.php#courses"><?= e(t('back')) ?></a>
  <span class="badge"><?= e(loc($course, 'cat')) ?></span>
  <h1><?= e(loc($course, 'title')) ?></h1>
  <p><?= e(loc($course, 'description')) ?></p>
  <ul class="meta">
    <li><?= e(t('lessons')) ?>: <?= (int)$course['lessons_count'] ?></li>
    <li><?= e(t('price')) ?>: <strong><?= number_format((float)$course['price_eur'], 2, ',', ' ') ?> €</strong></li>
  </ul>
  <?php if ($course['instructor']): ?>
    <div class="instructor">
      <h3><?= e(t('instructor')) ?>: <?= e($course['instructor']) ?></h3>
      <p><?= e(loc($course, 'bio')) ?></p>
    </div>
  <?php endif; ?>

  <?php if ($flash): ?><div class="flash"><?= e($flash) ?></div><?php endif; ?>
  <?php if ($user): ?>
    <form method="post" action="/enroll.php">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="course_id" value="<?= (int)$course['id'] ?>">
      <button class="btn pulse" type="submit"><?= e(t('enroll')) ?></button>
    </form>
  <?php else: ?>
    <p><a href="/login.php"><?= e(t('login_required')) ?></a></p>
  <?php endif; ?>
</section>

<section class="reveal">
  <h2><?= e(t('reviews')) ?></h2>
  <?php if (!$reviews): ?><p><?= e(t('no_reviews')) ?></p><?php endif; ?>
  <?php foreach ($reviews as $r): ?>
    <blockquote class="review">
      <strong><?= e($r['author_name']) ?></strong> — <?= str_repeat('★', (int)$r['rating']) ?>
      <p><?= e(loc($r, 'text')) ?></p>
    </blockquote>
  <?php endforeach; ?>
</section>
<?php require __DIR__ . '/includes/footer.php';
