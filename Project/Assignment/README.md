# Premium PHP 8+ MVC Auth Module (Email API Driven)

A highly secure, production-ready, full-stack MVC authentication and authorization system utilizing **PHP 8.5**, **MySQL**, **Bootstrap 5**, **JavaScript**, **AJAX**, and a high-performance **HTTP Email API (Resend.com)**.

---

## 📁 Key Directory Structure

```
├── assets/
│   ├── css/
│   │   └── style.css          # Ambient gradients, glassmorphism cards, micro-animations
│   └── js/
│       └── app.js             # AJAX fetch pipelines, live strength meters, toasted alerts
├── config/
│   ├── Database.php           # PDO connection manager (Dynamic PHP 8.5 warning patches)
│   └── config.php             # Core configs, security headers, secure session configurations
├── controllers/
│   ├── BaseController.php     # Sanitization (XSS mitigation), views loader, JSON APIs
│   ├── AuthController.php     # Registration & Login logic, BCRYPT checks, remember-me
│   ├── DashboardController.php# Role-based dashboard loaders, verified state and role updates
│   ├── PasswordResetController.php # Forgot OTP generation, reset verification
│   └── ProfileController.php  # Display name edits, secure password updates
├── database/
│   ├── schema.sql             # SQL table definitions (users, email_otps, password_resets)
│   └── migrate.php            # Automated CLI setup and Admin/User account seeders
├── middleware/
│   ├── AuthMiddleware.php     # Session authentication guards
│   ├── RoleMiddleware.php     # RBAC (Admin/User) guards
│   └── CSRFMiddleware.php     # State-modifying POST request token validation checks
├── services/
│   ├── EmailService.php       # Resend HTTP API client (Simulations capability integrated!)
│   └── SessionService.php     # Secure session, token rotation, remember-me cookies
├── views/
│   ├── auth/                  # Login, register, verify, forgot-password views
│   ├── dashboard/             # Role dashboards (user.php panel, admin.php console)
│   ├── errors/                # Themed 403 Forbidden and 404 Not Found error views
│   └── layout/                # Global layout wrappers (header.php, footer.php)
├── logs/                      # PHP system & OTP logs (.gitkeep protected)
├── uploads/                   # User attachments (.gitkeep protected)
├── .env                       # Environment credentials (git-ignored)
└── index.php                  # Front Controller, router, and static parser
```

---

## 🚀 Installation & Setup Guide

### 1. Prerequisites
* **PHP 8.0+** (PHP 8.5.6 is recommended)
* **MySQL 5.7+** or **MariaDB**
* **Composer** (optional, PHPMailer autoload supported if present, though cURL Email API has zero dependencies!)

---

### 2. Database Migration & Initialization
1. Ensure your local MySQL server is started (via Brew: `brew services start mysql`).
2. Run the automated database migration script in your terminal:
   ```bash
   php database/migrate.php
   ```
   This will:
   * Drop any existing `auth_system` development database.
   * Re-create the database `auth_system` with our clean schema: `users`, `email_otps`, and `password_resets`.
   * Seed pre-verified **Admin** and **User** accounts to let you log in instantly:
     * **1. ADMIN ACCOUNT:**
       * **Email**: `admin@gmail.com`
       * **Password**: `AdminPassword123!`
     * **2. USER ACCOUNT:**
       * **Email**: `user@gmail.com`
       * **Password**: `UserPassword123!`

---

### 3. Running the Application Locally
To launch the application using PHP's built-in web server:
1. Navigate to the root directory in your terminal and run:
   ```bash
   php -S localhost:8000
   ```
2. Open your web browser and navigate to `http://localhost:8000`.

---

### 📧 Testing OTPs instantly (Simulation Mode)
By default, the local `.env` file has `EMAIL_API_KEY=simulate`.
* **No setup required!** When you register a new account or trigger a forgot password recovery, **the system will instantly write the 6-digit OTP codes to the local log file `logs/otp.log`**.
* Simply open `logs/otp.log` in your editor, copy the generated code, and paste it into the browser input field!

---

### 🌐 Live Email API Setup (Resend.com)
To enable real-time live email OTP deliveries to actual Gmail boxes:
1. Go to [Resend.com](https://resend.com) and create a free account (takes 30 seconds).
2. Copy your free **API Key** (starts with `re_...`).
3. Open your [.env](file:///Users/rambhusingh/Desktop/Intern_work/.env) file and update:
   ```env
   EMAIL_API_KEY=your_actual_resend_api_key_here
   EMAIL_FROM_ADDRESS=onboarding@resend.dev # Or your verified custom domain
   ```
4. Save the file. All OTP codes will now be dispatched in real time to actual Gmail addresses!
