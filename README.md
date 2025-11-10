# CAG Attendance System - Laravel

A comprehensive, modern attendance management system built with Laravel 11, featuring QR code-based attendance marking, schedule management, client assignments, and advanced reporting capabilities.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat-square&logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=flat-square&logo=tailwind-css)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpine.js)

## 🌟 Features

### Core Functionality
- **Multi-Role Authentication** - Admin, Instructor, and Office Staff roles
- **QR Code Attendance** - Camera-based QR code scanning for check-in/check-out
- **Manual Attendance** - Optional manual attendance marking
- **Schedule Management** - Assign instructors to clients with recurring schedules
- **Client Management** - Comprehensive client information and tracking
- **Shift Management** - Morning, Mid-Morning, and Afternoon shifts
- **Real-Time Dashboard** - Role-based dashboards with statistics
- **Advanced Reports** - Attendance reports with export to Excel/PDF
- **Leave Management** - Leave requests and approvals system
- **Audit Trail** - Complete attendance logging for accountability

### Modern UI/UX
- **Responsive Design** - Mobile-first, works on all devices
- **Elegant Interface** - Tailwind CSS with green/orange CA Global theme
- **Interactive Components** - Alpine.js for smooth interactions
- **Data Visualization** - Chart.js for attendance trends
- **QR Scanner** - Built-in camera scanning with html5-qrcode
- **Toast Notifications** - User-friendly feedback messages
- **Calendar View** - Schedule visualization

### Advanced Features
- **GPS Location Tracking** - Record check-in/check-out locations
- **IP Address Logging** - Track attendance from different locations
- **Work Duration Calculation** - Automatic calculation of work hours
- **Conflict Detection** - Prevent double-booking of staff
- **Soft Deletes** - Safe deletion with recovery options
- **Bulk Operations** - Efficient multi-record management
- **Search & Filters** - Powerful filtering on all data views
- **Statistical Analysis** - Comprehensive analytics and insights

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x & NPM
- MySQL >= 8.0 or MariaDB >= 10.3
- GD Library (for QR code generation)
- Camera access (for QR scanning on client devices)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd cag-attendance
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cag_attendance
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Create Database

```bash
# MySQL
mysql -u root -p
CREATE DATABASE cag_attendance;
exit;
```

### 7. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

This will create all necessary tables and seed the database with:
- 1 Admin user
- 5 Instructors
- 3 Office Staff
- 10 Sample Clients
- 3 Shifts (Morning, Mid-Morning, Afternoon)
- 3 QR Codes (Daily, Weekly, Permanent)

### 8. Storage Setup

```bash
# Create symbolic link for storage
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
```

### 9. Compile Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 10. Start Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 👤 Default Login Credentials

### Admin Account
- **Email:** admin@caglobal.com
- **Password:** admin123
- **Employee No:** EMP001

### Instructor Account
- **Email:** john.anderson@caglobal.com
- **Password:** password123
- **Employee No:** EMP002

### Office Staff Account
- **Email:** rebecca.williams@caglobal.com
- **Password:** password123
- **Employee No:** EMP007

## 📁 Project Structure

```
cag-attendance/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   ├── Staff/          # Staff controllers
│   │   │   └── AuthController.php
│   │   └── Middleware/
│   │       └── CheckUserType.php
│   └── Models/                 # Eloquent models
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── views/
│   │   ├── layouts/           # Layout templates
│   │   ├── admin/             # Admin views
│   │   ├── staff/             # Staff views
│   │   └── auth/              # Authentication views
│   ├── css/
│   │   └── app.css           # Tailwind CSS
│   └── js/
│       └── app.js            # Alpine.js & Chart.js
├── routes/
│   └── web.php               # Application routes
└── public/
    └── build/                # Compiled assets
```

## 🎯 Key Features Guide

### For Administrators

1. **Dashboard** - View overall statistics and recent activities
2. **User Management** - Create, edit, and manage staff accounts
3. **Client Management** - Manage client information and contacts
4. **Schedule Management** - Assign instructors to clients with shifts
5. **QR Code Generation** - Create QR codes for attendance marking
6. **Reports** - Generate attendance and schedule reports
7. **Export Data** - Export reports to Excel or PDF

### For Instructors

1. **Dashboard** - View personal schedule and attendance status
2. **Mark Attendance** - Check-in/check-out using QR code or manual entry
3. **My Schedule** - View assigned client visits
4. **My Clients** - See all assigned clients
5. **Attendance History** - Review past attendance records
6. **Leave Requests** - Submit leave applications

