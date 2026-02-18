# GitHub Copilot Instruciók

## ⚠️ FONTOS: Laravel Sail használata

Ez a projekt **Laravel Sail**-t használ Dockerrel. A Copilot-nak MINDIG a Sail parancsokat kell használnia!

### ✅ Helyes parancsok (Sail-lel):

```bash
# Artisan parancsok
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan tinker

# Composer
./vendor/bin/sail composer install
./vendor/bin/sail composer update

# NPM
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build

# Teszt
./vendor/bin/sail artisan test
./vendor/bin/sail phpunit

# Shell hozzáférés
./vendor/bin/sail shell
./vendor/bin/sail root-shell

# Konténerek kezelése
./vendor/bin/sail up -d      # Elindítás
./vendor/bin/sail down        # Leállítás
./vendor/bin/sail restart     # Újraindítás
./vendor/bin/sail ps          # Státusz

# Adatbázis
./vendor/bin/sail mysql       # MySQL shell
```

### ❌ NE használd ezeket közvetlenül:

```bash
# TILOS - ezek nem fognak működni, mert nem a Dockerben futnak!
php artisan ...
composer ...
npm ...
mysql ...
```

### 🔐 Admin hozzáférés

- **Email:** admin@example.com
- **Jelszó:** admin

Az admin felhasználó a `UserSeeder` által kerül létrehozásra a `packages/user/src/Database/Seeders/UserSeeder.php` fájlban.

### 🗄️ Adatbázis

- MySQL konténerben fut
- Elérhető: `DB_HOST=mysql` (Docker network)
- Külső port: 3306 (ha szükséges külső hozzáférés)

### 🚀 Projekt indítása

1. Konténerek elindítása:
   ```bash
   ./vendor/bin/sail up -d
   ```

2. Ha szükséges, storage jogosultságok beállítása:
   ```bash
   ./vendor/bin/sail exec laravel.test chmod -R 777 storage bootstrap/cache
   ```

3. Migrációk futtatása (ha szükséges):
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

4. Adatbázis seed-elése (admin user létrehozása):
   ```bash
   ./vendor/bin/sail artisan db:seed
   ```

5. Frontend build:
   ```bash
   ./vendor/bin/sail npm run dev
   ```

6. Alkalmazás elérhető: **http://localhost** (port 80)
   - API endpoint: http://localhost/api/...
   - Frontend: http://localhost/

### 📝 Megjegyzések

- A projekt mindig Dockerben fut
- Az .env fájl Sail-hez van konfigurálva (`DB_HOST=mysql`)
- Ne próbálj meg közvetlenül PHP/Composer parancsokat futtatni
- Ha a konténerek nem futnak, először indítsd el őket: `./vendor/bin/sail up -d`

### 🔍 Hibakeresés

Ha valami nem működik, ellenőrizd:
1. Futnak-e a konténerek: `./vendor/bin/sail ps`
2. Nézd meg a logokat: `./vendor/bin/sail logs`
3. MySQL él-e: `./vendor/bin/sail mysql -e "SELECT 1"`

