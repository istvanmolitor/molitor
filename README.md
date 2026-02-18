<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Molitor CMS

Moduláris Laravel + Vue 3 alkalmazás TypeScript-tel, több package-ben szervezve.

## 🏗️ Architektúra

Ez az alkalmazás **moduláris architektúrát** használ:
- **Backend**: Laravel API végpontok a `/packages` mappában
- **Frontend**: Vue 3 + TypeScript modulok a `/resources/js/vue-packages` mappában

### Packages
- **user** - Felhasználók, autentikáció, jogosultságok
- **cms** - Tartalom kezelés (oldalak, szerzők, régiók)
- **media** - Média fájl kezelés
- **admin** - Admin beállítások és UI komponensek
- **menu** - Menü kezelés
- **rss-watcher** - RSS feed figyelő
- **theme** - Téma beállítások

## 📚 Dokumentáció

- **[FRONTEND_BACKEND_ARCHITECTURE.md](FRONTEND_BACKEND_ARCHITECTURE.md)** - Teljes architektúra leírás
- **[SETUP_COMPLETE.md](SETUP_COMPLETE.md)** - Setup összefoglaló és következő lépések
- **[CHECKLIST.md](CHECKLIST.md)** - Ellenőrző lista

## 🚀 Gyors Kezdés

### Telepítés

```bash
# Backend függőségek
composer install

# Frontend függőségek
npm install

# Környezeti változók
cp .env.example .env
php artisan key:generate

# Adatbázis
php artisan migrate

# Frontend build
npm run build
```

### Fejlesztés

```bash
# Mindent egyben indít (server, queue, logs, vite):
composer dev

# VAGY külön terminálokban:
php artisan serve    # Backend
npm run dev          # Frontend
```

Nyisd meg: **http://localhost:8000**

## 🔧 Technológiák

- **Backend**: Laravel 12, PHP 8.2+, SQLite/MySQL
- **Frontend**: Vue 3, TypeScript, Vite
- **Auth**: Laravel Sanctum
- **Styling**: Tailwind CSS 4
- **Icons**: Lucide Vue

## 📁 Projekt Struktúra

```
├── packages/              # Backend packages
│   ├── user/
│   ├── cms/
│   ├── media/
│   └── ...
├── resources/
│   └── js/
│       ├── router/        # Központi Vue router
│       └── vue-packages/  # Frontend packages
│           ├── vue-user/
│           ├── vue-cms/
│           └── ...
├── routes/
│   └── web.php           # Catch-all Vue app
└── public/
    └── build/            # Built assets
```

## 🎯 API Végpontok

- `POST /api/auth/login` - Bejelentkezés
- `GET /api/auth/me` - Aktuális felhasználó
- `GET|POST /api/admin/user/users` - Felhasználók
- `GET|POST /api/cms/pages` - CMS oldalak
- `GET|POST /api/media/*` - Média fájlok
- `GET|POST /api/rss-feeds` - RSS feedek

Részletek: [FRONTEND_BACKEND_ARCHITECTURE.md](FRONTEND_BACKEND_ARCHITECTURE.md)

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
