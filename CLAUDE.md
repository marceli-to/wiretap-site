# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application with Vite frontend build system and TailwindCSS styling.

## Development Commands

### Primary Development
- `composer run dev` - Start the full development environment (Laravel server, queue worker, logs, and Vite)
  - Runs Laravel server, queue listener, Pail logs, and Vite dev server concurrently
- `npm run dev` - Start Vite development server only
- `php artisan serve` - Start Laravel development server only

### Build and Test
- `npm run build` - Build frontend assets for production using Vite
- `composer run test` - Run PHPUnit tests (clears config cache first)
- `php artisan test` - Run Laravel tests directly

### Code Quality
- Laravel Pint is included for PHP code formatting

## Architecture

### Backend (Laravel)
- **Framework**: Laravel 12 with PHP 8.2+
- **Structure**: Standard Laravel MVC architecture
  - `app/Http/` - Controllers and middleware
  - `app/Models/` - Eloquent models
  - `app/Providers/` - Service providers
  - `routes/web.php` - Web routes
  - `routes/console.php` - Artisan commands
- **Testing**: PHPUnit with Feature and Unit test suites in `tests/`
- **Database**: Configured for SQLite (in-memory for tests)

### Frontend
- **Build Tool**: Vite with Laravel integration
- **CSS Framework**: TailwindCSS v4
- **Entry Points**:
  - `resources/css/app.css` - Main CSS file
  - `resources/js/app.js` - Main JavaScript file
- **Views**: Blade templates in `resources/views/`

## Key Files
- `composer.json` - PHP dependencies and custom scripts
- `package.json` - Node.js dependencies and build scripts
- `vite.config.js` - Vite configuration with Laravel and TailwindCSS plugins
- `phpunit.xml` - Test configuration