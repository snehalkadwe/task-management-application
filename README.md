# Laravel Task Management Project

## Requirements

-   **PHP**: 8.3
-   **Node.js**: 20.12
-   **NPM**: 8.15
-   **Composer**: Latest version

## Installation

Follow the steps below to set up the project:

### 1. Install Dependencies

Run the following commands to install PHP and Node dependencies:

```sh
composer install
npm install
```

### 3. Set Up Environment

Copy the example environment file and update your database configurations:

```sh
cp .env.example .env
php artisan key:generate
```

### 4. Database Migration & Seeding

Run migrations and seed the database:

```sh
php artisan migrate
php artisan db:seed
```

### 5. Start the Development Server

```sh
php artisan serve
```

## Usage

Visit `http://127.0.0.1:8000` in your browser to access the application.

## Test Application

```sh
php artisan test
```

This will run all the tests in the application.
