# DockerApp-ApacheWebServer

Apache Web Server with MySQL database and phpMyAdmin, built with Docker.

## Features

- **Apache 2.4** with PHP 8.3
- **MySQL 8.0** database
- **phpMyAdmin** for database administration
- **Bootstrap-based Admin Panel** for server management
- **.htaccess editor** for configuration management

## Quick Start

1. Build and start the containers:
```bash
docker compose up -d
```

2. Access the services:
   - **Main Website**: http://localhost:40000
   - **Admin Panel**: http://localhost:40000/admin/ (password: `admin123`)
   - **phpMyAdmin**: http://localhost:40020
   - **MySQL Database**: localhost:40010

## Admin Panel

The admin panel provides:
- System information dashboard
- Database connection status
- Live .htaccess file editor
- Quick links to phpMyAdmin and other resources
- Bootstrap 5 responsive UI

**Default credentials**: Password is `admin123` (can be changed via `ADMIN_PASSWORD` environment variable)

## phpMyAdmin Access

- URL: http://localhost:40020
- Root credentials: `root` / `secret`
- App user credentials: `appuser` / `apppass`

## Database Configuration

The application connects to MySQL with these default settings:
- Host: `db`
- Database: `appdb`
- User: `appuser`
- Password: `apppass`

## Security Note

⚠️ The admin panel uses basic authentication for demonstration purposes. For production use:
- Implement proper authentication with database-backed users
- Use HTTPS
- Add IP whitelisting
- Implement CSRF protection
- Change default passwords
