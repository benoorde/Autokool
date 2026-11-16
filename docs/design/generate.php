<?php
// Autokooli disaini generaator: loob iga ekraani kahes versioonis
//   wireframe/  hall makett (wireframe)
//   ui/         värviline disain (fotod, valgusfoor, teemärgistus)
// ja klikitava prototüübi prototype.html.
// Käivitamine: php docs/design/generate.php

const THEMES = [
    'ui' => [
        'bg' => '#f4f5f7', 'surface' => '#ffffff', 'text' => '#15171c', 'muted' => '#5f6673',
        'accent' => '#ff6b1a', 'dark' => '#121418', 'yellow' => '#ffc400', 'border' => '#e4e6eb',
        'ok' => '#1a9c5b', 'bad' => '#d64545', 'onAccent' => '#ffffff',
        'header' => '#121418', 'headerText' => '#d8dbe0', 'thText' => '#ffc400',
        'chipOff' => '#ffffff', 'line' => null, 'link' => '#e0560a',
    ],
    'wireframe' => [
        'bg' => '#f2f2f2', 'surface' => '#ffffff', 'text' => '#4a4a4a', 'muted' => '#9a9a9a',
        'accent' => '#7a7a7a', 'dark' => '#8a8a8a', 'yellow' => '#bdbdbd', 'border' => '#c9c9c9',
        'ok' => '#8f8f8f', 'bad' => '#8f8f8f', 'onAccent' => '#ffffff',
        'header' => '#ffffff', 'headerText' => '#4a4a4a', 'thText' => '#ffffff',
        'chipOff' => '#ffffff', 'line' => '#c4c4c4', 'link' => '#7a7a7a',
    ],
];
const IMG_DIR = __DIR__ . '/../../src/assets/img/';

$hotspots = [];
$clipId = 0;

