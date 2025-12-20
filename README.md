# LMS (Learning Management System)

A comprehensive Learning Management System built with Laravel 12, featuring role-based access control for Students, Instructors, and Administrators.

## Table of Contents

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Architecture](#architecture)
- [Security Features](#security-features)
- [User Roles](#user-roles)
- [Development](#development)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

## Overview

The LMS platform provides a secure, multi-role educational environment with:

- **Three-Role System**: Students (learners), Instructors (course facilitators), and Admins (system administrators)
- **Role-Based Access Control**: Spatie Permission package integration
- **Session-Based Authentication**: Laravel Sanctum with HTTP-only cookies
- **Security-First Design**: Account lockout, login logging, password requirements
- **Responsive UI**: Bootstrap 5.3.3 with role-specific themes

## Requirements

- PHP 8.0 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js 16+ and NPM
- Git

## Installation

### 1. Clone the Repository

\`\`\`bash
git clone <repository-url> lms
cd lms
\`\`\`

### 2. Install Dependencies

\`\`\`bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
\`\`\`

### 3. Environment Setup

\`\`\`bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
\`\`\`

### 4. Database Configuration

Edit \`.env\` and configure your database:

\`\`\`env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms
DB_USERNAME=your_username
DB_PASSWORD=your_password
\`\`\`

### 5. Run Migrations

\`\`\`bash
php artisan migrate
\`\`\`

### 6. Seed Database (Optional)

\`\`\`bash
php artisan db:seed
\`\`\`

### 7. Build Assets

\`\`\`bash
# Development
npm run dev

# Production
npm run prod
\`\`\`

### 8. Start Development Server

\`\`\`bash
php artisan serve
\`\`\`

Visit \`http://localhost:8000\` to access the application.

## Configuration

### Session Timeout

Role-based session timeouts are configured automatically:
- **Students**: 30 minutes
- **Instructors**: 2 hours
- **Admins**: 4 hours

### Account Lockout

Failed login attempt thresholds:
- **Admins**: 3 attempts = 30-minute lockout
- **Students/Instructors**: 5 attempts = 15-minute lockout

### Password Requirements

All passwords must contain:
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character
- Must not be a common password

## Security Features

### Authentication

- Session-based authentication with Sanctum
- HTTP-only cookies for security
- Session regeneration on login
- CSRF protection enabled

### Account Security

- Login attempt tracking with IP logging
- Automatic account lockout after failed attempts
- Strong password requirements with validation
- Password change enforcement capability

### Authorization

- Role-based access control (RBAC)
- Permission middleware on all admin routes
- User type isolation (student/instructor/admin)
- Dashboard access control

### Audit Logging

- Login attempt logging (success/failure/locked)
- Tracks IP addresses and user agents
- Database query error logging
- Security event logging

## Support

For support, please contact the development team or open an issue in the repository.

---

**Version**: 8.3.1  
**Laravel**: 12.0  
**Last Updated**: 2025-11-14
