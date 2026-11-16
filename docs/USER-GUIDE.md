# Autokool — kasutusjuhend (ISO 26514)

## 1. Sissejuhatus
Autokooli veebileht aitab valida sõidukategooria kursuse, registreeruda kursusele ja jälgida oma registreeringuid.

## 2. Alustamine
1. Ava leht http://localhost:8080.
2. Vali keel paremas ülanurgas: **Русский** või **Eesti**.
3. Kasutaja loomiseks vajuta **Регистрация** / **Registreeru**.

## 3. Kursuse valimine
1. Avalehel vali kategooria nupuga (A, B, C, D) või jäta **Все категории** / **Kõik kategooriad**.
2. Vaata kursuse hinda, tundide arvu ja alguskuupäeva.
3. Vajuta kursuse kaardil **Записаться** / **Registreeru kursusele**.

## 4. Registreerimine kursusele
1. Logi sisse.
2. Ava kursuse detailvaade.
3. Vajuta **Записаться** / **Registreeru kursusele**.
4. Kuvatakse teade: registreering on tehtud.
5. Kui oled juba registreeritud, kuvatakse teade, et see kursus on juba sinu nimel.

## 5. Minu registreeringud
Menüüs vali **Мои записи** / **Minu registreeringud**. Seal näed registreeringu kuupäeva ja staatust:
- **Новая / Uus** — ootab kinnitust,
- **Подтверждена / Kinnitatud** — registreering on kinnitatud,
- **Отменена / Tühistatud** — registreering on tühistatud.

## 6. Administraatori töö
1. Logi sisse administraatorina.
2. Menüüs vali **Админка** / **Admin**.
3. Registreeringu kõrval vajuta **Подтвердить** / **Kinnita** või **Отменить** / **Tühista**.

## 7. Tõrkeotsing

| Probleem | Lahendus |
|---|---|
| Leht ei avane | Kontrolli, et `docker compose up` on käivitatud ja port 8080 on vaba. |
| Registreerimine ei õnnestu | Kontrolli, et parool on vähemalt 6 märki ja e-post on õige. |
| Sisselogimine ei õnnestu | Kontrolli e-posti ja parooli. Vale andmeid ei kuvata eraldi, et kaitsta kontosid. |
| Tekst on vales keeles | Vali keel lehe ülaservas. |