function esc(string $s): string { return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8'); }

function rect($x, $y, $w, $h, $fill, $rx = 0, $stroke = null, $sw = 1, $extra = ''): string
{
    $s = $stroke ? " stroke=\"$stroke\" stroke-width=\"$sw\"" : '';
    return "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" rx=\"$rx\" fill=\"$fill\"$s$extra/>\n";
}

function txt($x, $y, $str, $size, $fill, $weight = 400, $anchor = 'start', $extra = ''): string
{
    return "<text x=\"$x\" y=\"$y\" font-family=\"Inter, Segoe UI, Arial, sans-serif\" font-size=\"$size\" font-weight=\"$weight\" fill=\"$fill\" text-anchor=\"$anchor\"$extra>" . esc($str) . "</text>\n";
}

// Tekst või halli makettis tekstiriba
function para($t, $x, $y, $str, $size, $width = 0, $color = null): string
{
    if ($t['line'] && $width > 0) {
        return rect($x, $y - $size + 3, $width, $size - 4, $t['line'], 3);
    }
    return txt($x, $y, $str, $size, $color ?? $t['muted']);
}

function btn($t, $x, $y, $w, $h, $label, $variant = 'primary', $size = 15): string
{
    $ty = $y + $h / 2 + $size / 3;
    switch ($variant) {
        case 'ghost':
            return rect($x, $y, $w, $h, $t['surface'], 12, $t['muted']) . txt($x + $w / 2, $ty, $label, $size, $t['text'], 700, 'middle');
        case 'yellow':
            return rect($x, $y, $w, $h, $t['yellow'], 12) . txt($x + $w / 2, $ty, $label, $size, $t['line'] ? '#ffffff' : $t['dark'], 700, 'middle');
        case 'glass':
            $fill = $t['line'] ? '#ffffff' : 'rgba(255,255,255,0.12)';
            return rect($x, $y, $w, $h, $fill, 12, $t['line'] ? $t['muted'] : 'rgba(255,255,255,0.35)') . txt($x + $w / 2, $ty, $label, $size, $t['line'] ? $t['text'] : '#ffffff', 700, 'middle');
        default:
            return rect($x, $y, $w, $h, $t['accent'], 12) . txt($x + $w / 2, $ty, $label, $size, $t['onAccent'], 700, 'middle');
    }
}

function image_box($t, $x, $y, $w, $h, $rx = 8): string
{
    // Pildi koht makettis: hall kast ristiga
    return rect($x, $y, $w, $h, '#e6e6e6', $rx, $t['border'])
        . "<line x1=\"$x\" y1=\"$y\" x2=\"" . ($x + $w) . "\" y2=\"" . ($y + $h) . "\" stroke=\"{$t['border']}\"/>\n"
        . "<line x1=\"" . ($x + $w) . "\" y1=\"$y\" x2=\"$x\" y2=\"" . ($y + $h) . "\" stroke=\"{$t['border']}\"/>\n";
}

// Foto (disainis) või pildikoht (makettis)
function photo($t, $x, $y, $w, $h, $file, $rx = 0, $roundBottom = true): string
{
    global $clipId;
    if ($t['line']) {
        return image_box($t, $x, $y, $w, $h, $rx);
    }
    $clipId++;
    $id = 'c' . $clipId;
    $ch = $roundBottom ? $h : $h + $rx;
    $data = base64_encode(file_get_contents(IMG_DIR . $file));
    return "<clipPath id=\"$id\"><rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$ch\" rx=\"$rx\"/></clipPath>\n"
        . "<image x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" preserveAspectRatio=\"xMidYMid slice\" clip-path=\"url(#$id)\" xlink:href=\"data:image/jpeg;base64,$data\"/>\n";
}

function svg_open($w, $h, $t): string
{
    $defs = '<defs>'
        . '<linearGradient id="shade" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#0a0b0e" stop-opacity="0.93"/><stop offset="0.5" stop-color="#0a0b0e" stop-opacity="0.66"/><stop offset="1" stop-color="#0a0b0e" stop-opacity="0.2"/></linearGradient>'
        . '<linearGradient id="shadeV" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0a0b0e" stop-opacity="0.55"/><stop offset="1" stop-color="#0a0b0e" stop-opacity="0.9"/></linearGradient>'
        . '<filter id="sh" x="-10%" y="-10%" width="120%" height="140%"><feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#121418" flood-opacity="0.10"/></filter>'
        . '<filter id="glow" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="10"/></filter>'
        . '</defs>';
    return "<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"$w\" height=\"$h\" viewBox=\"0 0 $w $h\">\n$defs\n"
        . rect(0, 0, $w, $h, $t['bg']);
}

function card_rect($t, $x, $y, $w, $h, $rx = 16): string
{
    $f = $t['line'] ? '' : ' filter="url(#sh)"';
    $s = $t['line'] ? " stroke=\"{$t['border']}\"" : '';
    return "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" rx=\"$rx\" fill=\"{$t['surface']}\"$s$f/>\n";
}

function dashes($x1, $x2, $y, $h, $color, $dash, $gap): string
{
    $o = '';
    for ($x = $x1; $x < $x2; $x += $dash + $gap) {
        $o .= rect($x, $y, min($dash, $x2 - $x), $h, $color, $h / 2);
    }
    return $o;
}

function logo($t, $x, $y, $size = 34, $font = 20): string
{
    $o = rect($x, $y, $size, $size, $t['yellow'], 8, $t['line'] ? null : $t['dark'], 2);
    $o .= txt($x + $size / 2, $y + $size * 0.7, 'A', $size * 0.55, $t['line'] ? '#ffffff' : $t['dark'], 900, 'middle');
    $o .= txt($x + $size + 10, $y + $size * 0.7, 'Автошкола', $font, $t['line'] ? $t['text'] : '#ffffff', 800);
    return $o;
}

function page_header($t, $w, $active = 'home', $logged = false, $admin = false): string
{
    $o = rect(0, 0, $w, 64, $t['header']) . rect(0, 63, $w, 1, $t['line'] ? $t['border'] : '#23262d');
    $o .= logo($t, 120, 15);
    $items = $logged
        ? ['home' => 'Главная', 'courses' => 'Курсы', 'my' => 'Мои записи'] + ($admin ? ['admin' => 'Админка'] : []) + ['logout' => 'Выход']
        : ['home' => 'Главная', 'courses' => 'Курсы', 'login' => 'Вход'];
    $x = $logged ? ($admin ? 690 : 790) : 820;
    foreach ($items as $k => $label) {
        $o .= txt($x, 38, $label, 15, $k === $active ? ($t['line'] ? $t['accent'] : $t['yellow']) : $t['headerText'], 600);
        $x += mb_strlen($label) * 9 + 28;
    }
    if (!$logged) {
        $o .= btn($t, $x, 15, 130, 34, 'Регистрация', 'primary', 14);
        $x += 150;
    }
    $o .= rect($x - 4, 18, 76, 28, $t['line'] ? '#eeeeee' : '#262a31', 8);
    $o .= rect($x, 22, 34, 20, $t['yellow'], 6) . txt($x + 17, 37, 'RU', 12, $t['line'] ? '#ffffff' : $t['dark'], 700, 'middle');
    $o .= txt($x + 44, 37, 'ET', 12, $t['headerText'], 600);
    return $o;
}

function page_footer($t, $w, $y): string
{
    if ($t['line']) {
        return rect(0, $y, $w, 1, $t['border']) . txt($w / 2, $y + 40, '© Автошкола. Все права защищены.', 14, $t['muted'], 400, 'middle');
    }
    $o = rect(0, $y, $w, 70, $t['dark']);
    for ($x = 0; $x < $w; $x += 80) {
        $o .= rect($x, $y, 40, 6, $t['yellow']);
    }
    return $o . txt($w / 2, $y + 44, '© Автошкола. Все права защищены.', 14, '#9aa1ad', 400, 'middle');
}

function hot($screen, $x, $y, $w, $h, $to)
{
    global $hotspots;
    $hotspots[$screen][] = compact('x', 'y', 'w', 'h', 'to');
}

function traffic_light($t, $x, $y): string
{
    $o = rect($x + 53, $y + 300, 14, 130, $t['line'] ? '#bbbbbb' : '#22252c');
    $o .= rect($x, $y, 120, 300, $t['line'] ? '#d0d0d0' : '#1a1d22', 26, $t['line'] ? $t['border'] : '#2c3038', 3);
    $colors = $t['line'] ? ['#a5a5a5', '#bdbdbd', '#bdbdbd'] : ['#ff3b30', '#3a3420', '#1f3527'];
    foreach ($colors as $i => $c) {
        $cy = $y + 60 + $i * 92;
        if ($i === 0 && !$t['line']) {
            $o .= "<circle cx=\"" . ($x + 60) . "\" cy=\"$cy\" r=\"46\" fill=\"#ff3b30\" opacity=\"0.55\" filter=\"url(#glow)\"/>\n";
        }
        $o .= "<circle cx=\"" . ($x + 60) . "\" cy=\"$cy\" r=\"38\" fill=\"$c\"/>\n";
    }
    if (!$t['line']) {
        $o .= txt($x - 26, $y + 66, 'Стоп-сомнения', 16, '#ff5a4f', 800, 'end');
    }
    return $o;
}

// ---------- Ekraanid ----------

function screen_home($t): string
{
    $W = 1440; $H = 1770;
    $o = svg_open($W, $H, $t);
    // hero: foto, tumendus, pealkiri, valgusfoor, teemärgistus
    if ($t['line']) {
        $o .= image_box($t, 0, 64, $W, 620, 0);
        $o .= rect(0, 64, $W, 620, 'rgba(255,255,255,0.55)');
    } else {
        $o .= photo($t, 0, 64, $W, 620, 'hero.jpg') . rect(0, 64, $W, 620, 'url(#shade)');
    }
    $o .= page_header($t, $W, 'home');
    $light = $t['line'] ? $t['text'] : '#ffffff';
    $o .= rect(120, 140, 300, 32, $t['line'] ? '#e2e2e2' : 'rgba(255,196,0,0.16)', 16) . txt(140, 161, 'Автошкола · категории A, B, C, D', 14, $t['line'] ? $t['text'] : $t['yellow'], 700);
    $o .= txt(120, 270, 'Права —', 84, $light, 900);
    $o .= "<g transform=\"rotate(-1.5 330 330)\">" . rect(112, 290, 530, 94, $t['yellow'], 12) . txt(130, 364, 'без стресса', 84, $t['line'] ? '#ffffff' : $t['dark'], 900) . "</g>\n";
    $o .= para($t, 120, 432, 'Опытные инструкторы, гибкое расписание и успешная сдача экзамена.', 19, 560, $t['line'] ? null : 'rgba(255,255,255,0.82)');
    $o .= btn($t, 120, 466, 170, 50, 'Выбрать курс', 'yellow', 16);
    $o .= btn($t, 304, 466, 240, 50, 'Как проходит обучение', 'glass', 16);
    hot('home', 120, 466, 170, 50, 'course');
    $stats = [['15+', 'инструкторов'], ['2 400+', 'студентов'], ['96%', 'сдача с первой попытки']];
    $x = 120;
    foreach ($stats as [$n, $l]) {
        $o .= rect($x, 544, 186, 84, $t['line'] ? '#ffffff' : 'rgba(255,255,255,0.08)', 14, $t['line'] ? $t['border'] : 'rgba(255,255,255,0.16)');
        $o .= txt($x + 18, 584, $n, 30, $t['line'] ? $t['text'] : $t['yellow'], 900) . txt($x + 18, 610, $l, 13, $t['line'] ? $t['muted'] : 'rgba(255,255,255,0.75)');
        $x += 200;
    }
    $o .= traffic_light($t, 1160, 150);
    $o .= dashes(0, $W, 646, 8, $t['yellow'], 60, 50);

    // kursused
    $o .= txt(120, 776, 'Курсы', 46, $t['text'], 900);
    $o .= dashes(120, 196, 792, 6, $t['yellow'], 18, 8);
    $o .= para($t, 120, 832, 'Выберите категорию — остальное мы берём на себя.', 17, 420);
    $o .= rect(120, 856, 1070, 46, $t['surface'], 12, $t['border']) . txt(140, 885, 'Поиск курсов', 15, $t['muted']);
    $o .= btn($t, 1210, 856, 110, 46, 'Найти');
    $chips = ['Все категории', 'A', 'B', 'C', 'D'];
    $x = 120;
    foreach ($chips as $i => $c) {
        $w = $i === 0 ? 150 : 50;
        $on = $i === 0;
        $o .= rect($x, 920, $w, 36, $on ? $t['dark'] : $t['chipOff'], $i === 0 ? 18 : 6, $on ? null : $t['border']);
        $o .= txt($x + $w / 2, 944, $c, 15, $on ? ($t['line'] ? '#ffffff' : $t['yellow']) : $t['text'], 800, 'middle');
        $x += $w + 10;
    }
    $cards = [
        ['B', 'Экспресс-курс категории B', 'Ускоренная подготовка для тех,', 'кто уже знаком с управлением.', '15', '05.12.2026', '490 €', 'kat-b2.jpg'],
        ['A', 'Курс категории A', 'Обучение управлению', 'мотоциклом с нуля.', '20', '10.12.2026', '690 €', 'kat-a.jpg'],
        ['B', 'Базовый курс категории B', 'Полный курс теории и практики', 'вождения легкового автомобиля.', '30', '01.12.2026', '890 €', 'kat-b.jpg'],
        ['C', 'Курс категории C', 'Подготовка водителей', 'грузовых автомобилей.', '40', '—', '1 490 €', 'kat-c.jpg'],
    ];
    $x = 120;
    foreach ($cards as $i => [$code, $title, $d1, $d2, $les, $date, $price, $img]) {
        $o .= card_rect($t, $x, 984, 285, 410);
        $o .= photo($t, $x, 984, 285, 178, $img, 16, false);
        $o .= rect($x + 14, 998, 44, 28, '#ffffff', 6, $t['line'] ? $t['border'] : $t['dark'], 2) . txt($x + 36, 1018, $code, 16, $t['line'] ? $t['text'] : $t['dark'], 900, 'middle');
        $o .= txt($x + 20, 1196, $title, 17, $t['text'], 800);
        $o .= para($t, $x + 20, 1224, $d1, 14, 230) . para($t, $x + 20, 1244, $d2, 14, 180);
        $o .= txt($x + 20, 1276, '⏱ ' . $les . ' уроков', 13, $t['muted']) . txt($x + 140, 1276, '📅 ' . $date, 13, $t['muted']);
        $o .= dashes($x + 20, $x + 265, 1300, 1, $t['border'], 4, 3);
        $o .= txt($x + 20, 1350, 'от', 12, $t['muted'], 600) . txt($x + 42, 1352, $price, 24, $t['text'], 900);
        $o .= btn($t, $x + 150, 1322, 115, 40, 'Записаться', 'primary', 14);
        if ($i === 2) {
            hot('home', $x + 150, 1322, 115, 40, 'course');
        }
        $x += 305;
    }

    // kolm sammu
    if ($t['line']) {
        $o .= image_box($t, 0, 1440, $W, 260, 0) . rect(0, 1440, $W, 260, 'rgba(255,255,255,0.6)');
    } else {
        $o .= photo($t, 0, 1440, $W, 260, 'tee.jpg') . rect(0, 1440, $W, 260, 'rgba(12,13,16,0.8)');
    }
    $o .= txt(120, 1500, 'Три шага до прав', 40, $light, 900);
    $o .= dashes(190, 1100, 1555, 4, $t['yellow'], 30, 20);
    $steps = [['Теория', 'Онлайн-занятия и тесты, как на экзамене.'], ['Площадка и город', 'Практика: от парковки до трассы.'], ['Экзамен', 'Пробный экзамен и сопровождение.']];
    $x = 120;
    foreach ($steps as $i => [$st, $sd]) {
        $o .= "<circle cx=\"" . ($x + 32) . "\" cy=\"1557\" r=\"40\" fill=\"" . ($t['line'] ? '#dddddd' : 'rgba(255,196,0,0.22)') . "\"/>\n";
        $o .= "<circle cx=\"" . ($x + 32) . "\" cy=\"1557\" r=\"32\" fill=\"{$t['yellow']}\"/>\n";
        $o .= txt($x + 32, 1567, (string)($i + 1), 26, $t['line'] ? '#ffffff' : $t['dark'], 900, 'middle');
        $o .= txt($x, 1630, $st, 21, $light, 800);
        $o .= para($t, $x, 1658, $sd, 15, 300, $t['line'] ? null : 'rgba(255,255,255,0.8)');
        $x += 400;
    }
    $o .= page_footer($t, $W, 1700 - 0);
    return str_replace('height="1700" viewBox="0 0 1440 1700"', 'height="1770" viewBox="0 0 1440 1770"', $o) . "</svg>\n";
}

function screen_course($t): string
{
    $W = 1440; $H = 1024;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'courses');
    $o .= card_rect($t, 120, 96, 1200, 420);
    $o .= photo($t, 760, 96, 560, 420, 'kat-a.jpg', 16);
    if (!$t['line']) {
        $o .= rect(760, 96, 30, 420, $t['surface']);
    }
    $o .= txt(150, 140, '← Ко всем курсам', 15, $t['link'], 600);
    hot('course', 150, 122, 160, 26, 'home');
    $o .= rect(296, 124, 170, 24, $t['dark'], 6) . txt(381, 141, 'Категория A (мотоциклы)', 12, $t['line'] ? '#ffffff' : $t['yellow'], 700, 'middle');
    $o .= txt(150, 208, 'Курс категории A', 40, $t['text'], 900);
    $o .= para($t, 150, 246, 'Обучение управлению мотоциклом с нуля.', 16, 360);
    $o .= txt(150, 282, 'Уроков: 20', 15, $t['muted']) . txt(260, 282, 'Цена: ', 15, $t['muted']) . txt(306, 282, '690,00 €', 15, $t['text'], 800);
    $o .= rect(150, 312, 5, 76, $t['yellow']) . txt(170, 338, 'Инструктор: Mart Tamm', 18, $t['text'], 800);
    $o .= para($t, 170, 366, 'Инструктор высшей категории, специализируется на начинающих водителях.', 15, 520);
    if (!$t['line']) {
        $o .= rect(144, 418, 192, 56, 'none', 16, '#ff6b1a', 2);
    }
    $o .= btn($t, 150, 424, 180, 44, 'Записаться', 'primary', 16);
    hot('course', 150, 424, 180, 44, 'login');
    $o .= txt(120, 584, 'Отзывы', 28, $t['text'], 900);
    $reviews = [['Olga', 5, 'Отличный курс, сдала с первого раза!'], ['Jüri', 4, 'Хорошие инструкторы и удобное расписание.']];
    $y = 608;
    foreach ($reviews as [$n, $r, $text]) {
        $o .= card_rect($t, 120, $y, 1200, 84, 14);
        $o .= txt(144, $y + 32, $n, 16, $t['text'], 800) . txt(200, $y + 32, str_repeat('★', $r), 16, $t['line'] ? $t['muted'] : '#f5a623');
        $o .= para($t, 144, $y + 60, $text, 15, 380);
        $y += 100;
    }
    $o .= page_footer($t, $W, 954);
    return $o . "</svg>\n";
}

