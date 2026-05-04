# 📘 RegOFIS Test Tizimi (Edu RegOFIS Integration)

**RegOFIS Test Tizimi** — bu `edu.regofis.uz` ta'lim platformasi bilan API orqali integratsiya qilinadigan Laravel asosidagi veb-ilova bo‘lib, talabalar arizalari, imtihon jarayonlari va o‘quv boshqaruvini avtomatlashtirish uchun ishlab chiqilgan.

Ushbu tizim oliy ta'lim muassasalarida (ayniqsa kredit-modul tizimida) quyidagi jarayonlarni raqamlashtirishga xizmat qiladi:

- Talabalar arizalarini boshqarish  
- Imtihon jarayonlarini tashkil etish  
- Natijalarni monitoring qilish  
- Statistik hisobotlar shakllantirish  

---

## 🚀 Asosiy imkoniyatlar

### 🔗 API Integratsiya
- `edu.regofis.uz` bilan token orqali xavfsiz ulanish
- Talabalar va arizalar ma’lumotlarini avtomatik sinxronlash

### 📄 Arizalarni boshqarish
- Qayta o‘qish (retrain) arizalari
- Imtihon topshirish arizalari
- Arizalarni tasdiqlash / rad etish

### 🧑‍🎓 Imtihon tizimi
- Fanlar kesimida imtihon ro‘yxatlari
- Semestr, guruh va kafedra bo‘yicha filtrlar
- Avtomatik ro‘yxat shakllantirish

### 📊 Hisobotlar (Excel eksport)
- Yakuniy qaydnomalar
- Bo‘sh fanlar ro‘yxati
- Kafedra kesimidagi yuklamalar
- Excel formatida yuklab olish (Laravel Excel orqali)

### 👥 Foydalanuvchi rollari
- **Administrator**
- **Kafedra**
- **O‘qituvchi**

---

## 🛠️ Texnologiyalar

| Qatlam   | Texnologiya                    |
|----------|------------------------------|
| Backend  | PHP 8.x, Laravel             |
| Frontend | Blade, Bootstrap, AdminLTE   |
| Database | MySQL / MariaDB              |
| API      | REST (Token-based)           |
| Export   | Maatwebsite/Laravel-Excel    |

---

## 📦 O‘rnatish (Installation)

### 1. Loyihani klonlash

```bash
git clone https://github.com/muxammedqudaynazarov/test_regofis_uz.git
cd test_regofis_uz
```

### 2. Kutubxonalarni o‘rnatish

```bash
composer install
```

### 3. `.env` faylini yaratish

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Ma’lumotlar bazasini sozlash

`.env` faylida:

```env
DB_DATABASE=regofis
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migratsiya va seed

```bash
php artisan migrate --seed
```

### 6. Serverni ishga tushirish

```bash
php artisan serve
```

Brauzerda oching:

```
http://127.0.0.1:8000
```

---

## 🔑 API sozlamalari

Tizim ishlashi uchun `edu.regofis.uz` API token kerak.

`.env` faylga qo‘shing:

```env
REGOFIS_TOKEN=your_secret_token_here
REGOFIS_API_URL=https://edu.regofis.uz/api
```

---

## 📁 Loyihaning tuzilishi

```
app/
 ├── Http/
 │    ├── Controllers/
 │    ├── Middleware/
 ├── Models/
 ├── Services/

database/
 ├── migrations/
 ├── seeders/

resources/
 ├── views/

routes/
 ├── web.php
```

---

## 🔐 Xavfsizlik

- API token `.env` faylda saqlanadi  
- CSRF himoya mavjud  
- Role-based access control ishlatiladi  

---

## 🧪 Testlash

```bash
php artisan test
```

---

## 🤝 Hissa qo‘shish (Contributing)

1. Fork qiling  
2. Yangi branch oching (`feature/your-feature`)  
3. O‘zgartirish kiriting  
4. Commit qiling  
5. Pull Request yuboring  

---

## 🐞 Muammolar (Issues)

Agar xatolik topsangiz yoki taklif bo‘lsa, GitHub Issues orqali yozing.

---

## 📜 Litsenziya

Ushbu loyiha MIT litsenziya asosida tarqatiladi.

---

## 👨‍💻 Muallif

**Mukhammed Qudaynazarov**

- GitHub: https://github.com/muxammedqudaynazarov  
- Hudud: O‘zbekiston  
