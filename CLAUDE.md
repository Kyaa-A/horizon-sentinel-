# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Horizon Sentinel is a digital leave request and conflict avoidance system built for Horizon Dynamics. It's a Laravel 12 application with Tailwind CSS 4 that manages employee time-off requests and manager approvals, with the core goal of preventing staffing conflicts through centralized visibility and calendar-based conflict detection.

**Key Domain Concepts:**
- **Employees** submit leave requests (PTO, Sick Leave, Vacation, Unpaid Leave) with date ranges
- **Managers** review pending requests and see a team availability calendar before approving/denying
- **Conflict Detection** is achieved through calendar overlays that highlight potential understaffing or critical overlaps

## Common Commands

### Development
```bash
# Start full development environment (server, queue, logs, vite)
./start-dev.sh
# OR
composer dev

# Start individual services
php artisan serve           # Development server on port 8000
php artisan queue:listen    # Queue worker
php artisan pail           # Real-time log viewer
npm run dev                # Vite dev server with hot reload

# Build for production
npm run build
```

### Database
```bash
# Run migrations (recommended - uses direct connection)
./migrate.sh

# Fresh database with seeders
./migrate.sh --seed

# Rollback last migration
./migrate.sh --rollback

# Create new migration
./artisan.sh make:migration create_table_name

# Run any artisan command with proper environment
./artisan.sh [command]
```

### Testing
```bash
# Run all tests
composer test
# OR
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Laravel Pint (code formatting)
./vendor/bin/pint

# Check formatting without fixing
./vendor/bin/pint --test
```

### Other Artisan Commands
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Create resources
php artisan make:model ModelName -mfc    # Model + migration + factory + controller
php artisan make:controller ControllerName
php artisan make:request RequestName
php artisan make:seeder SeederName

# Tinker (REPL)
php artisan tinker
```

## Architecture & Code Structure

### Database - Supabase PostgreSQL
This project uses Supabase as the PostgreSQL database provider.

**Connection Types:**
- **Pooled Connection (Default - Port 6543)**: Used for all web requests, optimal for production traffic
- **Direct Connection (Port 5432)**: Used for migrations and database operations via `pgsql_direct` connection

**Configuration Files:**
- `.env` - Contains Supabase credentials (DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD)
- `config/database.php` - Defines both `pgsql` (pooled) and `pgsql_direct` connections

**Helper Scripts:**
- `./start-dev.sh` - Starts development environment with clean DB environment variables
- `./migrate.sh` - Runs migrations using the direct connection (recommended)

**Important:** The project clears system environment variables that might override `.env` settings. These are automatically handled by the helper scripts.

### Role-Based Access Control (RBAC)
The User model will have a `role` field (employee/manager). Managers can view/approve requests from their direct reports. Authentication scaffolding will likely use Laravel Breeze.

### Database Schema (Planned)
- **users table**: Contains `role` column for RBAC (employee/manager)
- **leave_requests table**: Core entity with employee_id, manager_id, type (enum), start_date, end_date, status (pending/approved/denied), created_at timestamps

### Request Lifecycle
1. Employee submits leave request via form → validation → stored as "Pending"
2. Request routed to appropriate manager based on employee-manager relationship
3. Manager views request alongside team calendar showing existing approved time-off
4. Manager approves/denies → status updated → employee notified
5. Approved requests appear on team calendar for future conflict detection

### Frontend Architecture
- **Blade templates** in `resources/views/` for server-side rendering
- **Tailwind CSS 4** via Vite plugin for styling
- **Alpine.js** (if added) for lightweight interactivity
- Vite entry points: `resources/css/app.css` and `resources/js/app.js`

### Key Modules (To Be Built)
- **Employee Interface**: "My Leave Requests" page, submission form, status tracking
- **Manager Interface**: "Pending Approvals" list, team availability calendar with conflict highlighting
- **Notifications**: Email/in-app notifications for status changes (optional for MVP)

## Project-Specific Guidelines

### Task Management
This project uses a structured task-based workflow tracked in `.cursor/.rules/`:
- **create-prd.md**: Full Product Requirements Document outlining all features and requirements
- **generate-task.md**: Guide for breaking down PRD features into granular development tasks
- **process-task-list.md**: Active task tracking with IDs (HS-SETUP-*, HS-DB-*, HS-AUTH-*, etc.), statuses, dependencies, and effort estimates

When implementing features, reference the PRD and task list to ensure alignment with requirements. Tasks follow the pattern: Task ID, Description, Acceptance Criteria, Status, Estimated Effort, Dependencies. 

### Database Configuration
Default setup uses SQLite (`database/database.sqlite`). For MySQL, update `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=horizon_sentinel
DB_USERNAME=root
DB_PASSWORD=
```

### Testing Configuration
Tests use in-memory SQLite and array drivers for cache/session (see `phpunit.xml`). Test suites are organized as:
- `tests/Unit/` - Unit tests for isolated components
- `tests/Feature/` - Feature tests for HTTP requests and application behavior

## Non-Functional Requirements to Keep in Mind
- **Security**: Implement CSRF protection (enabled by default), XSS prevention, proper authorization checks
- **Performance**: Keep queries efficient, use eager loading to avoid N+1 queries on calendar views
- **Usability**: Intuitive navigation for non-technical users (employees/managers)
- **Scalability**: Design to accommodate growing employee base at Horizon Dynamics

## Initial Setup (for fresh clones)
```bash
composer setup   # Runs: install, copy .env, generate key, migrate, npm install, npm build
```
