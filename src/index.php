<?php require __DIR__ . '/includes/header.php';

$cat = (int)($_GET['cat'] ?? 0);
$cats = db()->query('SELECT id, code, name_ru, name_et FROM categories ORDER BY code')->fetchAll();
$sql = 'SELECT c.*, cat.name_ru AS cat_ru, cat.name_et AS cat_et, i.full_name AS instructor
        FROM courses c
        JOIN categories cat ON cat.id = c.category_id
        LEFT JOIN instructors i ON i.id = c.instructor_id';
$params = [];
$q = trim($_GET['q'] ?? '');
$where = [];
if ($cat) {
    $where[] = 'c.category_id = ?';
    $params[] = $cat;
}
if ($q !== '') {
    $where[] = '(c.title_ru LIKE ? OR c.title_et LIKE ? OR c.description_ru LIKE ? OR c.description_et LIKE ?)';
    for ($i = 0; $i < 4; $i++) {
        $params[] = '%' . $q . '%';
    }
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$stmt = db()->prepare($sql . ' ORDER BY c.price_eur');
$stmt->execute($params);
$courses = $stmt->fetchAll();
?>
<section class="hero reveal">
  <h1 class="glitch" data-text="<?= e(t('hero_title')) ?>"><?= e(t('hero_title')) ?></h1>
  <p><?= e(t('hero_text')) ?></p>
  <div class="counters">
    <div><span class="count" data-to="15">0</span>+<small><?= e(t('instructors_count')) ?></small></div>
    <div><span class="count" data-to="2400">0</span>+<small><?= e(t('students')) ?></small></div>
    <div><span class="count" data-to="96">0</span>%<small><?= e(t('pass_rate')) ?></small></div>
  </div>
</section>

<section id="courses">
  <form class="search" method="get" action="/index.php#courses">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="<?= e(t('search')) ?>">
    <?php if ($cat): ?><input type="hidden" name="cat" value="<?= (int)$cat ?>"><?php endif; ?>
    <button class="btn btn-small" type="submit"><?= e(t('search_btn')) ?></button>
  </form>
  <div class="filters">
    <a href="/index.php" class="chip <?= !$cat ? 'on' : '' ?>"><?= e(t('all_categories')) ?></a>
    <?php foreach ($cats as $c): ?>
      <a href="?cat=<?= (int)$c['id'] ?><?= $q !== '' ? '&q=' . urlencode($q) : '' ?>" class="chip <?= $cat === (int)$c['id'] ? 'on' : '' ?>"><?= e($c['code']) ?></a>
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
