# 🏗️ System Architecture - NiBoXMoVie

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENT LAYER (Browser)                   │
│              HTML5 + CSS3 + Vanilla JavaScript              │
└──────────────────────────┬──────────────────────────────────┘
                           │
                    HTTP/HTTPS Requests
                           │
┌──────────────────────────▼──────────────────────────────────┐
│                 ROUTING LAYER (index.php)                    │
│    - $_GET['page'] based routing                            │
│    - Authentication checks                                  │
│    - Role redirects (admin vs user)                         │
└──────────────────────────┬──────────────────────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
   ┌────▼─────┐    ┌──────▼──────┐   ┌──────▼──────┐
   │ Include  │    │   Admin     │   │   Auth      │
   │ Modules  │    │   Modules   │   │   Modules   │
   └────┬─────┘    └──────┬──────┘   └──────┬──────┘
        │                 │                 │
┌───────▼─────────────────▼─────────────────▼───────────────┐
│            BUSINESS LOGIC LAYER (PHP)                       │
│  - functions.php (data retrieval & manipulation)           │
│  - check_admin.php (authorization)                         │
│  - Authentication handlers (login/register/reset)          │
└───────┬─────────────────────────────────────────────────────┘
        │
┌───────▼─────────────────────────────────────────────────────┐
│         DATABASE ABSTRACTION LAYER (PDO/MySQLi)             │
│  - db.php (connection & configuration)                     │
│  - Prepared statements for security                        │
│  - Connection pooling (basic)                              │
└───────┬─────────────────────────────────────────────────────┘
        │
┌───────▼─────────────────────────────────────────────────────┐
│            DATABASE LAYER (MariaDB 10.4)                    │
│  - users, admins, movies, genres                           │
│  - Comments, ratings, tokens                               │
│  - Relationships & indexes                                 │
└───────────────────────────────────────────────────────────┘
```

---

## 🎯 Architectural Patterns

### 1. **MVC-like Pattern (Simplified)**

```
MODEL (Database Layer)
├── db.php → MySQL Connection
├── functions.php → Data Operations
└── SQL Queries

VIEW (Presentation Layer)
├── include/ → UI Components
├── admin/ → Admin Interface
├── auth/ → Auth Forms
└── user/ → User Pages
└── assets/ → Styling

CONTROLLER (Business Logic)
├── index.php → Main Router
├── check_admin.php → Auth Guard
└── Page-specific logic in each file
```

### 2. **Service Locator Pattern**

Functions stored in `functions.php` act as services:
```php
// Service Functions
getMovieDetails()  // Movie service
getGenres()        // Genre service
getFeaturedMovies() // Featured service
```

### 3. **Template Rendering Pattern**

```php
// Controller prepares data
$movie = getMovieById($conn, $id);
$comments = getMovieComments($conn, $id);

// View renders output
include 'template.php'; // Uses $movie, $comments
```

---

## 🔄 Request/Response Flow

### User Request Flow (Example: View Movie)

```
┌─────────────────────────────────────────────────────────────┐
│ 1. USER INITIATES REQUEST                                   │
│    GET /index.php?page=watch&id=5                           │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 2. ROUTE RESOLUTION (index.php)                             │
│    - Parse $_GET['page'] = 'watch', $_GET['id'] = 5        │
│    - Include user/watch.php                                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 3. AUTHENTICATION CHECK (watch.php)                         │
│    - Check if user is logged in                            │
│    - Optional: Check user permissions                       │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 4. DATA RETRIEVAL (functions.php)                           │
│    - $movie = getMovieById($conn, 5)                       │
│    - $comments = getMovieComments($conn, 5)                │
│    - Query: SELECT * FROM movies WHERE id = 5              │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 5. DATA PROCESSING                                           │
│    - Format data for display                               │
│    - Escape output (htmlspecialchars)                      │
│    - Validate comments (is_approved = 1)                   │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 6. RENDER RESPONSE                                           │
│    - Build HTML with movie data                            │
│    - Include video player                                  │
│    - Render comments section                               │
│    - Load CSS/JS assets                                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 7. SEND TO CLIENT                                            │
│    - HTTP 200 OK                                           │
│    - HTML response                                          │
│    - Browser renders & loads assets                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 8. CLIENT RENDERING                                         │
│    - Parse HTML                                            │
│    - Load CSS (style.css, responsive.css)                 │
│    - Load JS (app.js, watch.js)                           │
│    - Display fully interactive page                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 9. USER INTERACTION (JavaScript Events)                     │
│    - Play video                                            │
│    - Submit comment                                        │
│    - Rate movie                                            │
│    - Go to next movie                                      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔐 Authentication & Authorization Flow

