# CAG Attendance System - Quick Start Guide

## ✅ What Was Fixed

### Login Issue Resolved
The infinite loading problem on login was caused by:
1. **Login form bug** - Loading state was triggered on button click instead of form submit, causing it to stick when validation failed
2. **Database connection** - MySQL service needs to be running (was not started)
3. **Field name consistency** - Ensured `employee_no` is used throughout
4. **Vite removal** - Removed npm/build dependencies as requested

All issues have been fixed and committed to your repository.

## 🚀 Quick Setup Instructions

### 1. Start MySQL Service (IMPORTANT!)

The login issue was caused by MySQL not running. Start it first:

```bash
# Ubuntu/Debian
sudo systemctl start mysql
# or
sudo service mysql start

# MacOS
brew services start mysql

# Windows
net start MySQL

# Verify MySQL is running
mysqladmin -u root -p ping
```

### 2. Run Automated Database Setup (Recommended)

We've provided a setup script that handles everything:

```bash
chmod +x setup-database.sh
./setup-database.sh
```

**OR** Follow Manual Setup:

### 3. Manual Database Setup

```bash
# 1. Create MySQL database
mysql -u root -p
CREATE DATABASE cag_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# 2. Run migrations and seeders
php artisan migrate:fresh --seed
```

### 4. Configure Environment

The `.env` file is already configured with:
- **DB_CONNECTION**: mysql
- **DB_DATABASE**: cag_attendance
- **DB_USERNAME**: root
- **DB_PASSWORD**: (empty - update if needed)

If you have a database password, edit `.env` and set `DB_PASSWORD`.

### 5. Verify Database Setup

After running the setup script or manual setup, you should have:
- ✅ All database tables (users, clients, shifts, schedules, qr_codes, etc.)
- ✅ 1 Admin user (EMP001 / admin123)
- ✅ 5 Instructors
- ✅ 3 Office Staff
- ✅ 10 Sample Clients
- ✅ 3 Shifts (Morning, Mid-Morning, Afternoon)
- ✅ 3 QR Codes

### 6. Set Permissions

```bash
# Set storage permissions
chmod -R 775 storage bootstrap/cache

# Create storage link for public files
php artisan storage:link
```

### 5. Start the Application

```bash
# Serve the application
php artisan serve
```

Visit: **http://localhost:8000**

## 🔑 Login Credentials

### Admin Account
- **Employee No:** EMP001
- **Email:** admin@caglobal.com
- **Password:** admin123

### Instructor Accounts
- **Employee No:** EMP002
- **Email:** john.anderson@caglobal.com
- **Password:** password123

- **Employee No:** EMP003
- **Email:** sarah.martinez@caglobal.com
- **Password:** password123

### Office Staff Account
- **Employee No:** EMP007
- **Email:** rebecca.williams@caglobal.com
- **Password:** password123

## 📋 Testing Checklist

### Admin Features
- [ ] Login as admin (EMP001 / admin123)
- [ ] View admin dashboard with statistics
- [ ] Navigate to Users management
- [ ] Create a new user
- [ ] Navigate to Clients management
- [ ] Create a new client
- [ ] Navigate to Schedules
- [ ] Create a schedule (assign instructor to client)
- [ ] Navigate to QR Codes
- [ ] Generate a new QR code
- [ ] Download QR code
- [ ] View Reports section

### Staff Features
- [ ] Logout from admin
- [ ] Login as instructor (EMP002 / password123)
- [ ] View staff dashboard
- [ ] Navigate to Mark Attendance
- [ ] Check camera permissions (for QR scanning)
- [ ] View Attendance History
- [ ] View My Schedule
- [ ] View My Clients (instructors only)

### QR Code Attendance
- [ ] Login as staff
- [ ] Go to Mark Attendance
- [ ] Allow camera access
- [ ] Scan a QR code (from admin panel)
- [ ] Verify attendance is recorded
- [ ] Check attendance in history

## 🐛 Common Issues & Solutions

### Issue: Login Keeps Loading Forever
**Root Cause:** MySQL service is not running (Connection refused error)

