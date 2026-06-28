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

## DataTable – szűrhető, lapozható, rendezhető lista

Az admin felületen minden listanézet a `DataTable` rendszert használja. A szűrés, lapozás és rendezés **szerver oldalon** történik.

### Backend: `DataTable` absztrakt osztály

**Helye:** `packages/admin/src/DataTables/DataTable.php`

Minden entitáshoz saját DataTable osztályt kell létrehozni a `src/DataTables/` mappában:

```php
// packages/{name}/src/DataTables/{Entity}DataTable.php
namespace Molitor\{Name}\DataTables;

use Molitor\Admin\DataTables\DataTable;

class {Entity}DataTable extends DataTable
{
    protected function getModelClass(): string
    {
        return {Entity}::class;
    }

    protected function getResourceClass(): string
    {
        return {Entity}Resource::class;
    }

    protected function initColumns(): void
    {
        $this->addColumn('title')
            ->setLabel('Cím')
            ->setSearchable()
            ->setOrderable();

        $this->addColumn('slug')
            ->setLabel('Slug')
            ->setSearchable();
    }
}
```

**`DataTableColumn` metódusai** (fluent builder):

| Metódus | Leírás |
|---|---|
| `->setLabel('Cím')` | Frontend fejléc szövege (default: ucfirst(name)) |
| `->setSearchable()` | LIKE keresésnél bevonni |
| `->setOrderable()` | Rendezési oszlopként engedélyezni |
| `->setHidden()` | Ne küldjük a kliensnek (csak query-hez kell) |

**Haladó testreszabás:**

- `getBaseQuery()` – override az eager loadinghoz: `return {Entity}::query()->with('relation')`
- `query(Builder $query)` – extra szűrők hozzáadása (pl. `->where('is_active', true)`)
- `getDefaultSort()` / `getDefaultDirection()` – alapértelmezett rendezés megadása
- `getPerPage()` – alapértelmezett oldalméret (default: 10)

**Controller:**

```php
public function index({Entity}DataTable $dataTable): AnonymousResourceCollection
{
    return $dataTable->getResponse();
}
```

A DataTable a requestből olvassa a paramétereket, ezért a controller egyetlen sort tartalmaz.

**Válasz struktúra:**

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 42
  },
  "filters": {
    "search": "query",
    "sort": "title",
    "direction": "asc"
  },
  "columns": [
    { "key": "title", "label": "Cím", "sortable": true },
    { "key": "slug", "label": "Slug", "sortable": false }
  ]
}
```

**Elfogadott query paraméterek:**

| Paraméter | Leírás |
|---|---|
| `search` | LIKE keresési szöveg |
| `sort` | Rendezési oszlop neve (csak orderable oszlopra érvényes) |
| `direction` | `asc` vagy `desc` |
| `per_page` | Oldalméret (default: 10) |
| `page` | Oldalszám (default: 1) |

---

### Frontend: `DataTable` komponens

**Helye:** `resources/js/packages/vue-admin/components/ui/dataTable/DataTable.vue`

A komponens állapotmentes: minden adatot propokként kap, az interakciókat `@fetch` eseménnyel jelzi.

**Props:**

| Prop | Típus | Leírás |
|---|---|---|
| `columns` | `Column[]` | Oszlopdefiníciók (a szervertől jön) |
| `data` | `TData[]` | Az aktuális oldal sorai |
| `loading` | `boolean` | Töltési állapot |
| `pagination` | `PaginationMeta` | Lapozási metaadatok |
| `searchable` | `boolean` | Keresőmező megjelenítése (default: true) |
| `searchPlaceholder` | `string` | Keresőmező placeholder szövege |
| `defaultSort` | `string` | Alapértelmezett rendezési oszlop |
| `defaultDirection` | `'asc' \| 'desc'` | Alapértelmezett rendezési irány |

**Emit – `@fetch(params)`:**

300 ms debounce-szal hívódik keresésnél, azonnali rendezésnél és lapozásnál:

```ts
{ search?: string; sort?: string; direction?: 'asc' | 'desc'; page?: number }
```

**Slot-ok:**

| Slot | Leírás |
|---|---|
| `#actions` | Fejléc jobb oldali gombok (pl. "Új elem" gomb) |
| `#row-actions="{ row }"` | Sor szintű műveletek (edit, delete stb.) |
| `#cell-{key}="{ value, row }"` | Egyedi cella renderelés |
| `#empty` | Üres állapot szövege |

---

### Index nézet minta

```vue
<script setup lang="ts">
import { AdminLayout, toastService } from '@admin'
import DataTable, { type Column, type PaginationMeta } from '@admin/components/ui/dataTable/DataTable.vue'
import CreateButton from '@admin/components/ui/button/CreateButton.vue'
import EditButton from '@admin/components/ui/button/EditButton.vue'
import DeleteButton from '@admin/components/ui/button/DeleteButton.vue'
import { ref, onMounted } from 'vue'
import { entityService, type Entity } from '../../services/entityService'

const items = ref<Entity[]>([])
const isLoading = ref(false)
const pagination = ref<PaginationMeta>({ current_page: 1, last_page: 1, per_page: 10, total: 0 })
const columns = ref<Column[]>([])

const fetchItems = async (params: {
  search?: string; sort?: string; direction?: 'asc' | 'desc'; page?: number
}) => {
  try {
    isLoading.value = true
    const response = await entityService.getAll(params)
    items.value = response.data.data
    pagination.value = response.data.meta
    columns.value = (response.data.columns ?? []) as Column[]
  } catch {
    toastService.error('Hiba történt az adatok betöltése során.')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => fetchItems({ page: 1, sort: 'name', direction: 'asc' }))
</script>

<template>
  <AdminLayout pageTitle="Entitások">
    <DataTable
      :columns="columns"
      :data="items"
      :loading="isLoading"
      :pagination="pagination"
      search-placeholder="Keresés..."
      default-sort="name"
      default-direction="asc"
      @fetch="fetchItems"
    >
      <template #actions>
        <CreateButton to="/admin/{entity}/create">Új elem</CreateButton>
      </template>
      <template #row-actions="{ row }">
        <EditButton @click="router.push(`/admin/{entity}/${row.id}/edit`)" />
        <DeleteButton @confirm="deleteItem(row.id!)" />
      </template>
      <template #empty>Nincs megjeleníthető adat.</template>
    </DataTable>
  </AdminLayout>
</template>
```

**Service `getAll` metódus paraméter típusa:**

```ts
getAll(params?: {
  page?: number
  search?: string
  sort?: string
  direction?: 'asc' | 'desc'
  per_page?: number
})
```

A válasz `columns` mezőjét mindig `(response.data.columns ?? []) as Column[]` alakban kell kezelni (az első betöltésig üres tömb).

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
