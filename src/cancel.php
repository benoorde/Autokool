<?php
require __DIR__ . '/includes/bootstrap.php';
check_csrf();
$user = current_user();
if (!$user) {
    header('Location: /login.php');
    exit;
}
$enrollId = (int)($_POST['id'] ?? 0);

// Tühistada saab ainult oma registreeringu
$stmt = db()->prepare("UPDATE enrollments SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status <> 'cancelled'");
$stmt->execute([$enrollId, $user['id']]);
if ($stmt->rowCount() > 0) {
    $info = db()->prepare('SELECT c.title_ru, c.title_et FROM enrollments en JOIN courses c ON c.id = en.course_id WHERE en.id = ?');
    $info->execute([$enrollId]);
    $row = $info->fetch();
    if ($row) {
        $body = sprintf(t('cancel_body'), loc($row, 'title'));
        db()->prepare('INSERT INTO mail_log (to_email, subject, body) VALUES (?, ?, ?)')
            ->execute([$user['email'], t('cancel_subject'), $body]);
    }
    $_SESSION['flash'] = t('cancelled_ok');
}
header('Location: /my.php');
exit;
