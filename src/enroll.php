<?php
require __DIR__ . '/includes/bootstrap.php';
check_csrf();
$user = current_user();
$courseId = (int)($_POST['course_id'] ?? 0);
if (!$user) {
    header('Location: /login.php');
    exit;
}
db()->prepare('INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)')->execute([$user['id'], $courseId]);
$_SESSION['flash'] = t('enrolled');
header('Location: /course.php?id=' . $courseId);
exit;