### Login Flow

```
┌──────────────────────┐
│ User enters        │
│ credentials        │
└──────────┬──────────┘
           │
           │ POST /auth/login.php
           │
┌──────────▼──────────────────────────────────┐
│ 1. CSRF Token Validation                     │
│    - $_POST['csrf_token'] == $_SESSION[...]  │
│    - If invalid, reject request              │
└──────────┬──────────────────────────────────┘
           │
┌──────────▼──────────────────────────────────┐
│ 2. Input Validation                          │
│    - Check username/password not empty       │
│    - Validate email format                   │
│    - Check length constraints                │
└──────────┬──────────────────────────────────┘
           │
┌──────────▼──────────────────────────────────┐
│ 3. Verify Credentials                        │
│    - SELECT * FROM users WHERE username = ? │
│    - password_verify(input, stored_hash)     │
│    - If failed, return error                 │
└──────────┬──────────────────────────────────┘
           │
┌──────────▼──────────────────────────────────┐
│ 4. Create Session                            │
│    - Set $_SESSION['user_id']                │
│    - Set $_SESSION['username']               │
│    - Set $_SESSION['role']                   │
└──────────┬──────────────────────────────────┘
           │
┌──────────▼──────────────────────────────────┐
│ 5. Generate Remember Token (Optional)        │
│    - Create unique token: bin2hex()          │
│    - Save to user_tokens table               │
│    - Set cookie: setcookie('login_token')    │
└──────────┬──────────────────────────────────┘
           │
┌──────────▼──────────────────────────────────┐
│ 6. Redirect                                  │
│    - Admin: /admin/dashboard.php             │
│    - User: /index.php?page=home              │
└─────────────────────────────────────────────┘
```

### Admin Guard (check_admin.php)

```php
requireAdmin() {
  if (!isset($_SESSION['user_id'])) {
    redirect_to_login();
  }
  
  $role = retrieve_role($_SESSION['user_id']);
  
  if ($role NOT IN ['admin', 'super_admin']) {
    redirect_unauthorized();
  }
  
  return true;
}
```

---

## 🗄️ Database Architecture

### Entity Relationship Diagram

```
┌─────────────┐         ┌──────────────┐
│   users     │────────▶│   comments   │
│  (PK: id)   │         │  (FK: user)  │
└─────────────┘         │  (FK: movie) │
                        └──────────────┘
                             ▲
      ┌──────────────────────┘
      │
┌─────▼──────┐         ┌──────────────┐
│   movies   │────────▶│   ratings    │
│ (PK: id)   │         │  (FK: movie) │
└──────┬─────┘         │  (FK: user)  │
       │               └──────────────┘
       │
       │         ┌──────────────────┐
       └────────▶│  movie_genres    │
                 │  (FK: movie)     │
                 │  (FK: genre)     │
                 └──────────────────┘
                        ▲
                        │
                    ┌───┴──────┐
                    │  genres  │
                  (PK: id)     │
                    └──────────┘

Other Relations:
movies --FK--> universes
movies --FK--> movie_actors
movie_actors --FK--> actors
admins (separate table from users)
user_tokens (for remember-me)
```

### Query Optimization

**Indexed Columns:**
- movies.id (PRIMARY)
- users.id (PRIMARY)
- comments.movie_id (for lookups)
- movie_genres.movie_id (for listing)
- user_tokens.token (for validation)

---

## 🎨 Frontend Architecture

### HTML Structure

