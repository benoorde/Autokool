<?php
require __DIR__ . '/includes/bootstrap.php';
check_csrf();
$user = current_user();
$courseId = (int)($_POST['course_id'] ?? 0);
if (!$user) {
    header('Location: /login.php');
    exit;
}

// Kontrollime, et kursus eksisteerib
$course = db()->prepare('SELECT id FROM courses WHERE id = ?');
$course->execute([$courseId]);
if (!$course->fetch()) {
    http_response_code(404);
    exit('404');
}

// Ei luba sama kursuse topeltregistreerimist (kehtivad registreeringud)
$dup = db()->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? AND status <> 'cancelled'");
$dup->execute([$user['id'], $courseId]);
if ($dup->fetch()) {
    $_SESSION['flash'] = t('already_enrolled');
    header('Location: /course.php?id=' . $courseId);
    exit;
}

db()->prepare('INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)')->execute([$user['id'], $courseId]);
$_SESSION['flash'] = t('enrolled');
header('Location: /course.php?id=' . $courseId);
exit;
