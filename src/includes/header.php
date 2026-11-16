<?php
require_once __DIR__ . '/bootstrap.php';
$user = current_user();
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!doctype html>
<html lang="<?= e($lang) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(t('site')) ?></title>
  <link rel="stylesheet" href="/assets/style.css?v=<?= filemtime(__DIR__ . '/../assets/style.css') ?>">
</head>
<body class="page-<?= e($page) ?>">
<header class="site-header">
  <div class="container nav">
    <a href="/index.php" class="logo"><span class="logo-mark">A</span><?= e(t('site')) ?></a>
    <button class="menu-toggle" aria-label="menu" onclick="document.body.classList.toggle('menu-open')">☰</button>
    <nav class="menu">
      <a href="/index.php" class="<?= $page === 'index' ? 'active' : '' ?>"><?= e(t('home')) ?></a>
      <a href="/index.php#courses"><?= e(t('courses')) ?></a>
      <?php if ($user): ?>
        <a href="/my.php"><?= e(t('my_enrollments')) ?></a>
        <?php if ($user['role'] === 'admin'): ?><a href="/admin/index.php"><?= e(t('admin')) ?></a><?php endif; ?>
        <a href="/logout.php"><?= e(t('logout')) ?></a>
      <?php else: ?>
        <a href="/login.php"><?= e(t('login')) ?></a>
        <a href="/register.php" class="btn btn-small"><?= e(t('register')) ?></a>
      <?php endif; ?>
      <span class="lang">
        <?php foreach (LANGS as $code => $label): ?>
          <a href="?lang=<?= $code ?>" class="<?= $lang === $code ? 'current' : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </span>
    </nav>
  </div>
</header>
<main class="container">