```html
<!DOCTYPE html>
<html>
  <head>
    <!-- CSS loaded per page -->
  </head>
  <body>
    <!-- Header (universal) -->
    <?php include header.php ?>
    
    <!-- Content (routed) -->
    <main>
      <!-- Page-specific content -->
    </main>
    
    <!-- Footer (universal) -->
    <?php include footer.php ?>
    
    <!-- JS files (deferred) -->
    <script src="assets/app.js"></script>
  </body>
</html>
```

### CSS Architecture

```
assets/
├── style.css              # Global styles
│   - Body & Typography
│   - Layout & Grid
│   - Components (cards, buttons)
│   - Navigation
│   - Slider
│
├── responsive.css         # Media queries
│   - Mobile (< 768px)
│   - Tablet (768px - 1024px)
│   - Desktop (> 1024px)
│
├── auth.css              # Auth pages
│   - Forms
│   - Inputs
│   - Buttons
│
└── admin.css             # Admin dashboard
    - Dashboard
    - Tables
    - Forms
    - Statistics
```

### JavaScript Architecture

```javascript
// app.js - Main application logic
- Slider carousel functionality
- Movie list scrolling

// watch.js - Watch page logic
- Video player integration
- Comment submission
- Rating system

// hamburger.js - Mobile navigation
- Menu toggle
- Responsive navigation

// slider.js - Advanced slider features
- Auto-play
- Navigation
- Swipe support (if added)

// admin.js - Admin functionality
- Form validation
- Table interactions
- Delete confirmations
```

---

## 🔄 Data Flow Examples

### Example 1: Search Movies

```
User Input: "Iron Man"
     ↓
GET /index.php?page=search&q=Iron%20Man
     ↓
index.php router → include search.php
     ↓
search.php:
  $query = $_GET['q']
  $results = searchMovies($conn, $query)
     ↓
functions.php:
  SELECT * FROM movies 
  WHERE title LIKE '%Iron Man%' OR description LIKE '%..%'
     ↓
Database: Returns 5 matching movies
     ↓
search.php: Render results
     ↓
User sees search results with links to watch
```

### Example 2: Admin Adds Movie

```
Admin fills form:
  - Title: "New Movie"
  - Year: 2025
  - Genres: [Action, Sci-Fi]
  - Universe: Marvel
     ↓
POST /admin/add_movie.php
     ↓
Validation:
  - Check title not empty
  - Year between 1888 and future
  - Genres exist
     ↓
Transaction begins:
  - INSERT movies → get movie_id
  - INSERT movie_genres (multiple rows)
     ↓
Commit transaction
     ↓
Redirect to /admin/movies.php
     ↓
Success message displayed
```

### Example 3: Comment System

```
User posts comment: "Great movie!"
     ↓
JavaScript: Validate not empty
     ↓
POST /user/watch.php (comment data)
     ↓
Check user logged in
Validate CSRF token
     ↓
INSERT comments:
  - user_id = $_SESSION['user_id']
  - movie_id = $_GET['id']
  - comment_text = "Great movie!"
  - is_approved = 1 (auto-approve)
  - created_at = NOW()
     ↓
Fetch updated comments list
     ↓
Return JSON/HTML response
     ↓
JavaScript: Update DOM
User sees comment appear immediately
```

---

## 🔒 Security Architecture

### Input Security

```
User Input
    ↓
1. Validation Layer
   - Type checking (is_numeric, is_array)
   - Length limits
   - Format validation (email, regex)
    ↓
2. Sanitization Layer
   - htmlspecialchars() for output
   - trim() for whitespace
   - Type casting (int, string)
    ↓
3. SQL Injection Prevention
   - Prepared statements ALWAYS
   - bind_param() or parameterized queries
   - Never concatenate SQL
    ↓
4. Safe Output
   - HTML entities encoded
   - JavaScript not injected
   - Data displayed safely
```

### Authentication Security

```
Password Storage:
  user_input_password
    ↓
  password_hash(input, PASSWORD_BCRYPT)
    ↓
  stored_hash in database (never plain text)
    ↓
Login verification:
  password_verify(user_input, stored_hash)
    ↓
  Returns true/false (constant-time comparison)
```

### Session Security

