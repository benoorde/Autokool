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
        'already_enrolled' => 'Вы уже записаны на этот курс.',
        'not_found' => 'Страница не найдена.',
        'invalid_form' => 'Проверьте поля: имя, email и пароль от 6 символов.',
        'students' => 'студентов',
        'instructors_count' => 'инструкторов',
        'pass_rate' => 'сдача с первой попытки',
        'hero_title_sub' => 'Курсы для всех категорий',
        'search' => 'Поиск курсов',
        'hero_kicker' => 'Автошкола · категории A, B, C, D',
        'hero_line1' => 'Права —',
        'hero_line2' => 'без стресса',
        'hero_cta' => 'Выбрать курс',
        'hero_cta2' => 'Как проходит обучение',
        'light_red' => 'Стоп-сомнения',
        'light_yellow' => 'Готовимся',
        'light_green' => 'Поехали!',
        'courses_title' => 'Курсы',
        'courses_sub' => 'Выберите категорию — остальное мы берём на себя.',
        'how_title' => 'Три шага до прав',
        'step1_t' => 'Теория',
        'step1' => 'Онлайн-занятия и тесты, как на настоящем экзамене.',
        'step2_t' => 'Площадка и город',
        'step2' => 'Практика с инструктором: от парковки до трассы.',
        'step3_t' => 'Экзамен',
        'step3' => 'Пробный экзамен и сопровождение до получения прав.',
        'from' => 'от',
        'search_btn' => 'Найти',
        'cancel_my' => 'Отменить запись',
        'cancelled_ok' => 'Запись отменена.',
        'forgot' => 'Забыли пароль?',
        'forgot_title' => 'Восстановление пароля',
        'forgot_text' => 'Введите email, и мы создадим ссылку для смены пароля.',
        'forgot_btn' => 'Получить ссылку',
        'forgot_sent' => 'Если аккаунт существует, ссылка создана и записана в журнал писем.',
        'reset_title' => 'Новый пароль',
        'reset_btn' => 'Сохранить пароль',
        'reset_ok' => 'Пароль изменён. Теперь можно войти.',
        'reset_bad' => 'Ссылка недействительна или устарела.',
        'reset_link' => 'Ссылка для смены пароля (демо, письмо в журнале):',
        'mail_log' => 'Журнал писем',
        'mail_to' => 'Кому',
        'mail_subject' => 'Тема',
        'mail_date' => 'Когда',
        'mail_empty' => 'Писем пока нет.',
        'confirm_subject' => 'Ваша запись подтверждена',
        'confirm_body' => 'Здравствуйте! Ваша запись на курс «%s» подтверждена.',
        'cancel_subject' => 'Ваша запись отменена',
        'cancel_body' => 'Здравствуйте! Ваша запись на курс «%s» отменена.',
        'reset_subject' => 'Смена пароля Autokool',
        'reset_body' => 'Для смены пароля откройте ссылку: %s (действует 1 час).',
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
        'already_enrolled' => 'Oled juba sellele kursusele registreeritud.',
        'not_found' => 'Lehte ei leitud.',
        'invalid_form' => 'Kontrolli välju: nimi, e-post ja vähemalt 6-märgiline parool.',
        'students' => 'õpilast',
        'instructors_count' => 'instruktorit',
        'pass_rate' => 'esimesel katsel läbitud',
        'hero_title_sub' => 'Kursused kõigile kategooriatele',
        'search' => 'Otsi kursuseid',
        'hero_kicker' => 'Autokool · kategooriad A, B, C, D',
        'hero_line1' => 'Juhiload —',
        'hero_line2' => 'ilma stressita',
        'hero_cta' => 'Vali kursus',
        'hero_cta2' => 'Kuidas õpe käib',
        'light_red' => 'Stopp kahtlustele',
        'light_yellow' => 'Valmistume',
        'light_green' => 'Sõidame!',
        'courses_title' => 'Kursused',
        'courses_sub' => 'Vali kategooria — ülejäänu võtame enda peale.',
        'how_title' => 'Kolm sammu juhiloani',
        'step1_t' => 'Teooria',
        'step1' => 'Veebitunnid ja testid nagu päris eksamil.',
        'step2_t' => 'Plats ja linn',
        'step2' => 'Praktika instruktoriga: parkimisest maanteeni.',
        'step3_t' => 'Eksam',
        'step3' => 'Proovieksam ja tugi kuni juhiloa saamiseni.',
        'from' => 'alates',
        'search_btn' => 'Otsi',
        'cancel_my' => 'Tühista registreering',
        'cancelled_ok' => 'Registreering tühistati.',
        'forgot' => 'Unustasid parooli?',
        'forgot_title' => 'Parooli taastamine',
        'forgot_text' => 'Sisesta e-post, loome sulle parooli vahetamise lingi.',
        'forgot_btn' => 'Saa link',
        'forgot_sent' => 'Kui konto on olemas, on link loodud ja salvestatud kirjade logisse.',
        'reset_title' => 'Uus parool',
        'reset_btn' => 'Salvesta parool',
        'reset_ok' => 'Parool on muudetud. Nüüd saad sisse logida.',
        'reset_bad' => 'Link on kehtetu või aegunud.',
        'reset_link' => 'Parooli vahetamise link (demo, kiri on logis):',
        'mail_log' => 'Kirjade logi',
        'mail_to' => 'Kellele',
        'mail_subject' => 'Teema',
        'mail_date' => 'Millal',
        'mail_empty' => 'Kirju pole veel.',
        'confirm_subject' => 'Sinu registreering on kinnitatud',
        'confirm_body' => 'Tere! Sinu registreering kursusele «%s» on kinnitatud.',
        'cancel_subject' => 'Sinu registreering on tühistatud',
        'cancel_body' => 'Tere! Sinu registreering kursusele «%s» on tühistatud.',
        'reset_subject' => 'Autokooli parooli vahetamine',
        'reset_body' => 'Parooli vahetamiseks ava link: %s (kehtib 1 tund).',
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
