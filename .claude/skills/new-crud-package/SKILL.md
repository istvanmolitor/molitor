---
name: new-crud-package
description: "Scaffolds a new backend+frontend package pair (Molitor\\{Name} + vue-{name}) for a simple admin-managed CRUD entity in this monorepo — clones the two empty GitHub repos, generates all backend/frontend files per the project's CLAUDE.md conventions, wires up composer/vite/tsconfig/menu/router/seeder registration, migrates+seeds, and verifies in a headless browser. Use when the user asks to add a new admin-managed table/entity as a full package pair, e.g. \"csinálj egy X csomagot admin felülettel\", \"new package for managing Y\", \"hozz létre egy {entity} kezelő csomagot\"."
metadata:
  author: molitor
---

# New CRUD package pair

Scaffolds one backend package (`packages/{name}/`, namespace `Molitor\{Name}`) and
one frontend package (`resources/js/packages/vue-{name}/`) for a simple
admin-managed entity, following the conventions in the root `CLAUDE.md` and
wiring both packages into the monorepo. This was originally built for the
`dynamic-form` package (`packages/dynamic-form/`,
`resources/js/packages/vue-dynamic-form/`) — treat those two directories as
the canonical reference implementation and copy/adapt their files rather
than reinventing structure.

Read `CLAUDE.md` at the repo root first — it documents the file layout,
layer responsibilities (Controller/Repository/Model/Request/Resource),
DataTable system, and frontend package conventions in detail. This skill
only adds the operational steps CLAUDE.md doesn't cover: repo cloning,
root-level wiring, install, and verification.

## 1. Gather parameters

Before writing anything, nail down (ask the user with AskUserQuestion if
not given):

- **Package slug** (dash-case, e.g. `dynamic-form`) → PascalCase namespace
  segment is the slug with dashes stripped and each segment capitalized
  (`dynamic-form` → `DynamicForm`, `cms-post-relations` → `CmsPostRelations`).
  Composer name: `istvanmolitor/{slug}`. Vite/TS alias: `@{slug}`.
- **Table name** (snake_case plural, e.g. `dynamic_forms`) and its columns
  beyond `id`/timestamps (name + type + nullable/unique).
- **Two GitHub repo URLs** — `git@github.com:istvanmolitor/{slug}.git` and
  `git@github.com:istvanmolitor/vue-{slug}.git`. Confirm with the user
  whether these already exist (usually created empty in advance) or need
  `gh repo create istvanmolitor/{slug} --private` first.
- **Permission key** (snake_case, usually same as table singular or slug
  with dashes→underscores) and its Hungarian description for the seeder.
- **Menu label, icon, order** — pick a free `lucide-vue-next` icon name
  (grep existing menuBuilders for ones already in use to avoid duplicates:
  `grep -rhn "from 'lucide-vue-next'" resources/js/packages/*/config/menuBuilder.ts`).

## 2. Clone the two repos

```bash
git clone git@github.com:istvanmolitor/{slug}.git packages/{slug}
git clone git@github.com:istvanmolitor/vue-{slug}.git resources/js/packages/vue-{slug}
```

Both directories are covered by root `.gitignore` (`/packages`,
`/resources/js/packages`) — each package is its own independent git repo
with its own GitHub remote, not a submodule. An empty-repo clone warning is
expected and fine.

## 3. Backend package

Build these files under `packages/{slug}/`, using `packages/dynamic-form/`
as the line-by-line template (swap names/fields):

- `composer.json` — name `istvanmolitor/{slug}`, PSR-4 `Molitor\{Name}\` →
  `src/`, `extra.laravel.providers` → `Molitor\{Name}\Providers\{Name}ServiceProvider`,
  require `istvanmolitor/admin` (for the DataTable base class) and
  `istvanmolitor/user` (for `AclManagementService`) if the seeder needs them.
- `src/Models/{Entity}.php` — plain Eloquent model, `$table`, `$fillable`.
- `src/Database/Migrations/{date}_000000_create_{table}_table.php` — use
  today's date for the filename prefix.
- `src/Repositories/{Entity}RepositoryInterface.php` +
  `src/Repositories/{Entity}Repository.php` — interface is the only public
  contract; keep it to `create`/`update`/`delete` for a plain CRUD entity,
  add more only if the domain needs it.
- `src/Http/Requests/Store{Entity}Request.php` +
  `Update{Entity}Request.php` — `authorize()` returns `true`;
  `Update{Entity}Request` reads the route-bound model's id via
  `$this->route('{route_param}')?->id` for unique-ignoring validation.
- `src/Http/Resources/{Entity}Resource.php`.
- `src/DataTables/{Entity}DataTable.php` extends `Molitor\Admin\DataTables\DataTable`
  — never override `applyFilters()`/`getBaseQuery()`; for translatable
  fields use `joinTranslation()`/`selectBase()` per CLAUDE.md, not manual joins.
- `src/Http/Controllers/Api/{Entity}Controller.php` — constructor-injects
  `{Entity}RepositoryInterface`; `index()` delegates to the DataTable;
  `store`/`update`/`destroy` go through the repository.
- `src/Providers/{Name}ServiceProvider.php` — `loadMigrationsFrom`,
  registers `routes/api.php` under `prefix: api`, binds the repository
  interface in `register()`.
- `src/routes/api.php` — `Route::prefix('admin/{slug}')->middleware(['api','auth:sanctum','permission:{permission_key}'])->name('{slug}.')->group(...)` with `Route::resource('{entities}', {Entity}Controller::class)`.
- `src/Database/Seeders/{Name}Seeder.php` — `AclManagementService::createPermission($key, $description, 'admin')` wrapped in try/catch `PermissionException`; add an `if (app()->isLocal())` block seeding a handful of test rows via `firstOrCreate`.
- `README.md` — must include a `## Seeder regisztrálása` section with the
  exact `use` import and the `$this->call([...])` snippet (this is
  mandatory per CLAUDE.md whenever the package creates permissions/data).

