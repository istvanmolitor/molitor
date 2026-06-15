# Molitor – Fejlesztési konvenciók

## Projekt szerkezete

A projekt csomagokra épül. Minden funkcionális terület két csomagból áll:

- **Backend csomag** – `packages/{name}/` – Laravel PHP csomag
- **Frontend csomag** – `resources/js/packages/{vue-name}/` – Vue 3 + TypeScript csomag

A két csomag neve jellemzően összefügg: `user` ↔ `vue-user`, `cms` ↔ `vue-cms`.

---

## Backend csomag (`packages/{name}/`)

### Mappastruktúra

```
packages/{name}/
├── composer.json
├── config/
│   └── {name}.php
├── README.md
└── src/
    ├── Database/
    │   ├── Migrations/
    │   └── Seeders/
    ├── Events/
    ├── Exceptions/
    ├── Http/
    │   ├── Controllers/
    │   │   └── Api/
    │   ├── Middleware/
    │   ├── Requests/
    │   └── Resources/
    ├── Models/
    ├── Providers/
    │   └── {Name}ServiceProvider.php
    ├── Repositories/
    │   ├── {Entity}Repository.php
    │   └── {Entity}RepositoryInterface.php
    ├── routes/
    │   ├── api.php
    │   └── web.php
    └── Services/
```

### `composer.json`

```json
{
    "name": "istvanmolitor/{name}",
    "type": "library",
    "license": "MIT",
    "authors": [{ "name": "Molitor István", "email": "istvanmolitor@gmail.com" }],
    "minimum-stability": "dev",
    "require": { ... },
    "autoload": {
        "psr-4": { "Molitor\\{Name}\\": "src/" }
    },
    "extra": {
        "laravel": {
            "providers": ["Molitor\\{Name}\\Providers\\{Name}ServiceProvider"]
        }
    }
}
```

A csomagot a gyökér `composer.json` `"repositories"` szekciójában kell regisztrálni `"type": "path"` bejegyzéssel.

### ServiceProvider

A `{Name}ServiceProvider` feladata:

- `boot()`: migrációk, fordítások, nézetek, config publish, útvonalak betöltése, middleware alias regisztrálása
- `register()`: repository interface–implementáció kötések (`bind`), singletonok

Az API útvonalakat az `api` middleware-csoportban, `/api` prefix alatt kell betölteni. Admin útvonalak prefixe: `/api/admin/{name}`.

### Rétegek

| Réteg | Szabály |
|---|---|
| **Controller** | Csak HTTP logika. Repositoryt injektál konstruktoron át. `JsonResponse`-t ad vissza. OA attribútumokkal dokumentált. |
| **Repository** | Interface + implementáció. Az interface az egyetlen publikus kontraktus. |
| **Model** | Eloquent modell. |
| **Request** | FormRequest-alapú validáció minden store/update művelethez. |
| **Resource** | JsonResource minden modellhez. OA sémával dokumentált. |

### Útvonalak

- Névtér: `{name}.` prefix (pl. `user.users.index`)
- Admin CRUD: `Route::resource('{entities}', ...)` ahol lehetséges
- Jogosultságvédelem: `permission:{jogosultsag}` middleware

### Konfiguráció

A `config/{name}.php` fájl a csomag publikus konfigurációja. A ServiceProvider `mergeConfigFrom`-mal tölti be.

### Seeder

Ha a csomag jogosultságokat vagy kezdeti adatokat hoz létre, kötelező egy `{Name}Seeder` osztályt készíteni a `src/Database/Seeders/` mappában (vagy `src/database/seeders/`). A seeder:

- `AclManagementService`-szel hozza létre a szükséges jogosultságokat
- Lokális környezetben (`app()->isLocal()`) tölt be tesztadatokat
- Regisztrálni kell a gyökér `database/seeders/DatabaseSeeder.php` fájlban:

```php
use Molitor\{Name}\Database\Seeders\{Name}Seeder;

// DatabaseSeeder::run() $seeders tömbben:
{Name}Seeder::class,
```

A csomag `README.md`-jében kötelező dokumentálni a seeder regisztrálását `## Seeder regisztrálása` szekció alatt, a pontos `use` importtal és példakóddal.

---

## Frontend csomag (`resources/js/packages/{vue-name}/`)

### Mappastruktúra