function screen_login($t): string
{
    $W = 1440; $H = 900;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'login');
    $o .= card_rect($t, 510, 140, 420, 420);
    $o .= rect(510, 140, 420, 6, $t['yellow'], 3);
    $o .= txt(540, 200, 'Вход', 32, $t['text'], 900);
    $o .= txt(540, 244, 'Email', 15, $t['text'], 600) . rect(540, 254, 360, 44, $t['bg'], 10, $t['border']);
    $o .= txt(556, 282, 'admin@autokool.test', 15, $t['muted']);
    $o .= txt(540, 328, 'Пароль', 15, $t['text'], 600) . rect(540, 338, 360, 44, $t['bg'], 10, $t['border']);
    $o .= txt(556, 366, '••••••••', 15, $t['muted']);
    $o .= btn($t, 540, 404, 150, 44, 'Отправить');
    hot('login', 540, 404, 150, 44, 'my');
    $o .= txt(540, 484, 'Забыли пароль?', 15, $t['link'], 600);
    $o .= txt(540, 516, 'Нет аккаунта? Регистрация', 15, $t['muted']);
    $o .= page_footer($t, $W, 830);
    return $o . "</svg>\n";
}

function status_pill($t, $x, $y, $kind): string
{
    $map = ['new' => ['Новая', $t['accent']], 'confirmed' => ['Подтверждена', $t['ok']], 'cancelled' => ['Отменена', $t['bad']]];
    [$label, $color] = $map[$kind];
    $w = mb_strlen($label) * 8 + 24;
    return rect($x, $y - 16, $w, 24, $color, 12) . txt($x + $w / 2, $y, $label, 13, '#ffffff', 700, 'middle');
}