```
Session Initialization:
  1. session_start() - Create PHPSESSID cookie
  2. $_SESSION['csrf_token'] - CSRF protection
  3. Store user_id, username, role
  
Session Validation:
  1. Check PHPSESSID exists
  2. Verify user still exists in DB
  3. Validate CSRF token on POST requests
  4. Check user role/permissions
  
Session Destruction:
  1. Unset all $_SESSION variables
  2. session_destroy() - Delete session file
  3. Clear login_token cookie
```

---

## 🎯 State Management

### Server-Side State (PHP Sessions)

```php
$_SESSION = [
  'user_id' => 12,
  'username' => 'john_doe',
  'role' => 'user',  // or 'admin', 'super_admin'
  'csrf_token' => 'random_hex_string',
  'login_time' => time()
]
```

### Client-Side State (Cookies)

```
login_token (optional remember-me)
  - Expires: 30 days
  - HttpOnly: true (secure)
  - Path: /
  - Domain: automatic
```

### Database State

```
Persistent data:
  - User profiles
  - Movie metadata
  - Comments & ratings
  - Admin actions (logged)
  
Transient data:
  - User tokens (expire after 30 days)
  - Session files (auto-cleanup)
```

---

## 📈 Scalability Considerations

### Current Architecture Limits
1. **Single Server**: All PHP code runs on one instance
2. **No Caching**: Every page load queries database
3. **No Load Balancing**: Cannot distribute traffic
4. **Session Storage**: File-based (not distributed)

### Scaling Recommendations

**Phase 1: Database Optimization**
- Add indexes on frequently queried columns
- Cache query results (Redis/Memcached)
- Archive old comments

**Phase 2: Code-Level**
- Implement dependency injection
- Create query builder class
- Add rate limiting

**Phase 3: Infrastructure**
- Use CDN for images
- Session storage in Redis
- Database read replicas

**Phase 4: Architecture**
- Microservices for admin/user sections
- API layer (RESTful)
- Queue system for heavy operations

---

## 🧪 Testing Strategy

### Unit Testing (Recommended additions)
```php
// Test getMovieById function
function test_getMovieById() {
  $movie = getMovieById($conn, 1);
  assert($movie['id'] === 1);
  assert(!empty($movie['title']));
}
```

### Integration Testing
- Test database queries
- Test authentication flows
- Test comment system

### Manual Testing
- Browser testing (different devices)
- Security testing (SQL injection attempts)
- Performance testing (load testing)

---

## 📋 Design Decisions

| Decision | Rationale |
|----------|-----------|
| **Server-Side Rendering** | Simpler deployment, SEO friendly |
| **Prepared Statements** | SQL Injection prevention |
| **File-based Sessions** | No external dependencies |
| **bcrypt Hashing** | Industry standard, slow by design |
| **Single index.php Router** | Centralized routing logic |
| **CSS Classes** | Better maintainability than inline styles |
| **Vanilla JS** | No jQuery dependency, smaller payload |

---

## 🔗 Architecture Diagram (Complete)

```
                    ┌─────────────────────┐
                    │   Internet/Client   │
                    └──────────┬──────────┘
                               │
                    HTTP/HTTPS │ Requests
                               │
                    ┌──────────▼──────────┐
                    │  Apache/PHP Server  │
                    └──────────┬──────────┘
                               │
              ┌────────────────┼────────────────┐
              │                │                │
    ┌─────────▼──────┐  ┌──────▼──────┐  ┌────▼──────────┐
    │ index.php      │  │ admin/*.php  │  │ auth/*.php    │
    │ (User routes)  │  │ (Dashboard)  │  │ (Auth forms)  │
    └────────┬───────┘  └──────┬───────┘  └────┬──────────┘
             │                 │              │
             └─────────────────┼──────────────┘
                               │
                    ┌──────────▼──────────┐
                    │  Business Logic     │
                    │  (functions.php)    │
                    │  (Security checks)  │
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │  DB Abstraction     │
                    │  (db.php)           │
                    │  Prepared Statements│
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │  MariaDB Database   │
                    │  10.4.32            │
                    │  Port 3307          │
                    └─────────────────────┘
```

---

**Architecture Version**: 1.0
**Last Updated**: March 22, 2026
**Status**: Well-documented, production-ready
