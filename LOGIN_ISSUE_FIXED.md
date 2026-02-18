# Login Probléma Megoldása

## 🔍 Probléma

Nem lehetett bejelentkezni az `admin@example.com` felhasználóval és `admin` jelszóval.

## ✅ Megoldás

### 1. Laravel Sail elindítása

A projekt Docker konténerekben fut Laravel Sail segítségével:

```bash
./vendor/bin/sail up -d
```

### 2. Storage jogosultságok beállítása

```bash
./vendor/bin/sail exec laravel.test chmod -R 777 storage bootstrap/cache
```

### 3. Admin felhasználó létrehozása

Az admin felhasználó a `UserSeeder`-rel került létrehozásra:

```bash
./vendor/bin/sail artisan tinker --execute="
\$user = \Molitor\User\Models\User::where('email', 'admin@example.com')->first();
if (!\$user) {
    \$service = app(\Molitor\User\Services\AclManagementService::class);
    \$user = \$service->createUser('admin@example.com', 'admin', 'admin', ['admin', 'user']);
    \$user->markEmailAsVerified();
}
"
```

### 4. Laravel Sanctum beállítása

A `User` modellhez hozzá kellett adni a `HasApiTokens` traitet:

**app/Models/User.php**
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    // ...
}
```

### 5. Sanctum migráció futtatása

```bash
./vendor/bin/sail artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
./vendor/bin/sail artisan migrate
```

## 🎯 Eredmény

✅ Admin felhasználó létrejött
✅ Bejelentkezés működik az API-n keresztül
✅ Sanctum tokenek generálódnak
✅ Az alkalmazás elérhető: http://localhost

## 🔐 Admin Hozzáférés

- **Email:** admin@example.com
- **Jelszó:** admin
- **Csoportok:** admin, user
- **Jogosultságok:** minden (admin csoport)

## 📝 API Végpont

```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "admin"
}
```

Válasz:
```json
{
  "data": {
    "user": { ... },
    "token": "1|..."
  }
}
```

## 🚀 Használat

1. Konténerek elindítása:
   ```bash
   ./vendor/bin/sail up -d
   ```

2. Frontend build (fejlesztéshez):
   ```bash
   ./vendor/bin/sail npm run dev
   ```

3. Alkalmazás megnyitása böngészőben:
   ```
   http://localhost
   ```

4. Bejelentkezés:
   - Email: admin@example.com
   - Jelszó: admin

## ⚠️ Fontos

- **MINDIG** használj `./vendor/bin/sail` előtagot a parancsokhoz!
- Ne futtass közvetlenül `php artisan` vagy `composer` parancsokat
- Az adatbázis a MySQL konténerben fut (`DB_HOST=mysql`)

## 📚 További információ

- [COPILOT_INSTRUCTIONS.md](COPILOT_INSTRUCTIONS.md) - GitHub Copilot instruciók
- [README.md](README.md) - Projekt dokumentáció

