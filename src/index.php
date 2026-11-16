<?php require __DIR__ . '/includes/header.php';

$cat = (int)($_GET['cat'] ?? 0);
$cats = db()->query('SELECT id, code, name_ru, name_et FROM categories ORDER BY code')->fetchAll();
$sql = 'SELECT c.*, cat.name_ru AS cat_ru, cat.name_et AS cat_et, i.full_name AS instructor
        FROM courses c
        JOIN categories cat ON cat.id = c.category_id
        LEFT JOIN instructors i ON i.id = c.instructor_id';
$params = [];
if ($cat) {
    $sql .= ' WHERE c.category_id = ?';
    $params[] = $cat;
}
$stmt = db()->prepare($sql . ' ORDER BY c.price_eur');
$stmt->execute($params);
$courses = $stmt->fetchAll();
?>
<section class="hero reveal">
  <h1 class="glitch" data-text="<?= e(t('hero_title')) ?>"><?= e(t('hero_title')) ?></h1>
  <p><?= e(t('hero_text')) ?></p>
  <div class="counters">
    <div><span class="count" data-to="15">0</span>+<small>instructors</small></div>
    <div><span class="count" data-to="2400">0</span>+<small>students</small></div>
    <div><span class="count" data-to="96">0</span>%<small>pass</small></div>
  </div>
</section>

<section id="courses">
  <div class="filters">
    <a href="/index.php" class="chip <?= !$cat ? 'on' : '' ?>"><?= e(t('all_categories')) ?></a>
    <?php foreach ($cats as $c): ?>
      <a href="?cat=<?= (int)$c['id'] ?>" class="chip <?= $cat === (int)$c['id'] ? 'on' : '' ?>"><?= e($c['code']) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="grid">
    <?php if (!$courses): ?><p><?= e(t('empty')) ?></p><?php endif; ?>
    <?php foreach ($courses as $c): ?>
      <article class="card reveal tilt">
        <span class="badge"><?= e(loc($c, 'cat')) ?></span>
        <h3><?= e(loc($c, 'title')) ?></h3>
        <p><?= e(loc($c, 'description')) ?></p>
        <ul class="meta">
          <li><?= e(t('lessons')) ?>: <?= (int)$c['lessons_count'] ?></li>
          <li><?= e(t('price')) ?>: <strong><?= number_format((float)$c['price_eur'], 2, ',', ' ') ?> €</strong></li>
          <?php if ($c['start_date']): ?><li><?= e(t('start')) ?>: <?= e(date('d.m.Y', strtotime($c['start_date']))) ?></li><?php endif; ?>
        </ul>
        <a class="btn" href="/course.php?id=<?= (int)$c['id'] ?>"><?= e(t('enroll')) ?></a>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php';
