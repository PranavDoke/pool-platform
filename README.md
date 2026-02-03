# Real-Time Live Poll Platform

A Laravel-based polling platform with IP restriction, real-time updates via AJAX, and admin moderation capabilities.

---

## 📚 **Quick Navigation**

- 🚀 **[QUICKSTART.md](QUICKSTART.md)** - 5-minute setup guide (Start Here!)
- 📋 **[INSTALLATION.md](INSTALLATION.md)** - Detailed installation instructions
- 📊 **[PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)** - Complete project report
- ✅ **[CHECKLIST.md](CHECKLIST.md)** - Verification checklist
- 📖 **[INDEX.md](INDEX.md)** - Full documentation index

---

## Features

- **Authentication**: Login-only authentication system
- **IP-Based Voting**: One vote per IP address per poll
- **Real-Time Results**: Poll results update every second via AJAX (no page reload)
- **Admin Moderation**: Release IP addresses and view vote history
- **Vote Audit Trail**: Complete history of all voting actions

## Technology Stack

- **Backend**: Laravel 11.x + Core PHP (for voting logic)
- **Frontend**: HTML, CSS, Bootstrap, JavaScript, jQuery, AJAX
- **Database**: MySQL
- **Real-Time**: AJAX polling (1-second interval)

## Modules

### Module 1: Authentication & Poll Display (2 hours)
- Basic login authentication
- Poll creation with question, multiple options, status
- Display active polls list
- View poll details with voting options

### Module 2: IP-Restricted Voting
- Core PHP voting logic
- IP address validation and tracking
- One vote per IP per poll enforcement
- Vote submission via AJAX

### Module 3: Real-Time Poll Results
- Automatic result updates every ~1 second
- Live vote count per option
- No page reload required

### Module 4: Admin Features
- View IPs that voted on a poll
- Release IP addresses (vote rollback)
- Re-voting capability after IP release
- Vote history tracking (original → released → new)

## Installation

1. Configure database in `.env` file
2. Run migrations: `php artisan migrate`
3. Seed sample data: `php artisan db:seed`
4. Start server: `php artisan serve`
5. Access: `http://localhost:8000`

## Database Schema

- `users` - User accounts
- `polls` - Poll questions with status
- `poll_options` - Multiple choice options per poll
- `votes` - Active votes with IP tracking
- `vote_history` - Complete audit trail of all vote actions

## Requirements Met

✅ Only authenticated users can access polls  
✅ One vote per IP address  
✅ Real-time updates without page reload  
✅ Admin can release IP and remove votes  
✅ Previous vote history visible to admin after re-voting  
✅ No hardcoded poll content (database-driven)  
✅ Core PHP for voting logic, IP validation, rollback  
✅ AJAX for all interactions (voting, results, navigation)
