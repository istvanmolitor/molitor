# Molitor Project Structure Guide

Ez a dokumentum a Molitor projekt hivatalos struktúra konvencióit tartalmazza. Minden AI asszisztensnek (GitHub Copilot, Cursor, Junie, stb.) és fejlesztőnek követnie kell ezeket az irányelveket.

## 🎯 Alapvető Szabályok

### 1. Backend (Laravel/PHP)
**📍 Hely:** `/packages/`

Minden Laravel backend kód a `/packages/` mappában található. Ne hozz létre Laravel kódot máshol!

#### Struktúra:
```
packages/
├── user/                    # Felhasználó kezelés
│   ├── src/
│   │   ├── Controllers/     # HTTP controllers
│   │   ├── Models/          # Eloquent models
│   │   ├── Services/        # Business logic
│   │   ├── Providers/       # Service providers
│   │   └── routes.php       # Package routes
│   ├── resources/
│   │   ├── views/           # Blade views
│   │   └── config/          # Config files
│   ├── tests/
│   │   ├── Unit/
│   │   └── Feature/
│   └── composer.json
│
├── cms/                     # Content Management System
├── media/                   # Media management
├── admin/                   # Admin functionality
└── ...
```

#### Példák:
- ✅ Helyes: `/packages/user/src/Controllers/UserController.php`
- ✅ Helyes: `/packages/cms/src/Services/PageService.php`
- ❌ Helytelen: `/app/Controllers/UserController.php`

### 2. Frontend (Vue.js/TypeScript)
**📍 Hely:** `/resources/js/vue-packages/`

Minden Vue komponens és TypeScript kód a `/resources/js/vue-packages/` mappában található. Ne hozz létre Vue/TS kódot máshol!

#### Struktúra:
```
resources/js/vue-packages/
├── vue-user/                # User-related frontend
│   ├── components/          # Reusable components
│   │   ├── UserCard.vue
│   │   └── UserList.vue
│   ├── views/               # Page-level components
│   │   ├── auth/
│   │   │   ├── Login.vue
│   │   │   └── Register.vue
│   │   └── Profile.vue
│   ├── services/            # API calls
│   │   ├── authService.ts
│   │   └── userService.ts
│   ├── types/               # TypeScript types
│   │   └── user.ts
│   ├── stores/              # Pinia stores
│   │   └── userStore.ts
│   └── composables/         # Vue composables
│       └── useAuth.ts
│
├── vue-admin/               # Admin panel UI
│   ├── components/
│   │   └── ui/             # UI components (Button, Card, etc.)
│   └── layouts/
│
├── vue-cms/                 # CMS frontend
└── ...
```

#### Példák:
- ✅ Helyes: `/resources/js/vue-packages/vue-user/views/auth/Login.vue`
- ✅ Helyes: `/resources/js/vue-packages/vue-admin/components/ui/Button.vue`
- ❌ Helytelen: `/resources/js/components/Login.vue`
- ❌ Helytelen: `/app/vue/Login.vue`

## 📋 Fejlesztési Munkafolyamat

### Új Feature Létrehozása

#### Backend Feature:
1. Navigálj a megfelelő package-hez: `/packages/[package-name]/`
2. Controller: `/packages/[package-name]/src/Controllers/`
3. Model: `/packages/[package-name]/src/Models/`
4. Service: `/packages/[package-name]/src/Services/`
5. Route: `/packages/[package-name]/src/routes.php` vagy `/routes/web.php`
6. Test: `/packages/[package-name]/tests/`

#### Frontend Feature:
1. Navigálj a megfelelő package-hez: `/resources/js/vue-packages/[package-name]/`
2. Component: `/resources/js/vue-packages/[package-name]/components/`
3. View: `/resources/js/vue-packages/[package-name]/views/`
4. Service: `/resources/js/vue-packages/[package-name]/services/`
5. Type: `/resources/js/vue-packages/[package-name]/types/`
6. Store: `/resources/js/vue-packages/[package-name]/stores/`

### Példa: Login Feature

**Backend (Laravel):**
```
/packages/user/src/
├── Controllers/
│   └── AuthController.php          # login, logout, register
├── Services/
│   └── AuthService.php             # business logic
└── routes.php                       # POST /api/auth/login
```

**Frontend (Vue):**
```
/resources/js/vue-packages/vue-user/
├── views/auth/
│   ├── Login.vue                   # Login page component
│   └── Register.vue                # Register page component
├── services/
│   └── authService.ts              # API calls (login, logout)
├── types/
│   └── auth.ts                     # LoginCredentials, User types
└── stores/
    └── authStore.ts                # User state management
```

