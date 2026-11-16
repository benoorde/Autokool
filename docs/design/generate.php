<?php
// Autokooli disaini generaator: loob iga ekraani kahes versioonis
//   wireframe/  hall makett (wireframe)
//   ui/         värviline disain
// ja klikitava prototüübi prototype.html.
// Käivitamine: php docs/design/generate.php

const THEMES = [
    'ui' => [
        'bg' => '#f5f7fb', 'surface' => '#ffffff', 'text' => '#1d2433', 'muted' => '#5b6478',
        'accent' => '#ff6b1a', 'accent2' => '#1f6feb', 'border' => '#e3e7ef',
        'ok' => '#1a9c5b', 'bad' => '#d64545', 'hero' => 'url(#hero)', 'onAccent' => '#ffffff',
        'chipOff' => '#ffffff', 'line' => null,
    ],
    'wireframe' => [
        'bg' => '#f2f2f2', 'surface' => '#ffffff', 'text' => '#4a4a4a', 'muted' => '#9a9a9a',
        'accent' => '#7a7a7a', 'accent2' => '#a8a8a8', 'border' => '#c9c9c9',
        'ok' => '#8f8f8f', 'bad' => '#8f8f8f', 'hero' => '#d6d6d6', 'onAccent' => '#ffffff',
        'chipOff' => '#ffffff', 'line' => '#c4c4c4',
    ],
];

$hotspots = [];