```
resources/js/packages/{vue-name}/
├── index.ts
├── README.md
├── components/
│   └── (újrafelhasználható Vue komponensek)
├── composables/
│   └── use{Feature}.ts
├── config/
│   ├── menuBuilder.ts
│   └── dashboardBuilder.ts
├── directives/
├── router/
│   ├── index.ts
│   └── guards.ts
├── services/
│   └── {entity}Service.ts
└── views/
    └── {entity}/
        ├── {Entity}Index.vue
        ├── {Entity}Create.vue
        └── {Entity}Edit.vue
```

### Regisztráció

1. **Vite alias** – `vite.config.js`-ben `'@{name}': path.resolve(..., 'resources/js/packages/{vue-name}')` bejegyzés
2. **TypeScript path** – `tsconfig.json`-ban azonos alias

### `index.ts`

Az `index.ts` a csomag egyetlen belépési pontja. Minden publikus exportot itt kell felsorolni:

```ts
// Composables
export { useFeature } from './composables/useFeature'

// Components
export { default as MyComponent } from './components/MyComponent.vue'

// Services
export { entityService } from './services/entityService'
export type { Entity } from './services/entityService'

// Config
export { FeatureMenuBuilder, featureMenuBuilder } from './config/menuBuilder'
export { FeatureDashboardBuilder, featureDashboardBuilder } from './config/dashboardBuilder'
export { default as router } from './router/index'
```

### Services

Minden backend erőforráshoz saját service fájl. Az axios-klienst az `apiClient.ts`-ből kell importálni:

```ts
import { createApiClient } from './apiClient'
const api = createApiClient()

export const entityService = {
  getAll(params?) { return api.get('/api/admin/{name}/{entities}', { params }) },
  getById(id)     { return api.get(`/api/admin/{name}/{entities}/${id}`) },
  create(data)    { return api.post('/api/admin/{name}/{entities}', data) },
  update(id, data){ return api.put(`/api/admin/{name}/{entities}/${id}`, data) },
  delete(id)      { return api.delete(`/api/admin/{name}/{entities}/${id}`) },
}
```

Az API útvonalak tükrözik a backend route névterét: `/api/admin/{name}/{entities}`.

Típusok (`interface`) a service fájlban laknak, és az `index.ts`-ből exportálódnak.

### Router

```ts
// router/index.ts
const entityRoutes: RouteRecordRaw[] = [
  {
    path: '/admin/{entity}',
    name: 'admin-{entities}',
    component: () => import('../views/{entity}/{Entity}Index.vue'),
    meta: { requiresAuth: true }
  },
  // create, edit ...
]
export default entityRoutes
```

- Admin útvonalak: `/admin/{entity}` prefix
- Jogosultsági meta: `{ permission: '{permission_key}' }`
- Publikus útvonalak: `{ requiresAuth: false }`

### MenuBuilder

```ts
export class {Name}MenuBuilder extends MenuBuilder {
  build(menu: MenuItemConfig, menuName: string): MenuItemConfig {
    if (menuName === 'admin') return this.buildMainMenu(menu)
    return menu
  }
  private buildMainMenu(menu: MenuItemConfig): MenuItemConfig {
    this.addMenuItem(menu, { id: '...', title: '...', path: '/admin/...', icon: ..., order: N })
    return menu
  }
}
export const {name}MenuBuilder = new {Name}MenuBuilder()
```

### DashboardBuilder

```ts
export class {Name}DashboardBuilder extends DashboardWidgetBuilder {
  build(widgets: DashboardWidgetConfig[]): DashboardWidgetConfig[] {
    this.addWidget(widgets, { id: '{name}-widget', component: MyWidget, order: N })
    return widgets
  }
}
export const {name}DashboardBuilder = new {Name}DashboardBuilder()
```

---

## Backend–Frontend összefüggés

| Backend | Frontend |
|---|---|
| `Route::resource('users', ...)` prefix `/api/admin/user` | `userService` metódusai `/api/admin/user/users` végpontra mutatnak |
| `UserResource` mezői | `User` interface mezői tükrözik |
| `StoreUserRequest` validáció | `UserFormData` interface mezői |
| `permission:{key}` middleware | `meta: { permission: '{key}' }` a routerben |

---

## Elnevezési konvenciók

| Elem | Konvenció |
|---|---|
| PHP namespace | `Molitor\{Name}\...` |
| Composer csomagnév | `istvanmolitor/{name}` |
| Vite/TS alias | `@{name}` |
| Frontend csomag mappa | `vue-{name}` (Vue-alapú) vagy `ts-{name}` (pure TS) |
| Route prefix | `/api/admin/{name}` |
| Route name prefix | `{name}.` |
| Vue view fájlok | `{Entity}Index.vue`, `{Entity}Create.vue`, `{Entity}Edit.vue` |
