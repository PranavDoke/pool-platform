# 🏗️ System Architecture Diagram

## Real-Time Live Poll Platform Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER INTERFACE                          │
│                    (Browser - Frontend)                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐           │
│  │   Login     │  │ Polls List  │  │ Poll Detail │           │
│  │   Page      │  │    Page     │  │    Page     │           │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘           │
│         │                │                │                    │
│         └────────────────┴────────────────┘                    │
│                          │                                      │
│                    Bootstrap 5                                  │
│                    jQuery + AJAX                                │
│                          │                                      │
└──────────────────────────┼──────────────────────────────────────┘
                           │
                    AJAX Requests
                    (Every 1 second)
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                    LARAVEL APPLICATION                          │
│                   (Backend - Server)                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────── ROUTES ───────────┐                            │
│  │ web.php        │   api.php     │                            │
│  │ (Web Routes)   │ (AJAX Routes) │                            │
│  └────────┬───────┴───────┬───────┘                            │
│           │               │                                     │
│           ▼               ▼                                     │
│  ┌────────────────────────────────┐                            │
│  │        CONTROLLERS             │                            │
│  ├────────────────────────────────┤                            │
│  │ • LoginController              │                            │
│  │ • PollController               │                            │
│  │ • VoteController               │                            │
│  │ • AdminController              │                            │
│  └─────────┬──────────────────────┘                            │
│            │                                                    │
│            ▼                                                    │
│  ┌────────────────────────────────┐                            │
│  │    CORE PHP SERVICES           │ ← CORE PHP LOGIC          │
│  ├────────────────────────────────┤                            │
│  │ • VotingService.php            │   (Raw SQL queries)       │
│  │   - hasIpVoted()               │   (IP validation)         │
│  │   - processVote()              │   (Custom logic)          │
│  │                                │                            │
│  │ • IpValidationService.php      │                            │
│  │   - getClientIp()              │   Uses $_SERVER           │
│  │   - validateIp()               │   Uses filter_var()       │
│  │                                │                            │
│  │ • VoteRollbackService.php      │                            │
│  │   - releaseIpVote()            │                            │
│  │   - getIpVoteHistory()         │                            │
│  └─────────┬──────────────────────┘                            │
│            │                                                    │
│            ▼                                                    │
│  ┌────────────────────────────────┐                            │
│  │         MODELS                 │                            │
│  ├────────────────────────────────┤                            │
│  │ • User                         │                            │
│  │ • Poll                         │                            │
│  │ • PollOption                   │                            │
│  │ • Vote                         │                            │
│  │ • VoteHistory                  │                            │
│  └─────────┬──────────────────────┘                            │
│            │                                                    │
└────────────┼────────────────────────────────────────────────────┘
             │
             │ Eloquent ORM
             │
             ▼
┌─────────────────────────────────────────────────────────────────┐
│                       MYSQL DATABASE                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────┐  ┌──────────┐  ┌──────────────┐                │
│  │  users   │  │  polls   │  │ poll_options │                │
│  └────┬─────┘  └────┬─────┘  └──────┬───────┘                │
│       │             │               │                          │
│       │             │               │                          │
│  ┌────┴─────────────┴───────────────┴───────┐                │
│  │              votes                       │                │
│  │  ┌──────────────────────────────────┐   │                │
│  │  │ Unique: (poll_id, ip_address,    │   │                │
│  │  │          is_active)               │   │                │
│  │  └──────────────────────────────────┘   │                │
│  └──────────────┬───────────────────────────┘                │
│                 │                                              │
│  ┌──────────────┴────────────┐                               │
│  │      vote_history         │  (Audit Trail)                │
│  │  - All vote actions       │                               │
│  │  - Timestamps             │                               │
│  │  - Action types           │                               │
│  └───────────────────────────┘                               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Data Flow Diagrams

### 1️⃣ Vote Submission Flow

```
User Clicks Vote Button
         │
         ▼
JavaScript Confirms Vote
         │
         ▼
AJAX POST /api/polls/{id}/vote
         │
         ▼
VoteController::store()
         │
         ▼
IpValidationService::getClientIp()
         │
         ▼
IpValidationService::validateIp()
         │
         ▼
VotingService::processVote()
         │
         ├─► Check hasIpVoted() (Core PHP + Raw SQL)
         │   └─► If voted: Return error
         │
         ├─► Insert vote to database
         │
         ├─► Log to vote_history
         │
         └─► Return success
                │
                ▼
         JSON Response to Browser
                │
                ▼
         Update UI (no reload)
```

### 2️⃣ Real-Time Results Update Flow

```
Page Loads poll-voting.js
         │
         ▼
PollUpdater starts (every 1 second)
         │
         ▼
AJAX GET /api/polls/{id}/results
         │
         ▼
PollController::getResults()
         │
         ├─► Raw SQL Query
         │   SELECT option_text, COUNT(votes)
         │   FROM poll_options
         │   LEFT JOIN votes (is_active=1)
         │   GROUP BY option_text
         │
         └─► Return JSON
                │
                ▼
         Update DOM Elements
         ├─► Vote counts
         ├─► Percentages
         ├─► Progress bars
         └─► Timestamp
                │
                ▼
         Schedule next poll (1 second)
```

### 3️⃣ IP Release & Re-Voting Flow

