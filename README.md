# Binocor

Qurilish va ko'chmas mulk kompaniyalari uchun sotuv (CRM) va qurilish jarayonini boshqarish tizimi.

## Muammo

Ko'p qurilish kompaniyalari hozir Excel jadvallar, WhatsApp guruhlar va qog'oz hujjatlar orqali ishlaydi — bu esa sotuv holatini kuzatishda, to'lov jadvalini yuritishda va qurilish jarayonini nazorat qilishda xatoliklarga olib keladi. Binocor barcha ma'lumotni bitta markazlashgan tizimda, rolga qarab cheklangan ko'rinishda birlashtiradi.

## Modullar

- **Loyihalar va obyektlar** — qurilish loyihalari va ularning tarkibidagi obyektlar (kvartira/ofis/uchastka) katalogi
- **Mijozlar** — lead bosqichlaridan (qiziqdi → ko'rdi → band qildi → shartnoma tuzdi) tortib to shartnomagacha
- **Shartnomalar** — naqd yoki rassrochka, avtomatik to'lov jadvali generatsiyasi bilan
- **To'lovlar** — to'lov holati kuzatuvi, kechikkan to'lovlar uchun avtomatik bildirishnoma
- **Qurilish jarayoni** — bosqichlar, progress foizi, foto hisobotlar
- **Hisobotlar** — oylik sotuv/to'lov dinamikasi, obyektlar holati taqsimoti

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
