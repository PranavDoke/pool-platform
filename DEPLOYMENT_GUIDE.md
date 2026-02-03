# 🚀 Live Deployment Guide - Get Your Project Online

## Deploy Your Laravel Poll Platform with Free Live URL

---

## ⚡ Quick Comparison - Choose Your Platform

| Platform | Setup Time | Free Plan | MySQL | Custom Domain | Best For |
|----------|-----------|-----------|-------|---------------|----------|
| **Railway.app** | 5 min | ✅ Yes | ✅ Included | ✅ Yes | Easiest, Recommended |
| **Render.com** | 10 min | ✅ Yes | ✅ Included | ✅ Yes | Professional |
| **InfinityFree** | 15 min | ✅ Yes | ✅ Included | ✅ Yes | Traditional hosting |
| **000webhost** | 10 min | ✅ Yes | ✅ Included | ⚠️ Subdomain | Quick setup |

---

## 🏆 RECOMMENDED: Railway.app (Fastest & Easiest)

**Why Railway?**
- ✅ Free $5/month credit (enough for small projects)
- ✅ Automatic PHP/MySQL setup
- ✅ GitHub integration (auto-deploy)
- ✅ Live URL in 5 minutes
- ✅ SSL certificate included

### Step-by-Step Deployment:

#### 1. Prepare Your Project for Deployment

First, let's create the necessary deployment files:

**A. Create Procfile** (tells Railway how to start your app):
```bash
cd c:\Users\Pranav Doke\internship_tasks\augmented
echo web: php artisan serve --host=0.0.0.0 --port=$PORT > Procfile
```

**B. Create railway.json** (Railway configuration):
Create file: `railway.json`
```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

**C. Create nixpacks.toml** (Build configuration):
Create file: `nixpacks.toml`
```toml
[phases.setup]
nixPkgs = ['php82', 'php82Extensions.mbstring', 'php82Extensions.pdo', 'php82Extensions.pdo_mysql']

[phases.install]
cmds = ['composer install --no-dev --optimize-autoloader']

[phases.build]
cmds = ['php artisan config:cache', 'php artisan route:cache', 'php artisan view:cache']

[start]
cmd = 'php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT'
```

**D. Update .env for production**:
You'll set these environment variables in Railway dashboard later.

#### 2. Push to GitHub

```bash
# Initialize git if not already done
cd c:\Users\Pranav Doke\internship_tasks\augmented
git init
git add .
git commit -m "Initial commit - Live Poll Platform"

# Create new GitHub repository at https://github.com/new
# Then push:
git remote add origin https://github.com/YOUR_USERNAME/poll-platform.git
git branch -M main
git push -u origin main
```

#### 3. Deploy on Railway

1. **Sign up**: Go to https://railway.app/ and sign up with GitHub
2. **New Project**: Click "New Project" → "Deploy from GitHub repo"
3. **Select Repository**: Choose your `poll-platform` repository
4. **Add MySQL**: Click "New" → "Database" → "Add MySQL"
5. **Configure Environment Variables**:
   - Click your project → "Variables" → Add these:
   ```
   APP_NAME=LivePollPlatform
   APP_ENV=production
   APP_KEY=base64:YOUR_KEY_HERE
   APP_DEBUG=false
   APP_URL=https://your-app.railway.app
   
   DB_CONNECTION=mysql
   DB_HOST=${{MYSQL.RAILWAY_TCP_PROXY_DOMAIN}}
   DB_PORT=${{MYSQL.RAILWAY_TCP_PROXY_PORT}}
   DB_DATABASE=${{MYSQL.MYSQLDB_DATABASE}}
   DB_USERNAME=${{MYSQL.MYSQLUSER}}
   DB_PASSWORD=${{MYSQL.MYSQLPASSWORD}}
   
   SESSION_DRIVER=database
   CACHE_DRIVER=file
   ```

6. **Generate APP_KEY**:
   - Run locally: `php artisan key:generate --show`
   - Copy the output and paste as APP_KEY

7. **Deploy**: Railway will automatically build and deploy!

8. **Get Your Live URL**:
   - Click "Settings" → "Domains" → "Generate Domain"
   - Your URL: `https://your-project.railway.app`

#### 4. Import Database

After deployment, run migrations:
- Go to Railway dashboard → Your project → "Deployments"
- Click latest deployment → "View Logs"
- Migrations run automatically from nixpacks.toml

Or manually seed data:
1. Click MySQL service → "Connect"
2. Use provided credentials with any MySQL client
3. Import `database.sql`

**Your live URL is ready!** 🎉

---

## 🌟 Alternative 1: Render.com (Professional Option)

