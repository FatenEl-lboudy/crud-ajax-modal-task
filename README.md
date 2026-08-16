# A Laravel-based CRUD application built as part of the technical task.

## Features

- ✅ Create & Edit products via AJAX using a Bootstrap Modal
- ✅ Multi-row delete (works across paginated pages)
- ✅ AJAX-powered search and page navigation using DataTables
- ✅ Hierarchical category tree (parent/child categories)
- ✅ Server-side processing for performance on large datasets

## Tech Stack

- **Backend:** Laravel, Yajra DataTables
- **Frontend:** Bootstrap 4, jQuery, AJAX
- **Database:** MySQL

## Setup

1. Clone the repo
```bash
   git clone <repo-url>
   cd al-badr-smart-systems-task
```

2. Install dependencies
```bash
   composer install
   npm install
```

3. Set up environment
```bash
   cp .env.example .env
   php artisan key:generate
```
   Configure your database credentials in `.env`.

4. Run migrations & seeders
```bash
   php artisan migrate --seed
```

5. Serve the app
```bash
   php artisan serve
```

## Notes

Built to match the task requirements: AJAX-based CRUD with modals, multi-select delete, AJAX search, and a category tree view.