function esc(string $s): string { return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8'); }

function rect($x, $y, $w, $h, $fill, $rx = 0, $stroke = null, $sw = 1): string
{
    $s = $stroke ? " stroke=\"$stroke\" stroke-width=\"$sw\"" : '';
    return "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" rx=\"$rx\" fill=\"$fill\"$s/>\n";
}

function txt($x, $y, $str, $size, $fill, $weight = 400, $anchor = 'start'): string
{
    return "<text x=\"$x\" y=\"$y\" font-family=\"Inter, Segoe UI, Arial, sans-serif\" font-size=\"$size\" font-weight=\"$weight\" fill=\"$fill\" text-anchor=\"$anchor\">" . esc($str) . "</text>\n";
}

// Tekst või halli makettis tekstiriba
function para($t, $x, $y, $str, $size, $width = 0): string
{
    if ($t['line'] && $width > 0) {
        return rect($x, $y - $size + 3, $width, $size - 4, $t['line'], 3);
    }
    return txt($x, $y, $str, $size, $t['muted']);
}

function btn($t, $x, $y, $w, $h, $label, $variant = 'primary', $size = 15): string
{
    if ($variant === 'ghost') {
        $out = rect($x, $y, $w, $h, $t['surface'], 10, $t['muted']);
        return $out . txt($x + $w / 2, $y + $h / 2 + $size / 3, $label, $size, $t['text'], 600, 'middle');
    }
    $fill = $variant === 'blue' ? $t['accent2'] : $t['accent'];
    return rect($x, $y, $w, $h, $fill, 10) . txt($x + $w / 2, $y + $h / 2 + $size / 3, $label, $size, $t['onAccent'], 600, 'middle');
}

function image_box($t, $x, $y, $w, $h): string
{
    // Pildi koht: makettis rist, disainis pehme värviline plokk
    if ($t['line']) {
        return rect($x, $y, $w, $h, '#e6e6e6', 8, $t['border'])
            . "<line x1=\"$x\" y1=\"$y\" x2=\"" . ($x + $w) . "\" y2=\"" . ($y + $h) . "\" stroke=\"{$t['border']}\"/>\n"
            . "<line x1=\"" . ($x + $w) . "\" y1=\"$y\" x2=\"$x\" y2=\"" . ($y + $h) . "\" stroke=\"{$t['border']}\"/>\n";
    }
    return rect($x, $y, $w, $h, '#e8efff', 8);
}

function svg_open($w, $h, $t): string
{
    $defs = '<defs><linearGradient id="hero" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="#1f6feb"/><stop offset="1" stop-color="#6d28d9"/></linearGradient>'
        . '<filter id="sh" x="-10%" y="-10%" width="120%" height="140%"><feDropShadow dx="0" dy="6" stdDeviation="10" flood-color="#14203c" flood-opacity="0.08"/></filter></defs>';
    return "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"$w\" height=\"$h\" viewBox=\"0 0 $w $h\">\n$defs\n"
        . rect(0, 0, $w, $h, $t['bg']);
}

function card_rect($t, $x, $y, $w, $h, $rx = 14): string
{
    $f = $t['line'] ? '' : ' filter="url(#sh)"';
    $s = $t['line'] ? " stroke=\"{$t['border']}\"" : '';
    return "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" rx=\"$rx\" fill=\"{$t['surface']}\"$s$f/>\n";
}

function page_header($t, $w, $active = 'home', $logged = false, $admin = false): string
{
    $o = rect(0, 0, $w, 64, $t['surface']) . rect(0, 63, $w, 1, $t['border']);
    $o .= rect(120, 18, 28, 28, $t['accent'], 8) . txt(160, 39, 'Автошкола', 20, $t['text'], 800);
    $items = $logged
        ? ['home' => 'Главная', 'courses' => 'Курсы', 'my' => 'Мои записи'] + ($admin ? ['admin' => 'Админка'] : []) + ['logout' => 'Выход']
        : ['home' => 'Главная', 'courses' => 'Курсы', 'login' => 'Вход'];
    $x = $logged ? ($admin ? 690 : 790) : 820;
    foreach ($items as $k => $label) {
        $o .= txt($x, 38, $label, 15, $k === $active ? $t['accent'] : $t['text'], 500);
        $x += mb_strlen($label) * 9 + 28;
    }
    if (!$logged) {
        $o .= btn($t, $x, 16, 130, 32, 'Регистрация', 'primary', 14);
        $x += 150;
    }
    $o .= rect($x, 22, 30, 22, $t['accent'], 6) . txt($x + 15, 38, 'RU', 12, $t['onAccent'], 600, 'middle');
    $o .= txt($x + 44, 38, 'ET', 12, $t['muted'], 600);
    return $o;
}

function page_footer($t, $w, $y): string
{
    return rect(0, $y, $w, 1, $t['border']) . txt($w / 2, $y + 40, '© Автошкола. Все права защищены.', 14, $t['muted'], 400, 'middle');
}

function hot($screen, $x, $y, $w, $h, $to)
{
    global $hotspots;
    $hotspots[$screen][] = compact('x', 'y', 'w', 'h', 'to');
}

// ---------- Ekraanid ----------

function screen_home($t): string
{
    $W = 1440; $H = 1080;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'home');
    $o .= rect(120, 96, 1200, 300, $t['hero'], 16);
    $title = $t['line'] ? '#5a5a5a' : '#ffffff';
    $o .= txt(720, 190, 'Водительские права без стресса', 44, $title, 800, 'middle');
    $o .= txt(720, 228, 'Опытные инструкторы, гибкое расписание и успешная сдача экзамена.', 18, $title, 400, 'middle');
    $stats = [['15+', 'инструкторов'], ['2 400+', 'студентов'], ['96%', 'сдача с первой попытки']];
    $x = 470;
    foreach ($stats as [$n, $l]) {
        $o .= txt($x, 310, $n, 34, $title, 800, 'middle') . txt($x, 336, $l, 14, $title, 400, 'middle');
        $x += 250;
    }
    // otsing
    $o .= rect(120, 428, 1070, 44, $t['surface'], 10, $t['muted']) . txt(138, 456, 'Поиск курсов', 15, $t['muted']);
    $o .= btn($t, 1210, 428, 110, 44, 'Найти');
    // kategooriad
    $chips = ['Все категории', 'B', 'A', 'C', 'D'];
    $x = 120;
    foreach ($chips as $i => $c) {
        $w = mb_strlen($c) * 9 + 36;
        $on = $i === 0;
        $o .= rect($x, 492, $w, 32, $on ? $t['accent2'] : $t['chipOff'], 16, $on ? null : $t['muted']);
        $o .= txt($x + $w / 2, 513, $c, 14, $on ? '#ffffff' : $t['text'], 500, 'middle');
        $x += $w + 10;
    }
    // kursuste kaardid
    $cards = [
        ['Категория B', 'Базовый курс категории B', 'Полный курс теории и практики', 'вождения легкового автомобиля.', '30', '490,00 €'],
        ['Категория B', 'Экспресс-курс категории B', 'Ускоренная подготовка для тех,', 'кто уже знаком с управлением.', '15', '490,00 €'],
        ['Категория A', 'Курс категории A', 'Обучение управлению', 'мотоциклом с нуля.', '20', '690,00 €'],
        ['Категория C', 'Курс категории C', 'Подготовка водителей', 'грузовых автомобилей.', '40', '1 490,00 €'],
    ];
    $cards[0][5] = '890,00 €';
    $x = 120;
    foreach ($cards as $i => [$badge, $title, $d1, $d2, $les, $price]) {
        $o .= card_rect($t, $x, 548, 285, 330);
        $o .= image_box($t, $x + 20, 568, 245, 90);
        $o .= rect($x + 20, 674, mb_strlen($badge) * 8 + 20, 22, $t['accent2'], 11) . txt($x + 30, 690, $badge, 12, '#ffffff', 600);
        $o .= txt($x + 20, 724, $title, 17, $t['text'], 700);
        $o .= para($t, $x + 20, 752, $d1, 14, 230) . para($t, $x + 20, 772, $d2, 14, 180);
        $o .= txt($x + 20, 802, 'Уроков: ' . $les, 14, $t['muted']);
        $o .= txt($x + 20, 822, 'Цена: ', 14, $t['muted']) . txt($x + 62, 822, $price, 14, $t['text'], 700);
        $o .= btn($t, $x + 20, 836, 130, 34, 'Записаться', 'primary', 14);
        if ($i === 0) {
            hot('home', $x + 20, 836, 130, 34, 'course');
        }
        $x += 305;
    }
    hot('home', 820, 16, 50, 32, 'login');
    $o .= page_footer($t, $W, 940);
    return $o . "</svg>\n";
}

