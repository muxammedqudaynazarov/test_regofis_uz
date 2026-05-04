# RegOFIS Test Tizimi (Edu RegOFIS Integration)

RegOFIS Test Tizimi — bu edu.regofis.uz ta'lim platformasi bilan API orqali integratsiya qilinadigan Laravel asosidagi veb-ilova bo‘lib, talabalar arizalari, imtihon jarayonlari va o‘quv boshqaruvini avtomatlashtirish uchun ishlab chiqilgan.

Ushbu tizim oliy ta'lim muassasalarida (ayniqsa kredit-modul tizimida) quyidagi jarayonlarni raqamlashtirishga xizmat qiladi:

* Talabalar arizalarini boshqarish
* Imtihon jarayonlarini tashkil etish
* Natijalarni monitoring qilish
* Statistik hisobotlar shakllantirish

---

ASOSIY IMKONIYATLAR

API Integratsiya:

* edu.regofis.uz bilan token orqali xavfsiz ulanish
* Talabalar va arizalar ma’lumotlarini avtomatik sinxronlash

Arizalarni boshqarish:

* Qayta o‘qish (retrain) arizalari
* Imtihon topshirish arizalari
* Arizalarni tasdiqlash / rad etish

Imtihon tizimi:

* Fanlar kesimida imtihon ro‘yxatlari
* Semestr, guruh va kafedra bo‘yicha filtrlar
* Avtomatik ro‘yxat shakllantirish

Hisobotlar (Excel eksport):

* Yakuniy qaydnomalar
* Bo‘sh fanlar ro‘yxati
* Kafedra kesimidagi yuklamalar
* Excel formatida yuklab olish

Foydalanuvchi rollari:

* Administrator
* Kafedra
* O‘qituvchi

---

TEXNOLOGIYALAR

Backend: PHP 8.x, Laravel
Frontend: Blade, Bootstrap, AdminLTE
Database: MySQL / MariaDB
API: REST (Token-based authentication)
Export: Laravel Excel

---

O‘RNATISH (INSTALLATION)

1. Loyihani yuklab oling:

git clone https://github.com/muxammedqudaynazarov/test_regofis_uz.git
cd test_regofis_uz

2. Kutubxonalarni o‘rnating:

composer install

3. .env faylini yarating:

cp .env.example .env
php artisan key:generate

4. Ma’lumotlar bazasini sozlang (.env ichida):

DB_DATABASE=regofis
DB_USERNAME=root
DB_PASSWORD=

5. Migratsiya va seed:

php artisan migrate --seed

6. Serverni ishga tushiring:

php artisan serve

Brauzerda oching:
http://127.0.0.1:8000

---

API SOZLAMALARI

.env faylga quyidagini qo‘shing:

REGOFIS_TOKEN=your_secret_token_here
REGOFIS_API_URL=https://edu.regofis.uz/api

---

LOYIHA TUZILISHI

app/
Http/
Controllers/
Middleware/
Models/
Services/

database/
migrations/
seeders/

resources/
views/

routes/
web.php

---

XAVFSIZLIK

* API token .env faylda saqlanadi
* CSRF himoya mavjud
* Role-based access control ishlatiladi

---

TESTLASH

php artisan test

---

HISSA QO‘SHISH (CONTRIBUTING)

1. Fork qiling
2. Yangi branch oching (feature/your-feature)
3. O‘zgartirish kiriting
4. Commit qiling
5. Pull Request yuboring

---

MUAMMOLAR (ISSUES)

Agar xatolik topsangiz yoki taklif bo‘lsa, GitHub Issues orqali yozing.

---

LITSENZIYA

Ushbu loyiha MIT litsenziya asosida tarqatiladi.

---

MUALLIF

Mukhammed Qudaynazarov
GitHub: https://github.com/muxammedqudaynazarov
Hudud: O‘zbekiston