function table_head($t, $x, $y, $w, array $cols): string
{
    $o = rect($x, $y, $w, 46, $t['dark'], 14);
    foreach ($cols as [$cx, $label]) {
        $o .= txt($cx, $y + 29, $label, 15, $t['thText'], 800);
    }
    return $o;
}

function screen_my($t): string
{
    $W = 1440; $H = 900;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'my', true);
    $o .= txt(120, 144, 'Мои записи', 34, $t['text'], 900);
    $o .= rect(120, 164, 1200, 46, $t['line'] ? '#e6e6e6' : '#e6f8ef', 10) . txt(140, 193, 'Вы записаны! Мы свяжемся с вами.', 15, $t['line'] ? $t['text'] : $t['ok'], 700);
    $o .= card_rect($t, 120, 230, 1200, 46 + 3 * 60, 14);
    $o .= table_head($t, 120, 230, 1200, [[144, 'Курс'], [620, 'Дата'], [820, 'Статус']]);
    $rows = [['Базовый курс категории B', '16.11.2026', 'new'], ['Курс категории A', '10.11.2026', 'confirmed'], ['Экспресс-курс категории B', '02.11.2026', 'cancelled']];
    $y = 276;
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
    $o .= page_footer($t, $W, 830);
    return $o . "</svg>\n";
}