### For Office Staff

1. **Dashboard** - View personal attendance and schedules
2. **Mark Attendance** - Check-in/check-out for office work
3. **Attendance History** - Review attendance records
4. **Leave Requests** - Submit leave applications

## 📱 QR Code Attendance Flow

1. **Admin generates QR code** (daily/weekly/permanent)
2. **QR code is displayed** at office entrance or sent to instructors
3. **Staff opens attendance page** on their device
4. **Camera scans QR code** automatically
5. **System validates code** and records attendance
6. **Confirmation message** displayed with timestamp
7. **GPS location and IP** automatically logged

## 🔒 Security Features

- **Password Hashing** - Bcrypt encryption for all passwords
- **CSRF Protection** - Laravel CSRF tokens on all forms
- **Role-Based Access** - Middleware-protected routes
- **Session Management** - Secure session handling
- **SQL Injection Protection** - Eloquent ORM prevents SQL injection
- **XSS Protection** - Blade template escaping
- **Audit Logging** - All attendance actions logged
- **IP & Location Tracking** - Monitor attendance locations

## 📊 Database Schema

### Main Tables
- `users` - Staff members (Admin, Instructor, Office Staff)
- `clients` - Companies/clients visited by instructors
- `shifts` - Work shifts (Morning, Mid-Morning, Afternoon)
- `schedules` - Instructor assignments to clients
- `qr_codes` - Generated QR codes for attendance
- `attendances` - Attendance records with check-in/check-out
- `leave_requests` - Leave applications
- `announcements` - System announcements
- `attendance_logs` - Audit trail for all attendance actions

## 🛠️ Technologies Used

### Backend
- **Laravel 11** - PHP Framework
- **MySQL** - Database
- **SimpleSoftwareIO/simple-qrcode** - QR code generation
- **Spatie/laravel-permission** - Role management
- **Maatwebsite/Excel** - Excel export
- **Intervention/Image** - Image processing

### Frontend
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Chart.js** - Data visualization
- **html5-qrcode** - QR code scanner
- **Heroicons** - Icon library
- **Inter Font** - Typography

## 🎨 Customization

### Change Color Theme

Edit `resources/css/app.css` and update Tailwind configuration:

```css
/* Primary colors */
.bg-primary { @apply bg-green-500; }
.bg-secondary { @apply bg-orange-500; }
```

### Modify Shift Times

Edit shift times in the admin panel or update `ShiftSeeder.php`

### Add Custom Reports

Create new methods in `Admin/ReportController.php`

## 🐛 Troubleshooting

### QR Code Scanner Not Working
- Ensure HTTPS (camera requires secure context)
- Check browser permissions for camera access
- Try different browsers (Chrome/Firefox recommended)

### Migration Errors
```bash
php artisan migrate:fresh --seed
```

### Asset Not Loading
```bash
npm run build
php artisan optimize:clear
```

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 📈 Performance Optimization

- **Eager Loading** - Prevent N+1 queries
- **Caching** - Database query caching
- **Asset Optimization** - Minified CSS/JS
- **Database Indexing** - Optimized queries
- **Lazy Loading** - Load data on demand

## 🔄 Backup & Maintenance

### Database Backup
```bash
php artisan backup:run
```

### Clear Cache
```bash
php artisan optimize:clear
```

### Update Dependencies
```bash
composer update
npm update
```

## 👥 User Roles & Permissions

### Admin
- Full system access
- User management
- Client management
- Schedule management
- QR code generation
- Reports and analytics
- System settings

### Instructor
- Personal dashboard
- Mark attendance (QR/manual)
- View assigned schedules
- View assigned clients
- Submit leave requests
- View attendance history

### Office Staff
- Personal dashboard
- Mark attendance (QR/manual)
- View personal schedule
- Submit leave requests
- View attendance history

## 📞 Support & Documentation

For issues and questions, please refer to:
- Laravel Documentation: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com
- Alpine.js: https://alpinejs.dev

## 📄 License

This project is proprietary software developed for CA Global.

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS Team
- Alpine.js Community
- SimpleSoftwareIO QR Code Package
- All open-source contributors

---

**Built with ❤️ for CA Global**

**Version:** 1.0.0
**Last Updated:** November 2025