function screen_course($t): string
{
    $W = 1440; $H = 1024;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'courses');
    $o .= card_rect($t, 120, 96, 1200, 480);
    $o .= txt(150, 136, '← Ко всем курсам', 15, $t['accent2'], 500);
    hot('course', 150, 118, 160, 26, 'home');
    $o .= rect(150, 156, 110, 24, $t['accent2'], 12) . txt(205, 173, 'Категория B', 12, '#ffffff', 600, 'middle');
    $o .= txt(150, 222, 'Базовый курс категории B', 34, $t['text'], 800);
    $o .= para($t, 150, 258, 'Полный курс теории и практики вождения легкового автомобиля.', 16, 520);
    $o .= txt(150, 292, 'Уроков: 30', 15, $t['muted']) . txt(150, 316, 'Цена: ', 15, $t['muted']) . txt(198, 316, '890,00 €', 15, $t['text'], 700);
    $o .= image_box($t, 880, 136, 410, 220);
    $o .= rect(150, 346, 4, 76, $t['accent']) . txt(168, 370, 'Инструктор: Mart Tamm', 18, $t['text'], 700);
    $o .= para($t, 168, 396, 'Инструктор высшей категории, специализируется на начинающих водителях.', 15, 560);
    if (!$t['line']) {
        $o .= rect(144, 450, 172, 52, 'none', 14, '#ff6b1a', 2) ;
    }
    $o .= btn($t, 150, 456, 160, 40, 'Записаться');
    hot('course', 150, 456, 160, 40, 'my');
    $o .= txt(120, 640, 'Отзывы', 26, $t['text'], 800);
    $reviews = [['Olga', 5, 'Отличный курс, сдала с первого раза!'], ['Jüri', 4, 'Хорошие инструкторы и удобное расписание.']];
    $y = 664;
    foreach ($reviews as [$n, $r, $text]) {
        $o .= card_rect($t, 120, $y, 1200, 84, 12);
        $o .= txt(144, $y + 32, $n, 16, $t['text'], 700) . txt(200, $y + 32, str_repeat('★', $r), 16, $t['line'] ? $t['muted'] : '#f5a623');
        $o .= para($t, 144, $y + 60, $text, 15, 380);
        $y += 100;
    }
    $o .= page_footer($t, $W, 940);
    return $o . "</svg>\n";
}

function screen_login($t): string
{
    $W = 1440; $H = 900;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'login');
    $o .= card_rect($t, 510, 140, 420, 420);
    $o .= txt(540, 196, 'Вход', 30, $t['text'], 800);
    $o .= txt(540, 240, 'Email', 15, $t['text'], 500) . rect(540, 250, 360, 42, $t['bg'], 8, $t['muted']);
    $o .= txt(556, 277, 'admin@autokool.test', 15, $t['muted']);
    $o .= txt(540, 322, 'Пароль', 15, $t['text'], 500) . rect(540, 332, 360, 42, $t['bg'], 8, $t['muted']);
    $o .= txt(556, 359, '••••••••', 15, $t['muted']);
    $o .= btn($t, 540, 400, 140, 42, 'Отправить');
    hot('login', 540, 400, 140, 42, 'my');
    $o .= txt(540, 480, 'Забыли пароль?', 15, $t['accent2'], 500);
    $o .= txt(540, 512, 'Нет аккаунта? Регистрация', 15, $t['muted']);
    $o .= page_footer($t, $W, 820);
    return $o . "</svg>\n";
}