**Why Render?**
- ✅ Free tier with 750 hours/month
- ✅ PostgreSQL or MySQL included
- ✅ Auto-deploy from GitHub
- ✅ Great for portfolios

### Deployment Steps:

#### 1. Prepare Project Files

**Create render.yaml**:
```yaml
services:
  - type: web
    name: poll-platform
    env: php
    buildCommand: composer install --no-dev && php artisan migrate --force
    startCommand: php artisan serve --host=0.0.0.0 --port=$PORT
    envVars:
      - key: APP_KEY
        generateValue: true
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: DATABASE_URL
        fromDatabase:
          name: poll-db
          property: connectionString

databases:
  - name: poll-db
    databaseName: live_poll_platform
    user: poll_user
```

#### 2. Push to GitHub (same as Railway)

#### 3. Deploy on Render

1. Go to https://render.com/ and sign up
2. Click "New +" → "Blueprint"
3. Connect GitHub repository
4. Render auto-detects `render.yaml`
5. Click "Apply"
6. Wait 5-10 minutes for deployment

**Live URL**: `https://poll-platform.onrender.com`

---

## 🌐 Alternative 2: InfinityFree (Traditional Hosting)

**Why InfinityFree?**
- ✅ 100% free forever
- ✅ Unlimited bandwidth
- ✅ MySQL databases
- ✅ cPanel access
- ✅ Custom domains

### Deployment Steps:

#### 1. Sign Up
- Go to https://infinityfree.com/
- Click "Sign Up"
- Choose subdomain: `yourname.epizy.com`

#### 2. Prepare Project for Upload

Create `.htaccess` in project root:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Update `public/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### 3. Upload Files

**Option A: File Manager** (Easier)
1. Login to cPanel
2. Click "File Manager"
3. Navigate to `htdocs`
4. Upload all project files

**Option B: FTP** (Faster for large projects)
1. Use FileZilla (download from https://filezilla-project.org/)
2. Get FTP credentials from InfinityFree control panel
3. Connect and upload all files to `htdocs`

#### 4. Create Database

1. In cPanel → "MySQL Databases"
2. Create database: `epiz_XXXXX_poll`
3. Create user and note credentials
4. Import `database.sql`:
   - Go to phpMyAdmin
   - Select your database
   - Click "Import"
   - Upload `database.sql`

#### 5. Configure .env

Edit `.env` file via File Manager:
```env
APP_URL=http://yourname.epizy.com

DB_CONNECTION=mysql
DB_HOST=sqlXXX.epizy.com
DB_PORT=3306
DB_DATABASE=epiz_XXXXX_poll
DB_USERNAME=epiz_XXXXX
DB_PASSWORD=your_password

SESSION_DRIVER=file
```

#### 6. Set Permissions

In File Manager, set permissions:
- `storage/` → 755 (recursive)
- `bootstrap/cache/` → 755 (recursive)

**Live URL**: `http://yourname.epizy.com` 🎉

---

## 🎯 Alternative 3: 000webhost (Quick Setup)

**Why 000webhost?**
- ✅ Instant setup
- ✅ 300 MB storage
- ✅ MySQL included
- ✅ PHP 8.2 support

### Deployment Steps:

1. **Sign Up**: https://www.000webhost.com/
2. **Create Website**: Choose subdomain `yourname.000webhostapp.com`
3. **Upload Files**: Use File Manager (same as InfinityFree)
4. **Create Database**: In dashboard → "Database"
5. **Import SQL**: Via phpMyAdmin
6. **Configure .env**: Update database credentials

**Live URL**: `https://yourname.000webhostapp.com`

---

## 📋 Pre-Deployment Checklist

Before deploying, ensure:

### ✅ Environment Configuration
```bash
# Update .env.example with production settings
APP_ENV=production
APP_DEBUG=false
APP_URL=your-live-url

# Security
SESSION_DRIVER=database  # Important for Laravel
CACHE_DRIVER=file
```

### ✅ Database Ready
- [ ] `database.sql` file exists
- [ ] Sample data included (admin user)
- [ ] Migrations tested locally

### ✅ Files Ready
- [ ] `Procfile` created (for Railway/Render)
- [ ] `.htaccess` configured (for traditional hosting)
- [ ] `composer.json` has all dependencies
- [ ] No sensitive data in `.env`

### ✅ Code Optimizations
Run these before deploying:
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔧 Post-Deployment Setup

After deployment, you need to:

### 1. Test Your Live Site

Visit your URL: `https://your-site.com`

**Test these features:**
- ✅ Login page loads
- ✅ Admin login works (admin@poll.com / password)
- ✅ Poll listing shows
- ✅ Voting works with IP restriction
- ✅ Real-time updates working
- ✅ Admin can view voters
- ✅ Vote rollback functions

