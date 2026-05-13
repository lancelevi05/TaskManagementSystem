# TaskFlow

TaskFlow is a PHP and MySQL task management system with:

- user registration and login
- personal task CRUD
- search, filters, and sort options
- task status toggling
- due dates, priorities, and dashboard stats

## Setup

1. Import [database.sql](database.sql) into MySQL.
2. Update database credentials in [config/db.php](config/db.php).
3. Open the project in Apache, for example `http://localhost/TaskManagementSystem`.

## Notes

- Each user only sees their own tasks.
- Form submissions use CSRF protection.
- The UI is fully responsive and styled with custom CSS.
# Task Management System

Simple, clean task manager with minimal code.

## Quick Setup

1. **Import database** in phpMyAdmin:
   - Copy & paste `database.sql`

2. **Check DB credentials** in `config/app.php`:
   ```php
   define('DB_NAME', 'task_management_system');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Open in browser**:
   ```
   http://localhost/TaskManagementSystem/
   ```

## Folder Structure

```
TaskManagementSystem/
├── config/app.php              # Database + helper functions
├── includes/
│   ├── header.php             # HTML header & nav
│   └── footer.php             # HTML footer
├── public/
│   ├── index.php              # Dashboard
│   ├── assets/css/style.css   # Styling
│   └── tasks/
│       ├── index.php          # List tasks
│       ├── create.php         # Handle create
│       ├── edit.php           # Edit form & update
│       ├── toggle.php         # Toggle done status
│       └── delete.php         # Delete task
├── database.sql               # Schema
└── index.php                  # Root redirect
```

## Features

✅ Create, read, update, delete tasks  
✅ Mark tasks done/pending  
✅ Search & filter by status  
✅ Priority levels (low, medium, high)  
✅ Due dates  
✅ Simple, flat code structure  

## Debug

All logic is **inline in .php files**—no classes or abstractions. Add `var_dump()` or `echo` directly to debug.

## Tech

- PHP 8+
- MySQL 5.7+
- Plain CSS