function status_pill($t, $x, $y, $kind): string
{
    $map = ['new' => ['Новая', $t['accent']], 'confirmed' => ['Подтверждена', $t['ok']], 'cancelled' => ['Отменена', $t['bad']]];
    [$label, $color] = $map[$kind];
    $w = mb_strlen($label) * 8 + 24;
    return rect($x, $y - 16, $w, 24, $color, 12) . txt($x + $w / 2, $y, $label, 13, '#ffffff', 600, 'middle');
}

function table_head($t, $x, $y, $w, array $cols): string
{
    $o = rect($x, $y, $w, 44, $t['accent2'], 12);
    foreach ($cols as [$cx, $label]) {
        $o .= txt($cx, $y + 28, $label, 15, '#ffffff', 700);
    }
    return $o;
}

function screen_my($t): string
{
    $W = 1440; $H = 900;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'my', true);
    $o .= txt(120, 140, 'Мои записи', 32, $t['text'], 800);
    $o .= rect(120, 160, 1200, 46, $t['line'] ? '#e6e6e6' : '#e6f8ef', 10) . txt(140, 189, 'Вы записаны! Мы свяжемся с вами.', 15, $t['line'] ? $t['text'] : $t['ok'], 600);
    $o .= card_rect($t, 120, 226, 1200, 44 + 3 * 60, 12);
    $o .= table_head($t, 120, 226, 1200, [[144, 'Курс'], [620, 'Дата'], [820, 'Статус']]);
    $rows = [['Базовый курс категории B', '16.11.2026', 'new'], ['Курс категории A', '10.11.2026', 'confirmed'], ['Экспресс-курс категории B', '02.11.2026', 'cancelled']];
    $y = 270;
    foreach ($rows as [$c, $d, $s]) {
        $o .= rect(120, $y + 59, 1200, 1, $t['border']);
        $o .= txt(144, $y + 36, $c, 15, $t['text']) . txt(620, $y + 36, $d, 15, $t['text']);
        $o .= status_pill($t, 820, $y + 36, $s);
        if ($s !== 'cancelled') {
            $o .= btn($t, 1130, $y + 14, 170, 32, 'Отменить запись', 'ghost', 14);
        }
        $y += 60;
    }
    hot('my', 690, 22, 60, 24, 'admin');
    $o .= page_footer($t, $W, 820);
    return $o . "</svg>\n";
}

function screen_admin($t): string
{
    $W = 1440; $H = 1024;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'admin', true, true);
    $o .= txt(120, 140, 'Админка', 32, $t['text'], 800);
    $o .= card_rect($t, 120, 160, 1200, 44 + 3 * 68, 12);
    $o .= table_head($t, 120, 160, 1200, [[144, 'Пользователь'], [420, 'Курс'], [700, 'Дата'], [880, 'Статус']]);
    $rows = [['Olga Ivanova', 'olga@example.com', 'Базовый курс категории B', '16.11.2026 10:42', 'new'],
             ['Jüri Kask', 'juri@example.com', 'Курс категории A', '15.11.2026 18:05', 'confirmed'],
             ['Anna Lepp', 'anna@example.com', 'Курс категории C', '14.11.2026 09:13', 'cancelled']];
    $y = 204;
    foreach ($rows as [$n, $e, $c, $d, $s]) {
        $o .= rect(120, $y + 67, 1200, 1, $t['border']);
        $o .= txt(144, $y + 30, $n, 15, $t['text'], 600) . txt(144, $y + 50, $e, 13, $t['muted']);
        $o .= txt(420, $y + 38, $c, 15, $t['text']) . txt(700, $y + 38, $d, 15, $t['text']);
        $o .= status_pill($t, 880, $y + 38, $s);
        $o .= btn($t, 1040, $y + 18, 130, 32, 'Подтвердить', 'primary', 13) . btn($t, 1180, $y + 18, 120, 32, 'Отменить', 'ghost', 13);
        $y += 68;
    }
    $o .= txt(120, 500, 'Журнал писем', 26, $t['text'], 800);
    $o .= card_rect($t, 120, 520, 1200, 44 + 3 * 52, 12);
    $o .= table_head($t, 120, 520, 1200, [[144, 'Кому'], [480, 'Тема'], [1000, 'Когда']]);
    $mails = [['olga@example.com', 'Ваша запись подтверждена', '16.11.2026 10:50'],
              ['anna@example.com', 'Ваша запись отменена', '14.11.2026 09:20'],
              ['juri@example.com', 'Смена пароля Autokool', '13.11.2026 21:02']];
    $y = 564;
    foreach ($mails as [$to, $subj, $when]) {
        $o .= rect(120, $y + 51, 1200, 1, $t['border']);
        $o .= txt(144, $y + 32, $to, 15, $t['text']) . txt(480, $y + 32, $subj, 15, $t['text']) . txt(1000, $y + 32, $when, 15, $t['muted']);
        $y += 52;
    }
    hot('admin', 120, 18, 160, 30, 'home');
    $o .= page_footer($t, $W, 940);
    return $o . "</svg>\n";
}

