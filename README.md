# VELTRIXO Freelancer Portal - Version 1

## Included
- Freelancer registration
- Secure password hashing
- Freelancer login/logout
- Session authentication
- MySQL database
- Freelancer dashboard
- Basic project listing
- Application database foundation

## Requirements
- PHP 8.0+
- MySQL 5.7+/8.x or MariaDB
- Apache/Nginx (XAMPP, WAMP, Laragon or hosting)

## Installation

1. Copy the `veltrixo_freelancer_portal` folder into your server web directory.
2. Create/import the database:
   - Open phpMyAdmin.
   - Import `database.sql`.
3. Edit `config/database.php`:
   - `$host`
   - `$db`
   - `$user`
   - `$pass`
4. Open:
   `http://localhost/veltrixo_freelancer_portal/register.php`
5. Register a freelancer.
6. You will be redirected to the dashboard.

## Production security
Before public launch:
- Use HTTPS.
- Move database credentials into environment variables.
- Add CSRF protection to all POST forms.
- Add email/OTP verification.
- Add login rate limiting.
- Add secure file-upload validation before enabling resume/photo uploads.
- Set new accounts to `pending` and require admin approval.
- Configure secure session cookies.
- Add password reset using verified email.
- Add server-side authorization checks to every protected page.

## Next modules
- Freelancer profile editing
- Project application workflow
- Client registration/login
- Admin panel
- Freelancer approval
- Messaging
- Payments/earnings
- Notifications
- Resume/profile photo uploads
