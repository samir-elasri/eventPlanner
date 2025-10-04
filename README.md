# Event Planner

A comprehensive event planning application built with Laravel 11 backend and Vue 3 frontend, featuring a REST API, admin dashboard powered by FilamentPHP, and complete API documentation.

## 📋 Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Environment Variables](#environment-variables)
- [Email Configuration](#email-configuration)
- [Running the Application](#running-the-application)
- [API Documentation](#api-documentation)
- [Postman Collection](#postman-collection)
- [Access Points](#access-points)

## 🔧 Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js >= 18 & NPM (for Vue 3 frontend)

## 📦 Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd eventPlanner
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Seed the database (optional):
```bash
php artisan db:seed
```

8. Build frontend assets:
```bash
npm run build
```

## 🔐 Environment Variables

Configure the following variables in your `.env` file:

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_planner
DB_USERNAME=root
DB_PASSWORD=
```

### Application Settings
```env
APP_NAME="Event Planner"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### Email Configuration (see below for Mailtrap setup)
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@eventplanner.com
MAIL_FROM_NAME="${APP_NAME}"
```

### FilamentPHP Admin
```env
FILAMENT_DASHBOARD_URL=/admin
```

## 📧 Email Configuration

I recommend using **Mailtrap** for development email testing:

1. Sign up for a free account at [Mailtrap.io](https://mailtrap.io/)
2. Create a new inbox
3. Copy the SMTP credentials from your Mailtrap inbox
4. Update the following in your `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=<your_mailtrap_username>
   MAIL_PASSWORD=<your_mailtrap_password>
   MAIL_ENCRYPTION=tls
   ```

For production, configure your preferred email service (SendGrid, Mailgun, SES, etc.)

**Mailtrap Resources:**
- [Mailtrap Documentation](https://help.mailtrap.io/)
- [Laravel Mail Configuration](https://laravel.com/docs/mail)

## 🚀 Running the Application

### Backend Server
Start the Laravel development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Frontend Development (Vue 3)
Start the Vite development server:
```bash
npm run dev
```

The Vue 3 frontend will be available at `http://localhost:5173`

### Build for Production
```bash
npm run build
```

## 📚 API Documentation

### Swagger/OpenAPI Documentation
Access the interactive API documentation at:
- **URL:** `http://localhost:8000/api/documentation`
- **Resources:**
  - [Swagger UI Documentation](https://swagger.io/tools/swagger-ui/)
  - [OpenAPI Specification](https://swagger.io/specification/)

## 📮 Postman Collection

A Postman collection file is available at the root of the project for easy API testing:

1. Open Postman
2. Click **Import**
3. Select the `postman_collection.json` file from the project root
4. The collection will be imported with all API endpoints pre-configured

**Postman Resources:**
- [Download Postman](https://www.postman.com/downloads/)
- [Importing Collections Guide](https://learning.postman.com/docs/getting-started/importing-and-exporting-data/)

## 🔗 Access Points

### Base API
- **URL:** `http://localhost:8000/api`
- All API endpoints are prefixed with `/api`

### Admin Dashboard (FilamentPHP)
- **URL:** `http://localhost:8000/admin`
- Create an admin user:
  ```bash
  php artisan make:filament-user
  ```
- **Resources:**
  - [FilamentPHP Documentation](https://filamentphp.com/docs)
  - [FilamentPHP Admin Panel](https://filamentphp.com/docs/panels)

### Swagger API Documentation
- **URL:** `http://localhost:8000/api/documentation`

## 🛠️ Additional Commands

### Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```