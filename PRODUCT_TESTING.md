# Product CRUD - Tesztelési útmutató

## Előkészületek

### 1. Composer és autoload frissítés
```bash
vendor/bin/sail composer dump-autoload
```

### 2. Migrációk futtatása (ha még nem történt meg)
```bash
vendor/bin/sail artisan migrate
```

### 3. Seederek futtatása
```bash
# Termékek, kategóriák és mértékegységek feltöltése
vendor/bin/sail artisan db:seed --class=Molitor\\Product\\database\\seeders\\ProductSeeder
```

### 4. Frontend build
```bash
# Production build
vendor/bin/sail npm run build

# VAGY development módban:
vendor/bin/sail npm run dev
```

## Tesztelési lépések

### Backend API tesztelés

#### 1. Útvonalak ellenőrzése
```bash
vendor/bin/sail artisan route:list | grep product
```

Várt eredmény:
```
GET|HEAD  api/admin/product/products ............... product.products.index
POST      api/admin/product/products ............... product.products.store
GET|HEAD  api/admin/product/products/create ........ product.products.create
GET|HEAD  api/admin/product/products/{product} ..... product.products.show
PUT|PATCH api/admin/product/products/{product} ..... product.products.update
DELETE    api/admin/product/products/{product} ..... product.products.destroy
GET|HEAD  api/admin/product/products/{product}/edit  product.products.edit
```

#### 2. Service Provider regisztráció ellenőrzése
```bash
vendor/bin/sail artisan config:clear
vendor/bin/sail artisan package:discover
```

### Frontend tesztelés böngészőben

#### 1. Bejelentkezés
- Nyisd meg az alkalmazást: http://localhost
- Jelentkezz be admin felhasználóval

#### 2. Termékek menüpont
- ✅ Ellenőrizd, hogy megjelenik-e a "Termékek" menüpont a bal oldali menüben
- ✅ Kattints a menüpontra

#### 3. Termékek lista (ProductIndex)
- ✅ Betöltődik a termékek listája
- ✅ Látható az adattábla a termékekkel
- ✅ Működik a keresés (SKU vagy név alapján)
- ✅ Működik a rendezés (oszlopokra kattintva)
- ✅ Működik a lapozás
- ✅ Megjelenik az "Új termék" gomb

#### 4. Új termék létrehozása (ProductCreate)
- ✅ Kattints az "Új termék" gombra
- ✅ Betöltődik a létrehozó űrlap
- ✅ Megjelennek a mértékegységek a dropdown-ban
- ✅ Töltsd ki a mezőket:
  - SKU: `TEST-001`
  - Slug: `test-termek`
  - Ár: `1000`
  - Válassz mértékegységet
  - Pipáld be az "Aktív" jelölőnégyzetet
- ✅ Kattints a "Mentés" gombra
- ✅ Megjelenik a sikeres mentés toast üzenet
- ✅ Visszairányít a lista oldalra
- ✅ Az új termék megjelenik a listában

#### 5. Termék szerkesztése (ProductEdit)
- ✅ Kattints egy termék melletti "Szerkesztés" ikonra
- ✅ Betöltődik a szerkesztő űrlap a termék adataival
- ✅ Módosítsd az adatokat (pl. ár: `1500`)
- ✅ Kattints a "Mentés" gombra
- ✅ Megjelenik a sikeres frissítés toast üzenet
- ✅ Visszairányít a lista oldalra
- ✅ A módosított adatok megjelennek

#### 6. Termék törlése
- ✅ Kattints egy termék melletti "Törlés" ikonra
- ✅ Megjelenik a megerősítő dialog
- ✅ Erősítsd meg a törlést
- ✅ Megjelenik a sikeres törlés toast üzenet
- ✅ A termék eltűnik a listából

#### 7. Validációs hibák tesztelése
- ✅ Próbálj meg új terméket létrehozni üres SKU-val
- ✅ Megjelennek a validációs hibák
- ✅ Próbálj meg duplikált SKU-t használni
- ✅ Megjelenik a hibüzenet

## Gyakori problémák és megoldások

### Ha a termékek menüpont nem jelenik meg
```bash
# Tisztítsd meg a cache-t és build újra
vendor/bin/sail npm run build
# Vagy újraindítás development módban
vendor/bin/sail npm run dev
```

### Ha API hibát kapsz
```bash
# Ellenőrizd a log fájlokat
vendor/bin/sail artisan pail

# Vagy
tail -f storage/logs/laravel.log
```

### Ha "undefined" hibát látsz a konzolon
- Ellenőrizd, hogy a path alias-ek helyesen vannak-e konfigurálva
- Újraindítás: `Ctrl+C` és `vendor/bin/sail npm run dev`

## Sikerkritériumok

✅ A termékek menüpont látható és működik
✅ A termékek listája betöltődik és működik a szűrés/rendezés
✅ Új termék sikeresen létrehozható
✅ Létező termék szerkeszthető
✅ Termék törölhető
✅ Minden művelet után megfelelő toast üzenet jelenik meg
✅ A validációs hibák megjelennek és kezelhetőek
✅ A mértékegységek betöltődnek és választhatóak

## További tesztelendő funkciók (opcionális)

- Tesztelés különböző böngészőkben (Chrome, Firefox, Safari)
- Responsive design ellenőrzése mobilon
- Hálózati hibák kezelése (pl. disconnect esetén)
- Több termék egyidejű kezelése
- Nagy mennyiségű adat esetén a teljesítmény

