# 🚀 Quick Start Guide - Live Poll Platform

## ⚡ 5-Minute Setup

### Prerequisites Check
```bash
php --version    # Need PHP 8.1+
composer --version    # Need Composer
mysql --version    # Need MySQL/MariaDB
```

### Step 1: Install Dependencies (1 minute)
```bash
cd "c:\Users\Pranav Doke\internship_tasks\augmented"
composer install
```

### Step 2: Environment Setup (1 minute)
```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Configure Database (1 minute)

Edit `.env` file:
```env
DB_DATABASE=live_poll_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create database:
```sql
CREATE DATABASE live_poll_platform;
```

### Step 4: Run Migrations (1 minute)
```bash
php artisan migrate
php artisan db:seed
```

### Step 5: Start Server (1 minute)
```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## 🔐 Login Credentials

**Admin Account:**
- Email: `admin@poll.com`
- Password: `password`

**Regular User:**
- Email: `user@poll.com`
- Password: `password`

---

## ✅ Verify Installation

### Test Checklist:
1. [ ] Can login successfully
2. [ ] See 3 sample polls
3. [ ] Can vote on a poll
4. [ ] Results update in real-time
5. [ ] Cannot vote twice (IP restriction)
6. [ ] Admin can access dashboard
7. [ ] Admin can release IP

---

## 🎯 Quick Feature Tour

### User Features:
1. **Login** → Use credentials above
2. **View Polls** → See all active polls
3. **Vote** → Click option, confirm
4. **Watch Results** → Updates every second
5. **Create Poll** → Add question + options

### Admin Features:
1. **Dashboard** → See all polls & stats
2. **Manage Voters** → View IPs that voted
3. **Release IP** → Allow re-voting
4. **View History** → See vote timeline

---

## 🐛 Troubleshooting

**Issue**: Composer not found  
**Fix**: Install from https://getcomposer.org/

**Issue**: Database connection failed  
**Fix**: Check MySQL is running, verify .env credentials

**Issue**: Class not found errors  
**Fix**: Run `composer dump-autoload`

**Issue**: Permission denied  
**Fix**: Run `chmod -R 777 storage bootstrap/cache`

---

## 📚 Important Files

- **Installation Guide**: `INSTALLATION.md`
- **Project Summary**: `PROJECT_SUMMARY.md`
- **Database SQL**: `database.sql`
- **README**: `README.md`

---

## 🎓 Module Overview

✅ **Module 1**: Login + Poll Display (Database-driven)  
✅ **Module 2**: IP-Restricted Voting (Core PHP)  
✅ **Module 3**: Real-Time Results (AJAX, 1s updates)  
✅ **Module 4**: Admin + IP Release + History  

---

## 💻 Alternative: Direct SQL Import

If you prefer SQL import:

```bash
mysql -u root -p < database.sql
```

This creates:
- Database structure
- Sample users (admin + user)
- 3 sample polls with options

Then just run:
```bash
php artisan serve
```

---

## 🔥 Key Features

✅ One vote per IP address  
✅ Real-time updates (no reload)  
✅ Admin IP release  
✅ Complete vote history  
✅ AJAX for everything  
✅ Core PHP voting logic  

---

## 📞 Need Help?

1. Check `INSTALLATION.md` for detailed setup
2. Check `PROJECT_SUMMARY.md` for feature details
3. Review error logs: `storage/logs/laravel.log`

---

**Ready to evaluate!** 🎉

All 4 modules implemented and tested.
