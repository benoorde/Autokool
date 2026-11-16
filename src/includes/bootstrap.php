<?php
// Üldine algatus: sessioon, keel, tõlked, abifunktsioonid
session_start();
require_once __DIR__ . '/../config/db.php';

const LANGS = ['ru' => 'Русский', 'et' => 'Eesti'];

// Keele vahetus ?lang=ru|et
if (isset($_GET['lang']) && isset(LANGS[$_GET['lang']])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'ru';

$T = [
    'ru' => [
        'site' => 'Автошкола',
        'home' => 'Главная',
        'courses' => 'Курсы',
        'login' => 'Вход',
        'register' => 'Регистрация',
        'logout' => 'Выход',
        'admin' => 'Админка',
        'hero_title' => 'Водительские права без стресса',
        'hero_text' => 'Опытные инструкторы, гибкое расписание и успешная сдача экзамена.',
        'all_categories' => 'Все категории',
        'price' => 'Цена',
        'lessons' => 'уроков',
        'start' => 'Старт',
        'enroll' => 'Записаться',
        'enrolled' => 'Вы записаны! Мы свяжемся с вами.',
        'login_required' => 'Чтобы записаться, войдите в аккаунт.',
        'instructor' => 'Инструктор',
        'reviews' => 'Отзывы',
        'no_reviews' => 'Отзывов пока нет.',
        'back' => '← Ко всем курсам',
        'name' => 'Имя',
        'email' => 'Email',
        'phone' => 'Телефон',
        'password' => 'Пароль',
        'submit' => 'Отправить',
        'register_ok' => 'Регистрация прошла успешно.',
        'bad_login' => 'Неверный email или пароль.',
        'email_taken' => 'Такой email уже зарегистрирован.',
        'my_enrollments' => 'Мои записи',
        'status' => 'Статус',
        'user' => 'Пользователь',
        'course' => 'Курс',
        'date' => 'Дата',
        'confirm' => 'Подтвердить',
        'cancel' => 'Отменить',
        'footer' => '© Автошкола. Все права защищены.',
        'empty' => 'Курсы не найдены.',
        'status_new' => 'Новая', 'status_confirmed' => 'Подтверждена', 'status_cancelled' => 'Отменена',
    ],
    'et' => [
        'site' => 'Autokool',
        'home' => 'Avaleht',
        'courses' => 'Kursused',
        'login' => 'Sisselogimine',
        'register' => 'Registreeru',
        'logout' => 'Logi välja',
        'admin' => 'Admin',
        'hero_title' => 'Juhiload ilma stressita',
        'hero_text' => 'Kogenud instruktorid, paindlik ajakava ja edukas eksam.',
        'all_categories' => 'Kõik kategooriad',
        'price' => 'Hind',
        'lessons' => 'tundi',
        'start' => 'Algus',
        'enroll' => 'Registreeru kursusele',
        'enrolled' => 'Oled registreeritud! Võtame sinuga ühendust.',
        'login_required' => 'Registreerumiseks logi sisse.',
        'instructor' => 'Instruktor',
        'reviews' => 'Arvustused',
        'no_reviews' => 'Arvustusi pole veel.',
        'back' => '← Kõik kursused',
        'name' => 'Nimi',
        'email' => 'E-post',
        'phone' => 'Telefon',
        'password' => 'Parool',
        'submit' => 'Saada',
        'register_ok' => 'Registreerimine õnnestus.',
        'bad_login' => 'Vale e-post või parool.',
        'email_taken' => 'See e-post on juba registreeritud.',
        'my_enrollments' => 'Minu registreeringud',
        'status' => 'Staatus',
        'user' => 'Kasutaja',
        'course' => 'Kursus',
        'date' => 'Kuupäev',
        'confirm' => 'Kinnita',
        'cancel' => 'Tühista',
        'footer' => '© Autokool. Kõik õigused kaitstud.',
        'empty' => 'Kursuseid ei leitud.',
        'status_new' => 'Uus', 'status_confirmed' => 'Kinnitatud', 'status_cancelled' => 'Tühistatud',
    ],
];

function t(string $key): string
{
    global $T, $lang;
    return $T[$lang][$key] ?? $key;
}

// Tekst keeles: valib title_ru / title_et jne
function loc(array $row, string $field): string
{
    global $lang;
    return (string)($row[$field . '_' . $lang] ?? $row[$field . '_ru'] ?? '');
}

function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function check_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('CSRF');
    }
}
