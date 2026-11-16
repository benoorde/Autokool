# Disain

| Kaust / fail | Sisu |
|---|---|
| `wireframe/` | Hall makett (wireframe), 6 ekraani |
| `ui/` | Värviline disain, samad 6 ekraani |
| `prototype.html` | Klikitav prototüüp: ekraanide vahel liikumine, hall/värviline vahetus |
| `generate.php` | Generaator, mis loob kõik SVG-failid ja prototüübi |

Ekraanid: avaleht, kursuse leht, sisselogimine, minu registreeringud, admin, mobiilne avaleht.

## Figma

1. Ava Figma ja loo uus Design-fail.
2. Lohista kaustade `wireframe/` ja `ui/` SVG-failid lõuendile. Iga SVG muutub muudetavaks kihtide grupiks.
3. Vali iga grupp ja vajuta `Ctrl+Alt+G` (Frame selection), et sellest saaks frame.
4. Prototüübi jaoks: vahekaart **Prototype**, lohista nupust ühendus teise frame'ile (nt «Записаться» → kursuse leht).

## Uuesti genereerimine

```bash
php docs/design/generate.php
```

## Prototüübi vaatamine

```bash
php -S 127.0.0.1:8091 -t docs/design
```

Seejärel ava http://127.0.0.1:8091/prototype.html