```
Admin Clicks "Release IP"
         │
         ▼
AJAX POST /api/admin/polls/{id}/release-ip
         │
         ▼
AdminController::releaseIp()
         │
         ▼
VoteRollbackService::releaseIpVote()
         │
         ├─► Find active vote for IP
         │
         ├─► Set is_active = 0 (soft delete)
         │
         ├─► Create vote_history entry
         │   - action: "released"
         │   - metadata: previous vote info
         │
         └─► Return success
                │
                ▼
         IP can now vote again
                │
                ▼
    When IP votes again:
    - New vote created (is_active=1)
    - New vote_history entry
    - Admin sees timeline:
      [Original Vote] → [Released] → [New Vote]
```

---

## 🗄️ Database Relationships

```
users (1) ────────── (∞) polls
                          │
                          ├─────── (∞) poll_options
                          │              │
                          │              │
                          └─────── (∞) votes ────── (∞) vote_history
                                         │
                                         └─ Unique: (poll_id, ip_address, is_active)
```

---

## 🔐 Security Layers

```
┌─────────────────────────────────────────┐
│         USER REQUEST                    │
└───────────────┬─────────────────────────┘
                │
                ▼
┌───────────────────────────────────────────┐
│  Layer 1: Authentication Middleware       │
│  - Check if user is logged in             │
│  - Redirect to login if not               │
└───────────────┬───────────────────────────┘
                │
                ▼
┌───────────────────────────────────────────┐
│  Layer 2: CSRF Protection                 │
│  - Validate CSRF token                    │
│  - Reject if token mismatch               │
└───────────────┬───────────────────────────┘
                │
                ▼
┌───────────────────────────────────────────┐
│  Layer 3: IP Validation (Core PHP)        │
│  - Get IP from $_SERVER                   │
│  - Validate format with filter_var()      │
│  - Handle proxy detection                 │
└───────────────┬───────────────────────────┘
                │
                ▼
┌───────────────────────────────────────────┐
│  Layer 4: Vote Validation (Core PHP)      │
│  - Raw SQL: Check if IP voted             │
│  - Unique constraint enforcement          │
│  - Return error if duplicate              │
└───────────────┬───────────────────────────┘
                │
                ▼
┌───────────────────────────────────────────┐
│  Layer 5: Database Transaction            │
│  - BEGIN TRANSACTION                      │
│  - Insert vote                            │
│  - Insert history                         │
│  - COMMIT                                 │
└───────────────────────────────────────────┘
```

---

## 📊 Module Integration

```
┌──────────────────────────────────────────────────────────┐
│                     MODULE 1                             │
│              Authentication & Poll Display               │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐        │
│  │   Login    │→ │ Poll List  │→ │Poll Detail │        │
│  └────────────┘  └────────────┘  └────────────┘        │
│         ↓              ↓               ↓                 │
│    Laravel Auth   Blade Views    Bootstrap UI           │
└──────────────────────────────────────────────────────────┘
                          ↓
┌──────────────────────────────────────────────────────────┐
│                     MODULE 2                             │
│                IP-Restricted Voting                      │
│  ┌────────────────┐  ┌──────────────┐                  │
│  │ VotingService  │→ │IpValidation  │                  │
│  │  (Core PHP)    │  │  Service     │                  │
│  └────────────────┘  └──────────────┘                  │
│         ↓                    ↓                           │
│    Raw SQL Queries    $_SERVER['REMOTE_ADDR']           │
└──────────────────────────────────────────────────────────┘
                          ↓
┌──────────────────────────────────────────────────────────┐
│                     MODULE 3                             │
│                Real-Time Results                         │
│  ┌────────────────┐                                     │
│  │ AJAX Polling   │ → Every 1 second                    │
│  │ (JavaScript)   │ → Update DOM                        │
│  └────────────────┘                                     │
│         ↓                                                │
│    GET /api/polls/{id}/results                          │
└──────────────────────────────────────────────────────────┘
                          ↓
┌──────────────────────────────────────────────────────────┐
│                     MODULE 4                             │
│         Admin Features & Vote Rollback                   │
│  ┌──────────────────┐  ┌──────────────┐                │
│  │ Admin Dashboard  │→ │Vote Rollback │                │
│  │                  │  │   Service    │                │
│  └──────────────────┘  └──────────────┘                │
│         ↓                      ↓                         │
│    View Voters         Release IP & Track History       │
└──────────────────────────────────────────────────────────┘
```

---

## 🔄 Real-Time Update Mechanism

```
Browser                  Server                  Database
   │                        │                        │
   │─── Page Load ─────────>│                        │
   │                        │                        │
   │<── HTML + JS ──────────│                        │
   │                        │                        │
   │ Start 1s Timer         │                        │
   │                        │                        │
   ├─ AJAX GET results ────>│                        │
   │                        │                        │
   │                        ├─── SQL Query ─────────>│
   │                        │                        │
   │                        │<── Vote Counts ────────│
   │                        │                        │
   │<── JSON Response ──────│                        │
   │                        │                        │
   │ Update DOM             │                        │
   │ (counts, bars, %)      │                        │
   │                        │                        │
   │ Wait 1 second          │                        │
   │                        │                        │
   ├─ AJAX GET results ────>│ (Loop continues)       │
   │                        │                        │
   └────────────────────────┴────────────────────────┘
```

---

## 📝 Summary

**Architecture Type**: MVC (Model-View-Controller)  
**Backend Framework**: Laravel 11.x  
**Business Logic**: Core PHP (Services Layer)  
**Frontend**: jQuery + AJAX + Bootstrap  
**Database**: MySQL with optimized indexes  
**Real-Time**: AJAX Polling (1-second interval)  
**Security**: Multi-layer validation + CSRF protection  

All components work together to provide a seamless, real-time polling experience with robust IP-based voting restrictions and comprehensive admin controls.
