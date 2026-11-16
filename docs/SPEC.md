# Autokool — nõuete spetsifikatsioon (ISO/IEC/IEEE 29148)

Versioon 1.0. Keeled: vene (põhi) ja eesti.

## 1. Sissejuhatus

### 1.1 Eesmärk
Veebileht autokooli kursuste tutvustamiseks, registreerimiseks ja registreeringute haldamiseks.

### 1.2 Ulatus
Sait pakub kursuste nimekirja, detailvaateid, kasutajakontosid, registreerimist kursustele ja admini liidest registreeringute kinnitamiseks.

### 1.3 Sidusrühmad
- Õppija (külastaja ja registreerunud kasutaja)
- Administraator (autokooli töötaja)
- Arendaja / õppejõud (hindaja)

## 2. Viited
- ISO/IEC/IEEE 29148:2018 — nõuete tehnika
- ISO 26514 — kasutusjuhendi nõuded

## 3. Funktsionaalsed nõuded

| ID | Nõue | Prioriteet |
|---|---|---|
| FR-01 | Süsteem kuvab kursuste nimekirja hinna järgi sorteerituna. | Kõrge |
| FR-02 | Kursuseid saab filtreerida kategooria järgi (A, B, C, D). | Kõrge |
| FR-03 | Kursuse detailvaade näitab kirjeldust, tundide arvu, hinda, alguskuupäeva, instruktorit ja arvustusi. | Kõrge |
| FR-04 | Külastaja saab registreerida kontot nime, e-posti, telefoni ja parooliga. | Kõrge |
| FR-05 | Registreeritud kasutaja saab sisse logida ja välja logida. | Kõrge |
| FR-06 | Sisseloginud kasutaja saab registreeruda kursusele. | Kõrge |
| FR-07 | Kasutaja ei saa registreeruda samale kursusele topelt (v.a tühistatud registreering). | Kõrge |
| FR-08 | Kasutaja näeb oma registreeringuid ja nende staatust. | Keskmine |
| FR-09 | Administraator näeb kõiki registreeringuid ja saab neid kinnitada või tühistada. | Kõrge |
| FR-10 | Kasutajaliides on saadaval vene ja eesti keeles, keelt saab vahetada igal lehel. | Kõrge |
| FR-11 | Ligipääs administreerimisliidesele on ainult rolliga `admin`. | Kõrge |
| FR-12 | Kõik vormid on kaitstud CSRF-tokeniga. | Kõrge |

## 4. Mittefunktsionaalsed nõuded

| ID | Nõue |
|---|---|
| NFR-01 | Kujundus on adaptiivne: sisu on loetav ja navigatsioon kasutatav alates 360 px laiusest. |
| NFR-02 | Animatsioonid järgivad kasutaja eelistust vähendatud liikumise kohta (`prefers-reduced-motion`). |
| NFR-03 | Paroolid salvestatakse räsina (`password_hash`), ilma selgtekstita. |
| NFR-04 | Kõik kasutaja sisestatud andmed väljastatakse escapatuna (XSS-kaitse). |
| NFR-05 | Süsteem käivitub Docker Compose abil ühe käsuga. |
| NFR-06 | Andmebaas sisaldab näidisandmeid, mis vastavad automobiilikooli teemale. |

## 5. Liidesed
- Veebilehitseja: HTTP, port 8080.
- Andmebaas: MySQL 8.0, port 3307 (arendus).

## 6. Piirangud
- PHP 8.2, Apache, MySQL 8.0.
- Tehnilised eeldused: Docker Desktop.

## 7. Aktsepteerimiskriteeriumid
1. `docker compose up -d --build` käivitab lehe aadressil http://localhost:8080 ilma vigadeta.
2. Kursuste nimekiri ja kategooriafilter töötavad.
3. Uus kasutaja saab registreeruda ja kursusele kirjuda.
4. Topelt registreerimine annab teate, et kasutaja on juba registreeritud.
5. Admin kinnitab registreeringu ja staatus muutub kasutaja lehel.
6. Keele vahetus ru/et muudab kõik kasutajale nähtavad tekstid.
