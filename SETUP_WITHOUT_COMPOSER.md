# 🚀 Setup Without Composer - Alternative Installation Methods

## Option 1: Using XAMPP/WAMP (Recommended for Windows)

If you don't have Composer installed, the easiest way to run this Laravel project is using XAMPP or WAMP.

### Step 1: Install XAMPP
1. Download XAMPP from: https://www.apachefriends.org/
2. Install with PHP 8.1+ and MySQL
3. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Import Database Directly
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create new database: `live_poll_platform`
3. Import the provided `database.sql` file:
   - Click on `live_poll_platform` database
   - Go to "Import" tab
   - Choose file: `database.sql`
   - Click "Go"

### Step 3: Configure Application
1. Copy the project to XAMPP's htdocs folder:
   ```
   Copy: c:\Users\Pranav Doke\internship_tasks\augmented
   To: C:\xampp\htdocs\poll-platform
   ```

2. Create `.env` file by copying `.env.example`:
   ```cmd
   cd C:\xampp\htdocs\poll-platform
   copy .env.example .env
   ```

3. Edit `.env` file with Notepad and update:
   ```env
   APP_NAME="Live Poll Platform"
   APP_ENV=local
   APP_KEY=base64:YourRandomKeyHere123456789012345678901234567890=
   APP_DEBUG=true
   APP_URL=http://localhost/poll-platform/public

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=live_poll_platform
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Step 4: Access the Application
Open browser and go to: **http://localhost/poll-platform/public**

**Login with**:
- Admin: `admin@poll.com` / `password`
- User: `user@poll.com` / `password`

---

## Option 2: Install Composer (For Full Laravel Experience)

### Install Composer on Windows

1. **Download Composer Installer**:
   - Visit: https://getcomposer.org/download/
   - Click "Composer-Setup.exe" (Windows Installer)

2. **Run Installer**:
   - Double-click the downloaded file
   - Follow the installation wizard
   - It will automatically detect your PHP installation
   - Click "Install"

3. **Verify Installation**:
   ```cmd
   composer --version
   ```

4. **Then Install Project Dependencies**:
   ```cmd
   cd "c:\Users\Pranav Doke\internship_tasks\augmented"
   composer install
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   php artisan serve
   ```

---

## Option 3: Manual PHP Setup (No XAMPP, No Composer)

### Requirements
- PHP 8.1+ installed
- MySQL installed

### Step 1: Install PHP (if not installed)

1. Download PHP 8.1+ from: https://windows.php.net/download/
2. Extract to `C:\php`
3. Add to PATH:
   - Right-click "This PC" → Properties
   - Advanced System Settings → Environment Variables
   - Edit PATH, add: `C:\php`

### Step 2: Install MySQL (if not installed)

1. Download MySQL from: https://dev.mysql.com/downloads/installer/
2. Install MySQL Server
3. Set root password during installation

### Step 3: Import Database

Using MySQL Command Line:
```cmd
mysql -u root -p < "c:\Users\Pranav Doke\internship_tasks\augmented\database.sql"
```

Or use MySQL Workbench to import `database.sql`

### Step 4: Configure and Run

1. Create `.env` file:
   ```cmd
   cd "c:\Users\Pranav Doke\internship_tasks\augmented"
   copy .env.example .env
   ```

2. Edit `.env` with your database credentials

3. **If you have Composer**:
   ```cmd
   composer install
   php artisan key:generate
   php artisan serve
   ```

4. **If NO Composer** (Manual approach):
   - You'll need to download Laravel's vendor dependencies manually
   - This is complex - **XAMPP/WAMP is recommended instead**

---

## 🎯 Recommended Solution for You

Since you don't have Composer, **use Option 1 (XAMPP)**:

### Quick Steps:
1. Install XAMPP (includes PHP + MySQL)
2. Copy project to `C:\xampp\htdocs\poll-platform`
3. Import `database.sql` via phpMyAdmin
4. Copy `.env.example` to `.env`
5. Visit: `http://localhost/poll-platform/public`

**This works WITHOUT Composer!**

---

## 📁 Direct Database Access

If you just want to see the data structure, you can:

1. Open `database.sql` in any text editor
2. The file contains:
   - All table structures
   - Sample users (admin@poll.com / user@poll.com)
   - 3 sample polls with options
   - Complete SQL schema

---

## 🔧 Troubleshooting

### "PHP is not recognized"
- Install XAMPP which includes PHP
- Or download PHP and add to PATH

### "MySQL connection failed"
- Start MySQL from XAMPP Control Panel
- Or ensure MySQL service is running

### "Cannot access application"
- Make sure Apache is running (XAMPP)
- Check URL: http://localhost/poll-platform/public
- Verify .env database credentials

---

## 💡 Important Notes

### Without Composer, You Get:
✅ Full database structure  
✅ All source code files  
✅ Complete documentation  
✅ Sample data  

### With Composer, You Also Get:
✅ Laravel dependencies installed  
✅ Artisan commands working  
✅ Full development environment  
✅ Better error handling  

**For internship evaluation viewing**, XAMPP without Composer is sufficient!

---

## 🎓 Alternative: Online Demo

If you want to skip local setup entirely:

### Option A: Use PHP Built-in Server (Still needs PHP)
```cmd
php -S localhost:8000 -t public
```

### Option B: Review Code & Documentation
- All source code is in the project folders
- Read `PROJECT_SUMMARY.md` for complete overview
- Check `ARCHITECTURE.md` for system design
- Review individual PHP files to see implementation

---

## 📞 Need Help?

**Easiest Setup**: Install XAMPP → Import database.sql → Access via localhost

**Best Setup**: Install Composer → Run `composer install` → Use artisan commands

**Quick Review**: Read documentation files → Review source code → Check database.sql

---

Choose the option that works best for your current system setup!
