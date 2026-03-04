# GitHub Copilot Instructions - Molitor Project

## Project Structure Conventions

This document describes the project structure and conventions that should be followed when working with this codebase.

### Backend Packages (Laravel)

All Laravel backend packages are located in:
```
/packages/
```

These packages contain PHP code, Laravel-specific implementations, and backend logic.

Examples:
- `/packages/admin/` - Admin functionality
- `/packages/cms/` - Content Management System
- `/packages/article-parser/` - Article parsing logic
- `/packages/article-scraper/` - Web scraping functionality
- `/packages/html-parser/` - HTML parsing utilities
- `/packages/media/` - Media management
- `/packages/user/` - User management

Each package typically contains:
- `composer.json` - PHP dependencies
- `src/` - Source code (PHP classes, Controllers, Models, Services)
- `resources/` - Package-specific resources (views, config)
- `tests/` - Package tests

### Frontend Packages (Vue.js)

All Vue.js frontend packages are located in:
```
/resources/js/packages/
```

These packages contain Vue components (.vue files), TypeScript files (.ts), and frontend logic.

Examples:
- `/resources/js/packages/vue-user/` - User-related Vue components
- `/resources/js/packages/vue-admin/` - Admin panel components

Each Vue package typically contains:
- `components/` - Reusable Vue components
- `views/` - Page-level Vue components
- `services/` - TypeScript services (API calls, utilities)
- `types/` - TypeScript type definitions
- `composables/` - Vue composables
- `stores/` - State management (Pinia)

### Key Conventions

1. **Backend (PHP/Laravel)**: Always look in `/packages/` for Laravel packages
2. **Frontend (Vue/TypeScript)**: Always look in `/resources/js/packages/` for Vue components and TypeScript code
3. **Main Application**: The root Laravel application is in standard locations (`/app/`, `/config/`, `/routes/`, etc.)
4. **Public Assets**: Compiled frontend assets are in `/public/build/`

### Development Workflow

- Backend packages are autoloaded via Composer
- Frontend packages are bundled via Vite (see `vite.config.js`)
- Each package can have its own dependencies and configuration

### When Creating New Features

- **Backend logic**: Create or modify files in `/packages/[package-name]/src/`
- **Frontend components**: Create or modify files in `/resources/js/packages/[package-name]/`
- **API endpoints**: Define in package-specific route files or `/routes/web.php`
- **Database migrations**: Place in package-specific migrations or `/database/migrations/`

## Additional Context

This is a modular Laravel application with a Vue.js frontend. The monorepo structure allows for organized, package-based development where each package can be independently maintained while being part of the larger application.

