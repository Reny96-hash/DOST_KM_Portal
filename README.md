# DOST Knowledge Management Portal

A centralized knowledge management system for the Department of Science and Technology (DOST) to capture, store, and share institutional knowledge.


##  Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Reny96-hash/DOST_KM_Portal.git
cd DOST_KM_Portal
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file:

**For SQLite (Default - No setup needed):**
```env
DB_CONNECTION=sqlite
```

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kmportal
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Compile Assets

```bash
npm run dev
```

### 7. Start Server

```bash
php artisan serve
```

Visit: `http://127.0.0.1:8000`

##  Test Login

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@dost.gov.ph | Admin@1234 |


##  Database Tables

| Table | Purpose |
|-------|---------|
| tbl_users | User accounts and roles |
| tbl_documents | Knowledge assets |
| tbl_categories | Document categories |
| tbl_access_logs | Audit trail |
| sessions | Session management |

##  Commands Reference

```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Create admin user (if needed)
php artisan tinker
```

Then in tinker:
```php
use App\Models\User;
User::create([
    'emp_id' => 'ADMIN-001',
    'user_first_name' => 'Admin',
    'user_last_name' => 'User',
    'user_email' => 'admin@dost.gov.ph',
    'user_password_hash' => bcrypt('Admin@1234'),
    'security_clearance' => 'Top Secret',
    'user_role' => 'admin',
    'user_status' => 'active',
]);
exit;
```

##  Project Structure

```
dost-km-portal/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/               # User, Document models
│   └── Http/Middleware/      # AdminMiddleware
├── database/migrations/      # Database tables
├── resources/views/          # Blade templates
│   ├── layouts/app.blade.php
│   ├── login.blade.php
│   ├── dashboard.blade.php
│   └── upload.blade.php
└── routes/web.php            # All routes
```
----------