**Route param naming gotcha**: `Route::resource('{dash-entities}', ...)`
generates the wildcard as `Str::singular(str_replace('-', '_', $name))`
(e.g. `dynamic-forms` → `{dynamic_form}`), but the controller method
parameter can still be camelCase (`$dynamicForm`) — Laravel's implicit
binding falls back to `Str::snake($paramName)` when matching. Keep
`$this->route('{snake_case_singular}')` in the Update request consistent
with this.

## 4. Frontend package

Build these files under `resources/js/packages/vue-{slug}/`, using
`resources/js/packages/vue-dynamic-form/` as the template:

- `services/{entity}Service.ts` — `createApiClient` from `@user/services/apiClient`;
  CRUD methods hitting `/api/admin/{slug}/{entities}`; export the
  `{Entity}` and `{Entity}FormData` interfaces.
- `router/index.ts` — routes at `/admin/{slug}`, `/admin/{slug}/create`,
  `/admin/{slug}/:id/edit`, each with `meta: { requiresAuth: true, permission: '{permission_key}' }`.
- `config/menuBuilder.ts` — `{Name}MenuBuilder extends MenuBuilder`,
  registers under `menuName === 'admin'` only.
- `views/{entity}/{Entity}Index.vue` — `AdminLayout` + `DataTable` in
  self-fetching mode (`url="/api/admin/{slug}/{entities}"`, no manual
  `columns`/`fetchItems` plumbing needed — the component fetches columns
  and rows itself).
- `views/{entity}/{Entity}Create.vue` / `{Entity}Edit.vue` — `Card` +
  `InputField` per field (not raw `Label`+`Input`) + `FormButtons`.
- `index.ts` — re-export service, types, `router`, and the menu builder
  instance/class.

## 5. Wire into the root app

- `composer.json`: add `"istvanmolitor/{slug}": "@dev"` to `require`, and a
  `{"type": "path", "url": "packages/{slug}"}` entry to `repositories`.
- `vite.config.js`: add `'@{slug}': path.resolve(__dirname, 'resources/js/packages/vue-{slug}')`.
- `tsconfig.json`: add the matching `"@{slug}"` / `"@{slug}/*"` paths entries.
- `resources/js/menuRegistry.ts`: import the menu builder instance, call
  `menuRegistry.register(...)`.
- `resources/js/router/index.ts`: import the routes array, spread it into
  the combined `routes` array.
- `database/seeders/DatabaseSeeder.php`: import `{Name}Seeder`, add it to
  the `$seeders` array.

## 6. Install, migrate, seed

```bash
composer update istvanmolitor/{slug} --no-interaction
./vendor/bin/sail artisan migrate --path=vendor/istvanmolitor/{slug}/src/Database/Migrations
./vendor/bin/sail artisan db:seed --class="Molitor\{Name}\Database\Seeders\{Name}Seeder"
```

Use `./vendor/bin/sail artisan ...` for anything that touches the app's DB
or runtime — the project's `.env` points `DB_HOST=mysql`, which only
resolves inside the Sail Docker network, so bare `php artisan` on the host
fails with "could not find driver".

Build the frontend and typecheck just the new package:

```bash
npm run build
npx vue-tsc --noEmit -p tsconfig.json 2>&1 | grep -i "{slug}"
```

## 7. Verify in the browser

No browser tool is available directly — use a headless Playwright driver.
Chromium is normally already cached at `~/.cache/ms-playwright`; if
`node -e "require('playwright')"` fails, resolve the cached npx install
first:

```bash
find ~/.npm/_npx -maxdepth 2 -iname playwright  # find a cached copy
NODE_PATH=<that/node_modules> node script.js
```

Drive: log in at `/admin/login` (`admin@example.com` / `admin` seeded by
`UserSeeder`), navigate to `/admin/{slug}`, screenshot the list (confirm
menu item + seeded rows render), create a throwaway row, edit it, delete
it (the `DeleteButton` opens a custom `ConfirmDialog` modal — click the
delete icon, then click the button with text "Törlés", not a native
`window.confirm`). Check `page.on('console'/'response')` for errors/4xx
unrelated to pre-existing app issues.

## 8. Don't commit automatically

Leave the new package repos and root wiring changes uncommitted unless the
user explicitly asks to commit/push — two independent git repos plus the
monorepo are affected, so confirm scope before running any `git commit`/`push`.
