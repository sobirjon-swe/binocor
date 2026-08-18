# Binocor

Qurilish va ko'chmas mulk kompaniyalari uchun sotuv (CRM) va qurilish jarayonini boshqarish tizimi.

## Muammo

Ko'p qurilish kompaniyalari hozir Excel jadvallar, WhatsApp guruhlar va qog'oz hujjatlar orqali ishlaydi — bu esa sotuv holatini kuzatishda, to'lov jadvalini yuritishda va qurilish jarayonini nazorat qilishda xatoliklarga olib keladi. Binocor barcha ma'lumotni bitta markazlashgan tizimda, rolga qarab cheklangan ko'rinishda birlashtiradi.

## Modullar

- **Loyihalar va obyektlar** — qurilish loyihalari va ularning tarkibidagi obyektlar (kvartira/ofis/uchastka) katalogi, har biri uchun rasm galereyasi bilan
- **Mijozlar** — lead bosqichlaridan (qiziqdi → ko'rdi → band qildi → shartnoma tuzdi) tortib to shartnomagacha
- **Shartnomalar** — naqd yoki rassrochka, avtomatik to'lov jadvali generatsiyasi bilan
- **To'lovlar** — to'lov holati kuzatuvi, kechikkan to'lovlar uchun avtomatik email/SMS bildirishnoma, Payme va Click orqali onlayn to'lov havolasi
- **Qurilish jarayoni** — bosqichlar, progress foizi, foto hisobotlar
- **Hisobotlar** — oylik sotuv/to'lov dinamikasi, obyektlar holati taqsimoti, Excel'ga eksport
- **Global qidiruv** — mijoz, shartnoma va obyektni ism/telefon/loyiha nomi bo'yicha bitta joydan topish
- **Faoliyat tarixi** — shartnoma va to'lov narxi/holati o'zgarishlarini kim va qachon qilgani (admin/menejer uchun)

## Rollar

| Rol | Ko'radi |
|---|---|
| Admin | Hammasi, shu jumladan foydalanuvchilarni boshqarish |
| Menejer | Umumiy nazorat — sotuv, moliya, qurilish holati, hisobotlar |
| Sotuv bo'limi boshlig'i | Butun jamoaning mijozlari/shartnomalari |
| Sotuvchi | Faqat o'z mijozlari/shartnomalari, obyektlar katalogi |
| Buxgalter | Faqat to'lovlar moduli |
| Yurist | Mijozlar va shartnomalarni faqat ko'rish huquqi bilan |
| Bosh muhandis | Loyihalar, obyektlar va qurilish jarayoni |
| Prorab | Faqat qurilish jarayoni moduli |

## Texnologiyalar

- **Backend:** Laravel 13 (PHP)
- **Frontend:** Blade + Alpine.js + Tailwind CSS, Chart.js grafiklar uchun
- **Autentifikatsiya:** Laravel Breeze
- **Ruxsatlar:** Spatie Laravel-Permission
- **PDF:** barryvdh/laravel-dompdf
- **Ma'lumotlar bazasi:** SQLite (dev) / MySQL yoki PostgreSQL (production)

Tizim PWA (Progressive Web App) sifatida ham ishlaydi — telefon yoki kompyuterga ilova sifatida o'rnatish mumkin, oldin ochilgan sahifalar internet uzilganda ham ishlaydi.

## Tashqi integratsiyalar

- **SMS (Eskiz.uz)** — shartnoma imzolanganda va to'lov muddati o'tganda mijozga avtomatik SMS eslatma. `.env` da `ESKIZ_EMAIL`/`ESKIZ_PASSWORD` bo'sh qoldirilsa, xabar API'ga yuborilmaydi, o'rniga log fayliga yoziladi — bu dev muhitida haqiqiy hisobsiz ishlashni ta'minlaydi.
- **Payme / Click** — to'lovlar ro'yxatidagi har bir to'lanmagan to'lov uchun onlayn to'lov havolasi generatsiya qilinadi. Ishga tushirish uchun `.env` da `PAYME_MERCHANT_ID`/`PAYME_KEY` va `CLICK_SERVICE_ID`/`CLICK_MERCHANT_ID`/`CLICK_SECRET_KEY` to'ldiriladi, so'ng provayderning kabinetida webhook manzili sifatida `/webhooks/payme` va `/webhooks/click` ko'rsatiladi.

## O'rnatish

```bash
composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link

php artisan serve
```

Demo login ma'lumotlari (barchasi `password` paroli bilan):

- `admin@binocor.uz`
- `manager@binocor.uz`
- `sales.manager@binocor.uz`
- `sales@binocor.uz`
- `accountant@binocor.uz`
- `lawyer@binocor.uz`
- `chief.engineer@binocor.uz`
- `foreman@binocor.uz`

## Testlar

```bash
php artisan test
```

## CI/CD

`main` branchga har push va har pull request'da `.github/workflows/tests.yml` avtomatik testlarni ishga tushiradi (frontend build qilingandan so'ng, PHP 8.3/8.4/8.5 uchun).

Testlar muvaffaqiyatli o'tsa, `.github/workflows/deploy.yml` serverga SSH orqali deploy qiladi (`git pull` → `composer`/`npm` → `migrate` → `cache`). Ishga tushirish uchun repo sozlamalarida quyidagilarni kiritish kerak:

**Settings → Secrets and variables → Actions → Secrets:**

| Nomi | Tavsif |
|---|---|
| `DEPLOY_HOST` | Server IP yoki domeni |
| `DEPLOY_USER` | SSH foydalanuvchisi |
| `DEPLOY_SSH_KEY` | SSH maxfiy kaliti (deploy foydalanuvchisining) |
| `DEPLOY_PORT` | SSH porti (ixtiyoriy, sukut bo'yicha 22) |

**Settings → Secrets and variables → Actions → Variables:**

| Nomi | Tavsif |
|---|---|
| `DEPLOY_PATH` | Serverdagi loyiha manzili (ixtiyoriy, sukut bo'yicha `/var/www/binocor`) |

Bular kiritilmaguncha `Deploy` workflow xato bilan tugaydi — bu zararsiz, faqat sozlash tugallanmaganini bildiradi.

## Production'ga chiqarish

Serverni sozlashda quyidagilar bajarilishi kerak (bir martalik, qo'lda):

1. **`.env`** — `APP_ENV=production`, `APP_DEBUG=false`, yangi `APP_KEY` (`php artisan key:generate`), haqiqiy `APP_URL` (https).
2. **Ma'lumotlar bazasi** — production'da SQLite emas, MySQL yoki PostgreSQL (`DB_CONNECTION`, `DB_HOST`, va h.k.).
3. **Web server** — Nginx/Apache + PHP-FPM, SSL sertifikat (masalan Let's Encrypt).
4. **Scheduler (cron)** — kechikkan to'lov bildirishnomasi shunga bog'liq, bo'lmasa ishlamaydi:
   ```
   * * * * * cd /var/www/binocor && php artisan schedule:run >> /dev/null 2>&1
   ```
5. **Email** — `MAIL_MAILER=log` o'rniga haqiqiy SMTP/API provider (aks holda email bildirishnomalar hech kimga bormaydi; SMS alohida ishlaydi).
6. **Tashqi integratsiyalar** — yuqoridagi Eskiz/Payme/Click hisob ma'lumotlari.

Shundan keyin `.github/workflows/deploy.yml` uchun secret/variable'larni kiritib, `main`ga push qilish yetarli — qolganini CI/CD o'zi bajaradi.
