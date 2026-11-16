<?php
// Loob või uuendab administraatori. Andmed tulevad keskkonnamuutujatest (.env).
// Käivitamine: docker compose exec web php cli/create-admin.php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__ . '/../config/db.php';

$email = getenv('ADMIN_EMAIL') ?: '';
$pass = getenv('ADMIN_PASSWORD') ?: '';
if ($email === '' || strlen($pass) < 8) {
    fwrite(STDERR, "ADMIN_EMAIL ja ADMIN_PASSWORD (vähemalt 8 märki) peavad olema .env failis\n");
    exit(1);
}
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($id = $stmt->fetchColumn()) {
    db()->prepare("UPDATE users SET password_hash = ?, role = 'admin' WHERE id = ?")->execute([$hash, $id]);
    echo "Admin uuendatud: $email\n";
} else {
    db()->prepare("INSERT INTO users (name, email, password_hash, role) VALUES ('Administraator', ?, ?, 'admin')")->execute([$email, $hash]);
    echo "Admin loodud: $email\n";
}