function screen_admin($t): string
{
    $W = 1440; $H = 1024;
    $o = svg_open($W, $H, $t) . page_header($t, $W, 'admin', true, true);
    $o .= txt(120, 144, 'Админка', 34, $t['text'], 900);
    $o .= card_rect($t, 120, 164, 1200, 46 + 3 * 68, 14);
    $o .= table_head($t, 120, 164, 1200, [[144, 'Пользователь'], [420, 'Курс'], [700, 'Дата'], [880, 'Статус']]);
    $rows = [['Olga Ivanova', 'olga@example.com', 'Базовый курс категории B', '16.11.2026 10:42', 'new'],
             ['Jüri Kask', 'juri@example.com', 'Курс категории A', '15.11.2026 18:05', 'confirmed'],
             ['Anna Lepp', 'anna@example.com', 'Курс категории C', '14.11.2026 09:13', 'cancelled']];
    $y = 210;
    foreach ($rows as [$n, $e, $c, $d, $s]) {
        $o .= rect(120, $y + 67, 1200, 1, $t['border']);
        $o .= txt(144, $y + 30, $n, 15, $t['text'], 700) . txt(144, $y + 50, $e, 13, $t['muted']);
        $o .= txt(420, $y + 38, $c, 15, $t['text']) . txt(700, $y + 38, $d, 15, $t['text']);
        $o .= status_pill($t, 880, $y + 38, $s);
        $o .= btn($t, 1040, $y + 18, 130, 32, 'Подтвердить', 'primary', 13) . btn($t, 1180, $y + 18, 120, 32, 'Отменить', 'ghost', 13);
        $y += 68;
    }
    $o .= txt(120, 506, 'Журнал писем', 28, $t['text'], 900);
    $o .= card_rect($t, 120, 526, 1200, 46 + 3 * 52, 14);
    $o .= table_head($t, 120, 526, 1200, [[144, 'Кому'], [480, 'Тема'], [1000, 'Когда']]);
    $mails = [['olga@example.com', 'Ваша запись подтверждена', '16.11.2026 10:50'],
              ['anna@example.com', 'Ваша запись отменена', '14.11.2026 09:20'],
              ['juri@example.com', 'Смена пароля Autokool', '13.11.2026 21:02']];
    $y = 572;
    foreach ($mails as [$to, $subj, $when]) {
        $o .= rect(120, $y + 51, 1200, 1, $t['border']);
        $o .= txt(144, $y + 32, $to, 15, $t['text']) . txt(480, $y + 32, $subj, 15, $t['text']) . txt(1000, $y + 32, $when, 15, $t['muted']);
        $y += 52;
    }
    hot('admin', 120, 15, 160, 34, 'home');
    $o .= page_footer($t, $W, 954);
    return $o . "</svg>\n";
}

