<?php require __DIR__ . '/includes/header.php';

$cat = (int)($_GET['cat'] ?? 0);
$cats = db()->query('SELECT id, code, name_ru, name_et FROM categories ORDER BY code')->fetchAll();
$sql = 'SELECT c.*, cat.code AS cat_code, cat.name_ru AS cat_ru, cat.name_et AS cat_et, i.full_name AS instructor
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
<section class="hero">
  <div class="hero-bg" style="background-image: url('/assets/img/hero.jpg')"></div>
  <div class="hero-inner container">
    <div class="hero-text">
      <p class="kicker"><?= e(t('hero_kicker')) ?></p>
      <h1><?= e(t('hero_line1')) ?><br><span class="hl"><?= e(t('hero_line2')) ?></span></h1>
      <p class="lead"><?= e(t('hero_text')) ?></p>
      <div class="hero-actions">
        <a class="btn btn-yellow" href="#courses"><?= e(t('hero_cta')) ?></a>
        <a class="btn btn-glass" href="#how"><?= e(t('hero_cta2')) ?></a>
      </div>
      <div class="counters">
        <div><b><span class="count" data-to="15">0</span>+</b><small><?= e(t('instructors_count')) ?></small></div>
        <div><b><span class="count" data-to="2400">0</span>+</b><small><?= e(t('students')) ?></small></div>
        <div><b><span class="count" data-to="96">0</span>%</b><small><?= e(t('pass_rate')) ?></small></div>
      </div>
    </div>
    <div class="traffic" aria-hidden="true">
      <div class="light red"><span><?= e(t('light_red')) ?></span></div>
      <div class="light yellow"><span><?= e(t('light_yellow')) ?></span></div>
      <div class="light green"><span><?= e(t('light_green')) ?></span></div>
    </div>
  </div>
  <div class="road-line" aria-hidden="true"></div>
</section>

<section id="courses" class="section">
  <div class="section-head reveal">
    <h2><?= e(t('courses_title')) ?></h2>
    <p><?= e(t('courses_sub')) ?></p>
  </div>
  <form class="search" method="get" action="/index.php#courses">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="<?= e(t('search')) ?>">
    <?php if ($cat): ?><input type="hidden" name="cat" value="<?= (int)$cat ?>"><?php endif; ?>
    <button class="btn btn-small" type="submit"><?= e(t('search_btn')) ?></button>
  </form>
  <div class="filters">
    <a href="/index.php#courses" class="chip <?= !$cat ? 'on' : '' ?>"><?= e(t('all_categories')) ?></a>
    <?php foreach ($cats as $c): ?>
      <a href="?cat=<?= (int)$c['id'] ?><?= $q !== '' ? '&q=' . urlencode($q) : '' ?>#courses" class="chip plate <?= $cat === (int)$c['id'] ? 'on' : '' ?>"><?= e($c['code']) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="grid">
    <?php if (!$courses): ?><p><?= e(t('empty')) ?></p><?php endif; ?>
    <?php foreach ($courses as $c): ?>
      <article class="card reveal tilt">
        <a class="card-img" href="/course.php?id=<?= (int)$c['id'] ?>">
          <img src="/assets/img/<?= e($c['image'] ?: 'tee.jpg') ?>" alt="<?= e(loc($c, 'title')) ?>" loading="lazy">
          <span class="plate-badge"><?= e($c['cat_code']) ?></span>
        </a>
        <div class="card-body">
          <h3><?= e(loc($c, 'title')) ?></h3>
          <p><?= e(loc($c, 'description')) ?></p>
          <ul class="meta">
            <li>⏱ <?= (int)$c['lessons_count'] ?> <?= e(t('lessons')) ?></li>
            <?php if ($c['start_date']): ?><li>📅 <?= e(date('d.m.Y', strtotime($c['start_date']))) ?></li><?php endif; ?>
          </ul>
          <div class="card-foot">
            <span class="price"><small><?= e(t('from')) ?></small> <?= number_format((float)$c['price_eur'], 0, ',', ' ') ?> €</span>
            <a class="btn btn-small" href="/course.php?id=<?= (int)$c['id'] ?>"><?= e(t('enroll')) ?></a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section id="how" class="how" style="background-image: url('/assets/img/tee.jpg')">
  <div class="how-inner container">
    <h2 class="reveal"><?= e(t('how_title')) ?></h2>
    <ol class="steps">
      <?php for ($i = 1; $i <= 3; $i++): ?>
        <li class="reveal">
          <span class="step-num"><?= $i ?></span>
          <h3><?= e(t('step' . $i . '_t')) ?></h3>
          <p><?= e(t('step' . $i)) ?></p>
        </li>
      <?php endfor; ?>
    </ol>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php';