function screen_mobile($t): string
{
    $W = 375; $H = 812;
    $o = svg_open($W, $H, $t);
    $o .= rect(0, 0, $W, 56, $t['surface']) . rect(0, 55, $W, 1, $t['border']);
    $o .= rect(16, 14, 26, 26, $t['accent'], 7) . txt(50, 33, 'Автошкола', 17, $t['text'], 800);
    $o .= txt(343, 35, '☰', 24, $t['text'], 400, 'middle');
    $o .= rect(16, 72, 343, 210, $t['hero'], 14);
    $title = $t['line'] ? '#5a5a5a' : '#ffffff';
    $o .= txt(187, 122, 'Водительские права', 24, $title, 800, 'middle') . txt(187, 150, 'без стресса', 24, $title, 800, 'middle');
    $o .= txt(80, 230, '15+', 22, $title, 800, 'middle') . txt(187, 230, '2 400+', 22, $title, 800, 'middle') . txt(295, 230, '96%', 22, $title, 800, 'middle');
    $o .= txt(80, 252, 'инструкторов', 11, $title, 400, 'middle') . txt(187, 252, 'студентов', 11, $title, 400, 'middle') . txt(295, 252, 'с 1 попытки', 11, $title, 400, 'middle');
    $o .= rect(16, 298, 255, 40, $t['surface'], 10, $t['muted']) . txt(30, 323, 'Поиск курсов', 14, $t['muted']);
    $o .= btn($t, 279, 298, 80, 40, 'Найти', 'primary', 14);
    $x = 16;
    foreach (['Все', 'B', 'A', 'C', 'D'] as $i => $c) {
        $w = mb_strlen($c) * 9 + 30;
        $o .= rect($x, 352, $w, 30, $i === 0 ? $t['accent2'] : $t['chipOff'], 15, $i === 0 ? null : $t['muted']);
        $o .= txt($x + $w / 2, 372, $c, 13, $i === 0 ? '#ffffff' : $t['text'], 500, 'middle');
        $x += $w + 8;
    }
    $y = 398;
    foreach ([['Базовый курс категории B', '890,00 €'], ['Экспресс-курс категории B', '490,00 €']] as [$title2, $price]) {
        $o .= card_rect($t, 16, $y, 343, 190, 12);
        $o .= rect(32, $y + 16, 100, 20, $t['accent2'], 10) . txt(82, $y + 31, 'Категория B', 11, '#ffffff', 600, 'middle');
        $o .= txt(32, $y + 64, $title2, 17, $t['text'], 700);
        $o .= para($t, 32, $y + 90, 'Курс теории и практики вождения.', 13, 240);
        $o .= txt(32, $y + 118, 'Цена: ', 13, $t['muted']) . txt(72, $y + 118, $price, 13, $t['text'], 700);
        $o .= btn($t, 32, $y + 136, 311, 38, 'Записаться', 'primary', 14);
        $y += 206;
    }
    return $o . "</svg>\n";
}

// ---------- Genereerimine ----------

$screens = [
    'home' => ['01-avaleht', 'screen_home', 'Главная'],
    'course' => ['02-kursus', 'screen_course', 'Страница курса'],
    'login' => ['03-sisselogimine', 'screen_login', 'Вход'],
    'my' => ['04-minu-registreeringud', 'screen_my', 'Мои записи'],
    'admin' => ['05-admin', 'screen_admin', 'Админка'],
    'mobile' => ['06-mobiil-avaleht', 'screen_mobile', 'Мобильная главная'],
];

