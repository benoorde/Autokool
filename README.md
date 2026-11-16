# Autokool

Veebileht autokooli kursuste pakkumiseks. Keeled: vene (põhi) ja eesti.

## Funktsioonid

- Kursuste nimekiri kategooriate järgi (B, A, C, D)
- Kursuse detailvaade koos instruktori ja arvustustega
- Registreerimine, sisselogimine, "minu registreeringud"
- Admin: registreeringute kinnitamine ja tühistamine
- Keelevahetus (ru / et)
- Adaptiivne kujundus, animatsioonid ja kerimisefektid
- Tume teema järgib süsteemi seadet (prefers-color-scheme)

## Dokumentatsioon

- `docs/SPEC.md` — nõuete spetsifikatsioon (ISO/IEC/IEEE 29148)
- `docs/USER-GUIDE.md` — kasutusjuhend (ISO 26514)
- `docs/JIRA-TASKS.md` — Jira ülesannete nimekiri
- `docs/mockup/index.html` — lihtne HTML-makett
- `docs/design/` — hall makett, värviline disain ja klikitav prototüüp (Figma jaoks SVG)

## Käivitamine (Docker)

Windowsis: topeltklõps failil `start.bat`. See käivitab Docker Desktopi, projekti ja avab lehe brauseris.

Või käsurealt:

```bash
docker compose up -d --build
```

- Veebileht: http://localhost:8080
- Andmebaas brauseris (phpMyAdmin): http://localhost:8081
- Andmebaas: port 3307 (kasutaja `autokool`, parool `autokool`)
- Admin: `admin@autokool.test` / `admin123`

## Struktuur

- `src/` — PHP lehed ja staatilised failid
- `src/includes/` — päis, jalus, tõlked, abifunktsioonid
- `db/init.sql` — tabelid ja näidisandmed
- `docker/` — PHP/Apache konteiner