## 🔍 Csomag Példák

### Backend Csomagok
| Csomag | Hely | Célja |
|--------|------|-------|
| `user` | `/packages/user/` | Felhasználók, auth, permissions |
| `cms` | `/packages/cms/` | Tartalom kezelés (pages, authors) |
| `media` | `/packages/media/` | Média fájl feltöltés/kezelés |
| `admin` | `/packages/admin/` | Admin settings |
| `article-parser` | `/packages/article-parser/` | Article parsing logic |
| `html-parser` | `/packages/html-parser/` | HTML parsing utilities |
| `rss-watcher` | `/packages/rss-watcher/` | RSS feed monitoring |

### Frontend Csomagok
| Csomag | Hely | Célja |
|--------|------|-------|
| `vue-user` | `/resources/js/vue-packages/vue-user/` | User UI, auth views |
| `vue-admin` | `/resources/js/vue-packages/vue-admin/` | Admin panel UI, shared components |
| `vue-cms` | `/resources/js/vue-packages/vue-cms/` | CMS frontend |

## 🛠️ Technológiai Stack

### Backend
- **Framework:** Laravel 12
- **PHP:** 8.2+
- **Database:** SQLite (dev), MySQL (prod)
- **Auth:** Laravel Sanctum (token-based)
- **Testing:** PHPUnit

### Frontend
- **Framework:** Vue 3 (Composition API)
- **Language:** TypeScript (strict mode)
- **Build Tool:** Vite
- **Styling:** Tailwind CSS 4
- **Icons:** Lucide Vue
- **State:** Pinia
- **Router:** Vue Router
- **HTTP:** Axios

### DevOps
- **Container:** Docker (Laravel Sail)
- **Package Manager:** Composer (PHP), npm (JS)

## 📝 Kódolási Konvenciók

### PHP (Laravel)
- PSR-12 coding standard
- Type hints mindenhol
- Return type declarations
- Service layer pattern (Controllers → Services → Models)
```php
// ✅ Helyes
class UserService
{
    public function createUser(array $data): User
    {
        // ...
    }
}
```

### TypeScript (Vue)
- Strict mode enabled
- Explicit typing
- Composition API with `<script setup lang="ts">`
- PascalCase for components, camelCase for functions/variables
```typescript
// ✅ Helyes
interface User {
  id: number
  email: string
  name: string
}

const fetchUser = async (id: number): Promise<User> => {
  // ...
}
```

### Vue Components
```vue
<!-- ✅ Helyes -->
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { User } from '../types/user'

const user = ref<User | null>(null)

onMounted(async () => {
  // ...
})
</script>

<template>
  <div v-if="user">
    {{ user.name }}
  </div>
</template>
```

## 🚨 Gyakori Hibák és Megoldások

### ❌ HIBA: Laravel kód az `/app/` mappában
```
/app/Http/Controllers/UserController.php  ❌
```
**✅ HELYES:**
```
/packages/user/src/Controllers/UserController.php
```

### ❌ HIBA: Vue komponens a root `/resources/js/components/`-ben
```
/resources/js/components/Login.vue  ❌
```
**✅ HELYES:**
```
/resources/js/vue-packages/vue-user/views/auth/Login.vue
```

### ❌ HIBA: TypeScript service a rossz helyen
```
/resources/js/services/authService.ts  ❌
```
**✅ HELYES:**
```
/resources/js/vue-packages/vue-user/services/authService.ts
```

## 📚 További Dokumentáció

- **Teljes architektúra:** [FRONTEND_BACKEND_ARCHITECTURE.md](../FRONTEND_BACKEND_ARCHITECTURE.md)
- **Setup útmutató:** [SETUP_COMPLETE.md](../SETUP_COMPLETE.md)
- **API végpontok:** [README.md](../README.md#-api-végpontok)

## 🤖 AI Asszisztensek Számára

Ha AI asszisztens vagy (GitHub Copilot, Cursor, Junie, stb.):

1. **MINDIG** ellenőrizd, hogy a megfelelő helyen dolgozol
2. Backend kód → `/packages/`
3. Frontend kód → `/resources/js/vue-packages/`
4. Ne hozz létre fájlokat a szabványos Laravel helyeken (`/app/Http/Controllers/`, stb.)
5. Tartsd be a package struktúrát
6. Kérdezz vissza, ha bizonytalan vagy!

---

**Utolsó frissítés:** 2026-02-19
**Verzió:** 1.0.0