$dir = __DIR__;
foreach (THEMES as $themeName => $theme) {
    @mkdir("$dir/$themeName", 0777, true);
    foreach ($screens as [$file, $fn]) {
        file_put_contents("$dir/$themeName/$file.svg", $fn($theme));
    }
}

// Hotspotid koguti kaks korda (kaks teemat) — jätame ühe komplekti
foreach ($hotspots as $k => $list) {
    $hotspots[$k] = array_slice($list, 0, intdiv(count($list), 2));
}

// ---------- Klikitav prototüüp ----------
$sizes = ['home' => [1440, 1080], 'course' => [1440, 1024], 'login' => [1440, 900], 'my' => [1440, 900], 'admin' => [1440, 1024], 'mobile' => [375, 812]];
$data = [];
foreach ($screens as $key => [$file, , $label]) {
    $data[$key] = ['file' => $file, 'label' => $label, 'w' => $sizes[$key][0], 'h' => $sizes[$key][1], 'hot' => $hotspots[$key] ?? []];
}
$json = json_encode($data, JSON_UNESCAPED_UNICODE);
$html = <<<HTML
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Autokool — prototüüp</title>
<style>
  body { margin: 0; font-family: system-ui, sans-serif; background: #1d2433; color: #fff; }
  .bar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; padding: 12px 16px; background: #111827; position: sticky; top: 0; z-index: 2; }
  .bar button { border: 0; border-radius: 8px; padding: 8px 12px; cursor: pointer; background: #2b3550; color: #fff; }
  .bar button.on { background: #ff6b1a; }
  .bar .sep { flex: 1; }
  .stage { padding: 24px 16px; display: flex; justify-content: center; }
  .frame { position: relative; width: 100%; max-width: var(--w); }
  .frame img { width: 100%; display: block; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,.4); }
  .hot { position: absolute; border-radius: 8px; cursor: pointer; }
  body.hints .hot { outline: 2px dashed #ffcc00; background: rgba(255,204,0,.12); }
  .note { text-align: center; color: #9aa5bd; font-size: 14px; padding-bottom: 24px; }
</style>
</head>
<body>
<div class="bar" id="bar"></div>
<div class="stage"><div class="frame" id="frame"><img id="img" alt=""></div></div>
<p class="note">Нажимайте на кнопки макета, чтобы переходить между экранами. «Подсказки» показывают кликабельные области.</p>
<script>
const SCREENS = $json;
let theme = 'ui', current = 'home';
const bar = document.getElementById('bar'), frame = document.getElementById('frame'), img = document.getElementById('img');
function render() {
  bar.innerHTML = '';
  Object.entries(SCREENS).forEach(([k, s]) => {
    const b = document.createElement('button');
    b.textContent = s.label; b.className = k === current ? 'on' : '';
    b.onclick = () => { current = k; render(); };
    bar.appendChild(b);
  });
  const sep = document.createElement('span'); sep.className = 'sep'; bar.appendChild(sep);
  [['ui', 'Цветной дизайн'], ['wireframe', 'Серый макет']].forEach(([k, l]) => {
    const b = document.createElement('button'); b.textContent = l; b.className = theme === k ? 'on' : '';
    b.onclick = () => { theme = k; render(); }; bar.appendChild(b);
  });
  const h = document.createElement('button'); h.textContent = 'Подсказки';
  h.className = document.body.classList.contains('hints') ? 'on' : '';
  h.onclick = () => { document.body.classList.toggle('hints'); render(); }; bar.appendChild(h);

  const s = SCREENS[current];
  frame.style.setProperty('--w', s.w + 'px');
  img.src = theme + '/' + s.file + '.svg';
  img.alt = s.label;
  frame.querySelectorAll('.hot').forEach(e => e.remove());
  s.hot.forEach(a => {
    const d = document.createElement('a');
    d.className = 'hot'; d.title = SCREENS[a.to].label;
    d.style.left = (a.x / s.w * 100) + '%'; d.style.top = (a.y / s.h * 100) + '%';
    d.style.width = (a.w / s.w * 100) + '%'; d.style.height = (a.h / s.h * 100) + '%';
    d.onclick = () => { current = a.to; render(); window.scrollTo(0, 0); };
    frame.appendChild(d);
  });
}
render();
</script>
</body>
</html>
HTML;
file_put_contents("$dir/prototype.html", $html);
echo "OK: " . count($screens) . " ekraani x " . count(THEMES) . " versiooni\n";
