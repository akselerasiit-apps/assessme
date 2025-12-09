# Quick Testing Setup Guide

**COBIT 2019 Assessment - Testing Quick Start**

---

## 🚀 Quick Start (5 Minutes)

### Step 1: Create Test Database

**Option A: Using Terminal (if mysql command available)**
```bash
mysql -uroot -p -e "CREATE DATABASE assessme_cobit2019_test"
```

**Option B: Using phpMyAdmin or MySQL Workbench**
```sql
CREATE DATABASE assessme_cobit2019_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Option C: Using Laravel Artisan (if DB exists)**
```bash
# Pastikan .env.testing sudah benar
php artisan db:create assessme_cobit2019_test
```

---

### Step 2: Run Migrations for Test Database

```bash
cd /Users/siem1/Downloads/assessme/assessme-app

# Run migrations
php artisan migrate --env=testing

# Seed roles and permissions
php artisan db:seed --env=testing --class=RolePermissionSeeder
```

**Expected Output:**
```
INFO  Preparing database.
INFO  Running migrations.

2024_xx_xx_create_users_table .......................... 12ms DONE
2024_xx_xx_create_roles_table .......................... 8ms DONE
2024_xx_xx_create_assessments_table .................... 15ms DONE
...

Database seeding completed successfully.
- 5 roles created
- 45 permissions assigned
```

---

### Step 3: Run Tests

```bash
# Run all tests
php artisan test

# Or run specific suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage (requires Xdebug or PCOV)
php artisan test --coverage
```

---

## 📋 Troubleshooting

### Problem: "Unknown database 'assessme_cobit2019_test'"

**Solution:**
```bash
# Manually create database first
mysql -uroot -e "CREATE DATABASE assessme_cobit2019_test"

# Or use phpMyAdmin/MySQL Workbench
```

### Problem: "Access denied for user 'root'@'localhost'"

**Solution:** Update `.env.testing` file
```env
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

### Problem: "Class 'Tests\TestCase' not found"

**Solution:**
```bash
composer dump-autoload
```

### Problem: "SQLSTATE[42S02]: Base table or view not found"

**Solution:**
```bash
# Run migrations for test database
php artisan migrate --env=testing --force
```

---

## ✅ Verification Checklist

Before running tests, ensure:

- [ ] Test database `assessme_cobit2019_test` exists
- [ ] `.env.testing` file configured correctly
- [ ] Migrations run successfully (`php artisan migrate --env=testing`)
- [ ] Roles seeded (`php artisan db:seed --env=testing --class=RolePermissionSeeder`)
- [ ] Composer autoload updated (`composer dump-autoload`)

---

## 🎯 Expected Test Results

When everything is set up correctly:

```
   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ user can login with valid credentials
  ✓ user cannot login with invalid credentials
  ✓ user can logout
  ✓ unauthenticated user cannot access protected routes
  ✓ user data is returned correctly

   PASS  Tests\Feature\Assessment\AssessmentManagementTest
  ✓ admin can create assessment
  ✓ viewer cannot create assessment
  ✓ admin can view all assessments
  ✓ admin can update assessment
  ✓ admin can delete assessment
  ✓ validation errors for missing fields
  ✓ assessment status can be updated

   PASS  Tests\Unit\Policies\AssessmentPolicyTest
  ✓ super admin can view any assessment
  ✓ viewer can view assessments
  ✓ admin can create assessment
  ✓ viewer cannot create assessment
  ...

  Tests:    50 passed (178 assertions)
  Duration: 12.34s

✅ ALL TESTS PASSED
```

---

## 📚 Full Documentation

Untuk dokumentasi lengkap, lihat:
- **TESTING_DOCUMENTATION.md** - Complete testing guide (800+ lines)
- **TESTING_IMPLEMENTATION_SUMMARY.md** - Implementation summary & statistics

---

## 🔧 Manual Database Setup (If Needed)

### MySQL Command Line

```bash
# Login to MySQL
mysql -uroot -p

# Create database
CREATE DATABASE assessme_cobit2019_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Verify
SHOW DATABASES LIKE 'assessme%';

# Exit
EXIT;
```

### Using PHP Script (Alternative)

```php
<?php
// create_test_db.php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS assessme_cobit2019_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✅ Test database created successfully\n";
} else {
    echo "❌ Error creating database: " . $conn->error . "\n";
}

$conn->close();
?>
```

Run: `php create_test_db.php`

---

## 🎉 You're Ready!

Once database is created and migrations run, simply execute:

```bash
php artisan test
```

Happy Testing! 🚀