**Solution:**
```bash
# 1. Start MySQL service
sudo systemctl start mysql
# or
sudo service mysql start

# 2. Verify MySQL is running
mysqladmin -u root ping

# 3. Clear Laravel cache
php artisan config:clear
php artisan cache:clear

# 4. Test database connection
php artisan db:show
```

**If MySQL won't start:**
- Check if MySQL is installed: `mysql --version`
- Check MySQL logs: `sudo tail -f /var/log/mysql/error.log`
- Reinstall if needed: `sudo apt-get install --reinstall mysql-server`

### Issue: Database Connection Refused
**Symptoms:** Login button shows "Signing in..." forever, Laravel.log shows "Connection refused"

**Solution:**
1. Ensure MySQL is running (see above)
2. Check `.env` database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cag_attendance
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Test connection: `php artisan db:show`

### Issue: Migration Errors
**Solution:**
```bash
php artisan migrate:fresh --seed
```

### Issue: Permission Denied Errors
**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Issue: Assets Not Loading
**Solution:**
```bash
php artisan optimize:clear
```
**Note:** This system uses CDN-based libraries (Tailwind CSS, Alpine.js, Chart.js) - no npm build required!

### Issue: QR Scanner Not Working
**Solutions:**
- Use HTTPS (camera requires secure context) OR use localhost
- Check browser permissions for camera access
- Try Chrome or Firefox (recommended browsers)
- Ensure you're not blocking camera in browser settings

### Issue: Login Still Not Working
**Check:**
1. Database connection is working
2. Users table exists and has data
3. Employee number is exactly "EMP001" (case-sensitive)
4. Password is exactly "admin123"

**Debug:**
```bash
# Check if users exist
php artisan tinker
User::all();
```

## 📁 Project Structure

```
cag-attendance/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── ClientController.php
│   │   │   ├── ShiftController.php
│   │   │   ├── ScheduleController.php
│   │   │   ├── QrCodeController.php
│   │   │   └── ReportController.php
│   │   ├── Staff/          # Staff controllers
│   │   │   ├── DashboardController.php
│   │   │   └── AttendanceController.php
│   │   └── AuthController.php
│   ├── Models/             # Database models
│   └── Middleware/
│       └── CheckUserType.php
├── database/
│   ├── migrations/         # Database structure
│   └── seeders/           # Sample data
├── resources/views/       # Blade templates
│   ├── admin/            # Admin views
│   ├── staff/            # Staff views
│   ├── auth/             # Login pages
│   └── layouts/          # Layout templates
├── routes/web.php        # Application routes
└── public/               # Public assets
```

## 🎯 Feature Highlights

### For Administrators
✅ **Dashboard** - Real-time statistics and charts
✅ **User Management** - Full CRUD operations
✅ **Client Management** - Track all clients
✅ **Schedule Management** - Assign instructors to clients
✅ **QR Code System** - Generate attendance QR codes
✅ **Advanced Reports** - Export to Excel/PDF
✅ **Shift Management** - Define work shifts

### For Staff
✅ **Personal Dashboard** - Today's schedule and status
✅ **QR Code Scanning** - Mark attendance via camera
✅ **Manual Check-in** - Alternative attendance marking
✅ **Schedule View** - See upcoming assignments
✅ **Client List** - View assigned clients (instructors)
✅ **Attendance History** - Review past records

### Advanced Features
✅ **GPS Location Tracking** - Record check-in/check-out locations
✅ **IP Address Logging** - Security tracking
✅ **Work Duration** - Automatic time calculation
✅ **Conflict Detection** - Prevent double-booking
✅ **Audit Trail** - Complete attendance logs
✅ **Soft Deletes** - Safe data management

## 🔧 Additional Commands

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### View Routes
```bash
php artisan route:list
```

### Database Operations
```bash
# Reset and reseed database
php artisan migrate:fresh --seed

# Run only migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

## 📞 Need Help?

If you encounter any issues:

1. **Check Laravel logs** at `storage/logs/laravel.log`
2. **Check browser console** for JavaScript errors
3. **Verify database connection** in `.env`
4. **Clear all caches** with `php artisan optimize:clear`

## 🎉 You're All Set!

The CAG Attendance System is now ready to use. Start by logging in as admin and exploring all the features!

**Happy tracking! 🚀**
