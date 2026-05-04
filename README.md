# RegOFIS Test Tizimi (Edu RegOFIS Integration)

Bu loyiha `edu.regofis.uz` ta'lim platformasi bilan API orqali sinxronizatsiya qilib ishlaydigan, talabalar arizalari, o'quv jarayonlari va imtihonlarni boshqarishga mo'ljallangan Laravel veb-ilovasidir.

Tizim oliy ta'lim muassasalari (xususan, kredit-modul tizimi) dagi jarayonlarni raqamlashtirish, imtihon natijalarini tahlil qilish va statistik hisobotlarni qulay tarzda shakllantirish uchun xizmat qiladi.

## 🚀 Asosiy imkoniyatlar

* **API Integratsiya:** `edu.regofis.uz` tizimidan talabalar va arizalar ma'lumotlarini xavfsiz tortib olish (token orqali).
* **Arizalarni boshqarish:** Talabalarning qayta o'qish (retrain) va imtihon topshirish uchun yuborgan arizalarini ko'rib chiqish va tasdiqlash.
* **Imtihon jarayoni:** Fanlar, semestrlar, kafedralar va guruhlar kesimida avtomatik imtihon ro'yxatlarini shakllantirish.
* **Excel Hisobotlar:** Maatwebsite Excel paketi yordamida yakuniy qaydnomalar, bo'sh fanlar va kafedra resurslari hisobotlarini yuklab olish imkoniyati.
* **Foydalanuvchi rollari:** Kafedra, o'qituvchi va administratorlar uchun maxsus huquqlar.

## 🛠️ Ishlatilgan texnologiyalar

* **Backend:** PHP 8.x, Laravel Framework
* **Ma'lumotlar bazasi:** MySQL / MariaDB
* **Frontend:** Blade Templates, Bootstrap, AdminLTE
* **Eksport:** Maatwebsite/Laravel-Excel

## ⚙️ O'rnatish va ishga tushirish (Installation)

Loyiha kompyuteringizda ishlashi uchun quyidagi qadamlarni bajaring:

1. Loyihani yuklab oling:

```bash
   git clone [https://github.com/muxammedqudaynazarov/test_regofis_uz.git](https://github.com/muxammedqudaynazarov/test_regofis_uz.git)
   cd test_regofis_uz
