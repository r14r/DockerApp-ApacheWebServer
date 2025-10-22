# Admin Panel

This directory contains the admin panel for managing the Apache web server configuration.

## Features

- **phpMyAdmin Access**: Quick link to phpMyAdmin for database administration
- **.htaccess Editor**: Edit .htaccess files directly from the web interface
- **System Information**: View PHP version, server info, and database connection status
- **Bootstrap UI**: Modern, responsive interface built with Bootstrap 5

## Access

Access the admin panel at: `http://localhost:40000/admin/`

**Default Password**: `admin123`

You can change this password by setting the `ADMIN_PASSWORD` environment variable in docker-compose.yml.

## Security Note

⚠️ This is a basic admin panel with simple password authentication. For production use, you should:
- Implement proper authentication (database-backed users, sessions, etc.)
- Use HTTPS
- Add IP whitelisting
- Implement CSRF protection
- Add audit logging

## phpMyAdmin

phpMyAdmin is accessible at: `http://localhost:40020`

**Login credentials**:
- Root user: `root` / `secret`
- App user: `appuser` / `apppass`