### 2. Create Admin Account

If database seeder didn't run:
```bash
# Via hosting provider's terminal or SSH
php artisan tinker

# Then run:
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@poll.com',
    'password' => bcrypt('password'),
    'is_admin' => true
]);
```

### 3. Security Checks

- [ ] APP_DEBUG=false in production
- [ ] APP_KEY is set (unique)
- [ ] HTTPS enabled (most free hosts provide this)
- [ ] Database credentials secure

---

## 🐛 Common Deployment Issues & Fixes

### Issue 1: "500 Internal Server Error"

**Fix**:
```bash
# Check storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Clear caches
php artisan config:clear
php artisan cache:clear
```

### Issue 2: "Database connection failed"

**Fix**:
- Verify DB credentials in `.env`
- Check DB host (might be different in production)
- Ensure database exists
- Test connection via phpMyAdmin

### Issue 3: "Class 'Facade\Ignition\...' not found"

**Fix**:
Update `composer.json`:
```json
{
  "require": {
    "facade/ignition": "^2.0"
  }
}
```
Run: `composer update`

### Issue 4: "CSRF token mismatch"

**Fix**:
Update `.env`:
```env
SESSION_DRIVER=database
SESSION_DOMAIN=.yourdomain.com
```

### Issue 5: Assets not loading (CSS/JS)

**Fix**:
Update `.env`:
```env
ASSET_URL=https://your-domain.com
```

In `config/app.php`, ensure:
```php
'asset_url' => env('ASSET_URL', null),
```

---

## 📊 My Recommendation for Your Internship

### 🏆 Best Option: Railway.app

**Why?**
1. ✅ **Fastest deployment**: 5 minutes from GitHub to live URL
2. ✅ **Professional appearance**: `https://poll-platform-production.railway.app`
3. ✅ **Easy to demonstrate**: Just share the link
4. ✅ **Auto-deploys**: Push to GitHub → automatically updates
5. ✅ **Reliable**: 99.9% uptime
6. ✅ **Free**: $5/month credit is enough

### 📝 For Submission

Include this in your submission:

```
Live Demo URL: https://your-project.railway.app
Admin Login: admin@poll.com / password
Test User: user@poll.com / password

GitHub Repository: https://github.com/yourname/poll-platform

Key Features Demonstrated:
✅ Module 1: Authentication & Poll Display
✅ Module 2: IP-Restricted Voting (Core PHP)
✅ Module 3: Real-Time Results (AJAX updates)
✅ Module 4: Admin Dashboard & Vote Rollback

Technology Stack:
- Laravel 11.x (MVC Framework)
- Core PHP (Voting Services)
- MySQL (Database)
- AJAX/jQuery (Real-time updates)
- Bootstrap 5 (Responsive UI)
```

---

## 🚀 Quick Start Commands

### For Railway/Render Deployment:

```bash
# 1. Navigate to project
cd "c:\Users\Pranav Doke\internship_tasks\augmented"

# 2. Create deployment files (I'll create these for you)

# 3. Initialize Git
git init
git add .
git commit -m "Live Poll Platform - Ready for deployment"

# 4. Create GitHub repo and push
# (Go to github.com/new)
git remote add origin https://github.com/YOUR_USERNAME/poll-platform.git
git branch -M main
git push -u origin main

# 5. Connect Railway to GitHub and deploy!
```

### For Traditional Hosting (InfinityFree/000webhost):

```bash
# 1. Create .htaccess files (I'll create these)
# 2. Zip the project
# 3. Upload via File Manager or FTP
# 4. Extract on server
# 5. Import database.sql via phpMyAdmin
# 6. Update .env with hosting credentials
```

---

## ✅ Next Steps

**Choose your deployment method**:

1. **Want fastest/easiest?** → Use Railway.app (recommended)
2. **Want professional portfolio hosting?** → Use Render.com
3. **Want traditional hosting experience?** → Use InfinityFree
4. **Want quick and simple?** → Use 000webhost

**Tell me which option you prefer, and I'll help you deploy step-by-step!**

---

## 📞 Need Help?

Common questions:

**Q: Which is really the fastest?**
A: Railway.app - 5 minutes total if you have GitHub account

**Q: Which looks most professional?**
A: Render.com or Railway.app (both give clean URLs)

**Q: Which is easiest without command line?**
A: InfinityFree or 000webhost (uses web interface)

**Q: Will it really be free?**
A: Yes! All options have generous free tiers

**Q: How long will it stay online?**
A: Permanently (as long as you don't violate usage limits)

---

Ready to deploy? Let me know which platform you choose! 🚀