function screen_mobile($t): string
{
    $W = 375; $H = 1180;
    $o = svg_open($W, $H, $t);
    if ($t['line']) {
        $o .= image_box($t, 0, 56, $W, 520, 0) . rect(0, 56, $W, 520, 'rgba(255,255,255,0.55)');
    } else {
        $o .= photo($t, 0, 56, $W, 520, 'hero.jpg') . rect(0, 56, $W, 520, 'url(#shadeV)');
    }
    $o .= rect(0, 0, $W, 56, $t['header']) . rect(0, 55, $W, 1, $t['line'] ? $t['border'] : '#23262d');
    $o .= logo($t, 16, 12, 32, 18);
    $o .= txt(343, 37, '☰', 26, $t['line'] ? $t['text'] : '#ffffff', 400, 'middle');
    $light = $t['line'] ? $t['text'] : '#ffffff';
    $o .= rect(16, 88, 250, 28, $t['line'] ? '#e2e2e2' : 'rgba(255,196,0,0.16)', 14) . txt(30, 107, 'Автошкола · A, B, C, D', 13, $t['line'] ? $t['text'] : $t['yellow'], 700);
    $o .= txt(16, 176, 'Права —', 46, $light, 900);
    $o .= "<g transform=\"rotate(-1.5 140 210)\">" . rect(12, 190, 286, 56, $t['yellow'], 10) . txt(24, 233, 'без стресса', 44, $t['line'] ? '#ffffff' : $t['dark'], 900) . "</g>\n";
    $o .= para($t, 16, 290, 'Опытные инструкторы, гибкое', 15, 260, $t['line'] ? null : 'rgba(255,255,255,0.82)');
    $o .= para($t, 16, 312, 'расписание и успешная сдача экзамена.', 15, 300, $t['line'] ? null : 'rgba(255,255,255,0.82)');
    $o .= btn($t, 16, 334, 160, 46, 'Выбрать курс', 'yellow', 15);
    $o .= btn($t, 16, 390, 230, 46, 'Как проходит обучение', 'glass', 15);
    $x = 16;
    foreach ([['15+', 'инструкторов'], ['2400+', 'студентов'], ['96%', 'с 1 попытки']] as [$n, $l]) {
        $o .= rect($x, 456, 107, 74, $t['line'] ? '#ffffff' : 'rgba(255,255,255,0.08)', 12, $t['line'] ? $t['border'] : 'rgba(255,255,255,0.16)');
        $o .= txt($x + 12, 490, $n, 22, $t['line'] ? $t['text'] : $t['yellow'], 900) . txt($x + 12, 512, $l, 11, $t['line'] ? $t['muted'] : 'rgba(255,255,255,0.75)');
        $x += 118;
    }
    $o .= dashes(0, $W, 548, 6, $t['yellow'], 40, 30);
    $o .= txt(16, 628, 'Курсы', 34, $t['text'], 900) . dashes(16, 80, 642, 5, $t['yellow'], 14, 6);
    $o .= rect(16, 664, 255, 42, $t['surface'], 10, $t['border']) . txt(30, 690, 'Поиск курсов', 14, $t['muted']);
    $o .= btn($t, 279, 664, 80, 42, 'Найти', 'primary', 14);
    $x = 16;
    foreach (['Все', 'A', 'B', 'C', 'D'] as $i => $c) {
        $w = $i === 0 ? 70 : 44;
        $o .= rect($x, 720, $w, 32, $i === 0 ? $t['dark'] : $t['chipOff'], $i === 0 ? 16 : 6, $i === 0 ? null : $t['border']);
        $o .= txt($x + $w / 2, 741, $c, 14, $i === 0 ? ($t['line'] ? '#ffffff' : $t['yellow']) : $t['text'], 800, 'middle');
        $x += $w + 8;
    }
    $o .= card_rect($t, 16, 770, 343, 380, 16);
    $o .= photo($t, 16, 770, 343, 190, 'kat-b.jpg', 16, false);
    $o .= rect(30, 784, 40, 26, '#ffffff', 6, $t['line'] ? $t['border'] : $t['dark'], 2) . txt(50, 803, 'B', 15, $t['line'] ? $t['text'] : $t['dark'], 900, 'middle');
    $o .= txt(32, 994, 'Базовый курс категории B', 18, $t['text'], 800);
    $o .= para($t, 32, 1020, 'Полный курс теории и практики вождения.', 13, 280);
    $o .= txt(32, 1048, '⏱ 30 уроков   📅 01.12.2026', 13, $t['muted']);
    $o .= txt(32, 1110, 'от', 12, $t['muted'], 600) . txt(54, 1112, '890 €', 26, $t['text'], 900);
    $o .= btn($t, 210, 1082, 133, 42, 'Записаться', 'primary', 14);
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
$sizes = ['home' => [1440, 1770], 'course' => [1440, 1024], 'login' => [1440, 900], 'my' => [1440, 900], 'admin' => [1440, 1024], 'mobile' => [375, 1180]];
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
  body { margin: 0; font-family: system-ui, sans-serif; background: #121418; color: #fff; }
  .bar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; padding: 12px 16px; background: #0b0c0f; position: sticky; top: 0; z-index: 2; }
  .bar button { border: 0; border-radius: 8px; padding: 8px 12px; cursor: pointer; background: #262a31; color: #fff; }
  .bar button.on { background: #ffc400; color: #121418; }
  .bar .sep { flex: 1; }
  .stage { padding: 24px 16px; display: flex; justify-content: center; }
  .frame { position: relative; width: 100%; max-width: var(--w); }
  .frame img { width: 100%; display: block; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,.5); }
  .hot { position: absolute; border-radius: 8px; cursor: pointer; }
  body.hints .hot { outline: 2px dashed #ffc400; background: rgba(255,196,0,.15); }
  .note { text-align: center; color: #9aa1ad; font-size: 14px; padding-bottom: 24px; }
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
