# TaskSpace 🌌

TaskSpace is a premium, high-fidelity task management application built with a modern dark-mode aesthetic and glassmorphic UI elements. It features seamless interactive capabilities like drag-and-drop task reordering, AJAX status toggling, search, and pagination, all built on top of Laravel 12 and powered by Tailwind CSS v4 and Bootstrap 4.

---

## ✨ Features

- **💎 Premium Glassmorphic UI**: Sleek dark-mode theme utilizing rich, curated gradients, micro-animations, and glassmorphic panels for an exceptionally premium look and feel.
- **🔐 Secure Authentication**: Integrated login and logout system powered by secure Laravel session authentication.
- **🔄 Interactive Drag & Drop Reordering**: Reorder tasks intuitively using drag handles powered by SortableJS, with ordering persisted instantly to the database via AJAX.
- **⚡ Optimistic AJAX Status Toggle**: Change task status between `Pending` and `Completed` with a single click, instantly updating the UI styling (striking out completed tasks, changing badge colors) without reloading the page.
- **📝 Interactive CRUD**: Full Create, Read, Update, and Delete operations for tasks managed inside elegant, contextual modal dialogs.
- **🔍 Quick Search & Pagination**: Filter tasks by title or description dynamically, complete with page navigation (10 tasks per page).
- **🔔 Custom Toast Notifications**: An elegant, custom-designed toast utility that displays success or error messages automatically.

---

## 🛠️ Technology Stack

- **Backend**: Laravel 12.x, PHP 8.2+
- **Frontend**: Blade Templates, Tailwind CSS v4, Bootstrap 4, jQuery, SortableJS, FontAwesome 6
- **Database**: SQLite (default connection for easy local setup)
- **Asset Bundler**: Vite 6.x

---

## 🚀 Setup & Installation

Follow these steps to set up and run the application locally on your machine:

### Prerequisites

Ensure you have the following installed:
- PHP 8.2 or higher
- Composer
- Node.js & npm
- SQLite extension (enabled in your `php.ini`)

### Quick Setup (Recommended)

Run the automated composer script which installs all PHP and NPM dependencies, sets up the database, and builds assets:

```bash
composer run setup
```

### Manual Setup Steps

If you prefer to run the setup steps manually, run the following commands:

1. **Install Composer Dependencies**:
   ```bash
   composer install
   ```

2. **Configure Environment File**:
   Create a `.env` file by copying the example file:
   ```bash
   copy .env.example .env
   ```

3. **Generate App Key**:
   ```bash
   php artisan key:generate
   ```

4. **Initialize Database and Seed Admin User**:
   *Note: Ensure the database file `database/database.sqlite` is created or automatically initialized.*
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Install and Build Frontend Assets**:
   ```bash
   npm install
   npm run build
   ```

---

## 🔑 Default Credentials

The database seeder automatically creates an administrator account for you to log in and access the dashboard.

- **Email**: `admin@gmail.com`
- **Password**: `123456`

---

## 💻 Running the Application

To run the application locally with hot-reloading for frontend assets and local servers:

```bash
composer run dev
```

Alternatively, you can run the services individually:

- **Start PHP server**: `php artisan serve`
- **Start frontend hot-reloader**: `npm run dev`

Access the application at [http://localhost:8000](http://localhost:8000).

---

## 🛣️ API & Route Reference

Below are the primary routes registered within the application:

| Method | URI | Action / Route Name | Middleware | Description |
|---|---|---|---|---|
| **GET** | `/login` | `login` | `guest` | Show the login page |
| **POST** | `/login` | `login` | `guest` | Authenticate the user |
| **POST** | `/logout` | `logout` | `auth` | Log out the user |
| **GET** | `/` | Redirects to `/tasks` | `auth` | Root landing redirection |
| **GET** | `/tasks` | `tasks.index` | `auth` | View the tasks dashboard |
| **POST** | `/tasks` | `tasks.store` | `auth` | Create a new task |
| **PUT** | `/tasks/{task}` | `tasks.update` | `auth` | Update a specific task |
| **DELETE** | `/tasks/{task}` | `tasks.destroy` | `auth` | Delete a specific task |
| **PATCH** | `/tasks/{task}/toggle-status` | `tasks.toggle-status` | `auth` | Toggle task status (AJAX) |
| **POST** | `/tasks/reorder` | `tasks.reorder` | `auth` | Persist new task order (AJAX) |

---

## 🧪 Running Tests

The application includes a comprehensive test suite covering guest authentication restrictions, login/logout validation, task CRUD operations, AJAX status toggling, and task reordering.

To execute the feature tests, run:

```bash
php artisan test
```
