# Testing Documentation - COBIT 2019 Assessment Application

**Version:** 1.0  
**Last Updated:** December 9, 2025  
**Framework:** Laravel 12.x + PHPUnit  

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Test Environment Setup](#test-environment-setup)
3. [Test Structure](#test-structure)
4. [Running Tests](#running-tests)
5. [Test Categories](#test-categories)
6. [Coverage Goals](#coverage-goals)
7. [Test Database](#test-database)
8. [Common Test Patterns](#common-test-patterns)
9. [CI/CD Integration](#cicd-integration)
10. [Troubleshooting](#troubleshooting)

---

## 1. Overview

Aplikasi COBIT 2019 Assessment menggunakan comprehensive testing strategy dengan fokus pada:

- **Feature Tests**: End-to-end API testing
- **Unit Tests**: Policy & business logic testing
- **Security Tests**: Authentication, authorization, input validation
- **Integration Tests**: Workflow & data integrity

### Testing Philosophy

```
✓ Test-Driven Development (TDD) friendly
✓ UAM (User Access Management) compliance
✓ Security-first approach
✓ Automated CI/CD integration
✓ High code coverage (target: 85%+)
```

---

## 2. Test Environment Setup

### 2.1 Prerequisites

```bash
# Ensure PHPUnit is installed
composer require --dev phpunit/phpunit

# Install Laravel testing helpers (already included)
# - Illuminate\Foundation\Testing\RefreshDatabase
# - Illuminate\Foundation\Testing\WithFaker
```

### 2.2 Environment Configuration

#### `.env.testing` Configuration

```env
APP_NAME="COBIT 2019 Assessment"
APP_ENV=testing
APP_KEY=base64:HikXM2nlgeUH9536U3PvJLmJjfNeDPJbKp5VfklSk94=
APP_DEBUG=true

# Fast bcrypt for testing
BCRYPT_ROUNDS=4

# Minimal logging for tests
LOG_LEVEL=error

# Test Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assessme_cobit2019_test
DB_USERNAME=root
DB_PASSWORD=

# Array drivers for speed
CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array
```

#### `phpunit.xml` Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="DB_CONNECTION" value="mysql"/>
        <env name="DB_DATABASE" value="assessme_cobit2019_test"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="MAIL_MAILER" value="array"/>
    </php>
</phpunit>
```

### 2.3 Create Test Database

```bash
# MySQL
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS assessme_cobit2019_test"

# Or via PHP Artisan
php artisan migrate:fresh --env=testing
php artisan db:seed --env=testing --class=RolePermissionSeeder
```

---

## 3. Test Structure

```
tests/
├── Feature/                    # End-to-end API tests
│   ├── Auth/
│   │   └── AuthenticationTest.php
│   ├── Assessment/
│   │   └── AssessmentManagementTest.php
│   ├── Answer/
│   │   └── AnswerManagementTest.php
│   ├── Report/
│   │   └── ReportGenerationTest.php
│   └── Security/
│       ├── AuthenticationSecurityTest.php
│       └── FileUploadSecurityTest.php
├── Unit/                       # Unit tests
│   ├── Policies/
│   │   ├── AssessmentPolicyTest.php
│   │   └── AnswerPolicyTest.php
│   └── Services/
│       └── ScoringServiceTest.php
└── TestCase.php               # Base test class
```

### Base TestCase Class

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    /**
     * Authenticate as specific role
     */
    protected function authenticateAs(string $role = 'Super Admin'): \App\Models\User
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    /**
     * Create user with role (without auth)
     */
    protected function createUserWithRole(string $role): \App\Models\User
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);
        return $user;
    }
}
```

---

## 4. Running Tests

### 4.1 Run All Tests

```bash
# Run all tests
php artisan test

# Or using PHPUnit directly
./vendor/bin/phpunit
```

### 4.2 Run Specific Test Suites

```bash
# Feature tests only
php artisan test --testsuite=Feature

# Unit tests only
php artisan test --testsuite=Unit

# Specific test file
php artisan test tests/Feature/Auth/AuthenticationTest.php

# Specific test method
php artisan test --filter test_user_can_login_with_valid_credentials
```

### 4.3 Run with Coverage

```bash
# Generate coverage report (requires Xdebug or PCOV)
php artisan test --coverage

# Detailed coverage HTML report
php artisan test --coverage-html coverage/

# Coverage with minimum threshold
php artisan test --coverage --min=80
```

### 4.4 Parallel Testing

```bash
# Run tests in parallel (Laravel 8+)
php artisan test --parallel

# Specify number of processes
php artisan test --parallel --processes=4
```

---

## 5. Test Categories

### 5.1 Feature Tests

#### Authentication Tests (`tests/Feature/Auth/AuthenticationTest.php`)

**Purpose:** Test authentication flow, login, logout, token management

**Test Cases:**
- ✅ `test_user_can_login_with_valid_credentials()`
- ✅ `test_user_cannot_login_with_invalid_credentials()`
- ✅ `test_user_can_logout()`
- ✅ `test_unauthenticated_user_cannot_access_protected_routes()`
- ✅ `test_user_data_is_returned_correctly()`

**Example:**
```php
public function test_user_can_login_with_valid_credentials(): void
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['user', 'token']);
}
```

#### Assessment Tests (`tests/Feature/Assessment/AssessmentManagementTest.php`)

**Purpose:** Test CRUD operations, workflow, UAM compliance

**Test Cases:**
- ✅ `test_admin_can_create_assessment()`
- ✅ `test_viewer_cannot_create_assessment()`
- ✅ `test_admin_can_view_all_assessments()`
- ✅ `test_admin_can_update_assessment()`
- ✅ `test_admin_can_delete_assessment()`
- ✅ `test_validation_errors_for_missing_fields()`
- ✅ `test_assessment_status_can_be_updated()`

#### Answer Tests (`tests/Feature/Answer/AnswerManagementTest.php`)

**Purpose:** Test answer submission, evidence upload, validation

**Test Cases:**
- ✅ `test_assessor_can_submit_answer()`
- ✅ `test_assessor_can_upload_evidence_file()`
- ✅ `test_assessor_can_update_answer()`
- ✅ `test_validation_for_answer_submission()`
- ✅ `test_evidence_file_type_validation()`
- ✅ `test_viewer_cannot_submit_answer()`

#### Report Tests (`tests/Feature/Report/ReportGenerationTest.php`)

**Purpose:** Test PDF/Excel generation, dashboard statistics

**Test Cases:**
- ✅ `test_manager_can_generate_assessment_summary_pdf()`
- ✅ `test_manager_can_generate_maturity_report_pdf()`
- ✅ `test_manager_can_generate_gap_analysis_pdf()`
- ✅ `test_manager_can_export_assessment_to_excel()`
- ✅ `test_admin_can_view_dashboard_statistics()`

### 5.2 Unit Tests

#### Policy Tests (`tests/Unit/Policies/`)

**Purpose:** Test authorization logic for all roles

**AssessmentPolicyTest:**
- ✅ `test_super_admin_can_view_any_assessment()`
- ✅ `test_viewer_can_view_assessments()`
- ✅ `test_admin_can_create_assessment()`
- ✅ `test_viewer_cannot_create_assessment()`
- ✅ `test_super_admin_can_delete_assessment()`
- ✅ `test_manager_can_review_assessment()`
- ✅ `test_super_admin_can_approve_assessment()`

**AnswerPolicyTest:**
- ✅ `test_assessor_can_create_answer()`
- ✅ `test_viewer_cannot_create_answer()`
- ✅ `test_assessor_can_update_own_answer()`
- ✅ `test_assessor_cannot_update_others_answer()`
- ✅ `test_assessor_can_upload_evidence()`

### 5.3 Security Tests

#### Authentication Security (`tests/Feature/Security/AuthenticationSecurityTest.php`)

**Purpose:** Test security vulnerabilities prevention

**Test Cases:**
- ✅ `test_sql_injection_prevention_in_login()`
- ✅ `test_xss_prevention_in_user_input()`
- ✅ `test_rate_limiting_on_login_endpoint()`
- ✅ `test_password_strength_requirement()`
- ✅ `test_unauthorized_access_is_blocked()`
- ✅ `test_token_has_expiration()`
- ✅ `test_session_hijacking_prevention()`

#### File Upload Security (`tests/Feature/Security/FileUploadSecurityTest.php`)

**Purpose:** Test file upload security measures

**Test Cases:**
- ✅ `test_malicious_file_upload_is_rejected()`
- ✅ `test_file_size_limit_is_enforced()`
- ✅ `test_only_allowed_file_types_can_be_uploaded()`
- ✅ `test_file_is_stored_securely()`
- ✅ `test_path_traversal_attack_is_prevented()`

---

## 6. Coverage Goals

### Target Coverage Levels

```
✅ Critical Paths:     95%+ coverage
✅ Business Logic:     85%+ coverage
✅ Controllers:        80%+ coverage
✅ Policies:           100% coverage
✅ Form Requests:      90%+ coverage
✅ Overall:            85%+ coverage
```

### Coverage Report

```bash
# Generate coverage report
php artisan test --coverage

# Example output:
  Tests:    42 passed (132 assertions)
  Duration: 8.45s

  App\Http\Controllers\Api ................ 87.5 %
  App\Policies ............................ 100.0 %
  App\Http\Requests ....................... 92.3 %
  App\Models .............................. 78.2 %
  
  Total Coverage .......................... 86.7 %
```

---

## 7. Test Database

### Database Strategy

```php
// tests/TestCase.php
use RefreshDatabase;  // Migrates fresh database for each test

protected function setUp(): void
{
    parent::setUp();
    
    // Seed roles and permissions for every test
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
}
```

### Factory Usage

```php
// Create test data
$company = Company::factory()->create();
$assessment = Assessment::factory()->draft()->create();
$user = User::factory()->create();
$user->assignRole('Admin');

// Create multiple records
Assessment::factory()->count(10)->create();

// With specific attributes
$assessment = Assessment::factory()->create([
    'status' => 'completed',
    'company_id' => $company->id,
]);

// Using factory states
$draftAssessment = Assessment::factory()->draft()->create();
$completedAssessment = Assessment::factory()->completed()->create();
```

---

## 8. Common Test Patterns

### 8.1 Testing API Endpoints

```php
public function test_api_endpoint(): void
{
    $user = $this->authenticateAs('Admin');
    
    $response = $this->getJson('/api/assessments');
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'code', 'title']
            ]
        ]);
}
```

### 8.2 Testing Authorization

```php
public function test_unauthorized_access(): void
{
    $this->authenticateAs('Viewer');
    
    $response = $this->postJson('/api/assessments', $data);
    
    $response->assertStatus(403);
}
```

### 8.3 Testing Validation

```php
public function test_validation(): void
{
    $this->authenticateAs('Admin');
    
    $response = $this->postJson('/api/assessments', []);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'company_id']);
}
```

### 8.4 Testing File Upload

```php
public function test_file_upload(): void
{
    Storage::fake('private');
    
    $file = UploadedFile::fake()->create('test.pdf', 1024);
    
    $response = $this->postJson('/api/evidence/upload', [
        'file' => $file,
    ]);
    
    $response->assertStatus(201);
    Storage::disk('private')->assertExists('evidence/' . $file->hashName());
}
```

### 8.5 Testing Database Changes

```php
public function test_database_changes(): void
{
    $assessment = Assessment::factory()->create(['title' => 'Old']);
    
    $this->putJson("/api/assessments/{$assessment->id}", [
        'title' => 'New Title',
    ]);
    
    $this->assertDatabaseHas('assessments', [
        'id' => $assessment->id,
        'title' => 'New Title',
    ]);
}
```

---

## 9. CI/CD Integration

### 9.1 GitHub Actions Workflow

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: assessme_cobit2019_test
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: mbstring, pdo_mysql
          coverage: xdebug
      
      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist
      
      - name: Copy Environment
        run: cp .env.testing .env
      
      - name: Generate Key
        run: php artisan key:generate
      
      - name: Run Migrations
        run: php artisan migrate --force
      
      - name: Run Tests
        run: php artisan test --coverage --min=85
```

### 9.2 GitLab CI Configuration

```yaml
# .gitlab-ci.yml
test:
  image: php:8.2
  services:
    - mysql:8.0
  variables:
    MYSQL_DATABASE: assessme_cobit2019_test
    MYSQL_ROOT_PASSWORD: root
  script:
    - composer install
    - cp .env.testing .env
    - php artisan migrate --force
    - php artisan test --coverage --min=85
```

---

## 10. Troubleshooting

### Common Issues

#### Issue: "Database not found"

```bash
# Solution: Create test database
mysql -uroot -e "CREATE DATABASE assessme_cobit2019_test"
```

#### Issue: "Class 'Tests\TestCase' not found"

```bash
# Solution: Regenerate autoload
composer dump-autoload
```

#### Issue: "Too few arguments to function"

```bash
# Solution: Check factory relationships
# Ensure all foreign keys have factory() or specific ID
'company_id' => Company::factory(),
```

#### Issue: "Access denied for user"

```bash
# Solution: Check .env.testing database credentials
DB_USERNAME=root
DB_PASSWORD=
```

#### Issue: "Tests taking too long"

```bash
# Solution: Use array drivers and reduce bcrypt rounds
# .env.testing
BCRYPT_ROUNDS=4
CACHE_STORE=array
SESSION_DRIVER=array
```

---

## 11. Test Execution Summary

### Running Complete Test Suite

```bash
# Full test run with coverage
php artisan test --coverage

# Expected output:
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

   PASS  Tests\Feature\Answer\AnswerManagementTest
  ✓ assessor can submit answer
  ✓ assessor can upload evidence file
  ✓ assessor can update answer
  ✓ validation for answer submission
  ✓ evidence file type validation
  ✓ viewer cannot submit answer

   PASS  Tests\Feature\Report\ReportGenerationTest
  ✓ manager can generate assessment summary pdf
  ✓ manager can generate maturity report pdf
  ✓ manager can generate gap analysis pdf
  ✓ manager can export assessment to excel
  ✓ admin can view dashboard statistics

   PASS  Tests\Unit\Policies\AssessmentPolicyTest
  ✓ super admin can view any assessment
  ✓ viewer can view assessments
  ✓ admin can create assessment
  ✓ viewer cannot create assessment
  ✓ admin can update assessment
  ✓ assessor cannot update assessment
  ✓ super admin can delete assessment
  ✓ manager cannot delete assessment
  ✓ manager can review assessment
  ✓ super admin can approve assessment
  ✓ admin cannot approve assessment

   PASS  Tests\Unit\Policies\AnswerPolicyTest
  ✓ assessor can create answer
  ✓ viewer cannot create answer
  ✓ admin can view any answer
  ✓ assessor can update own answer
  ✓ assessor cannot update others answer
  ✓ admin can delete any answer
  ✓ assessor can upload evidence
  ✓ viewer cannot upload evidence

   PASS  Tests\Feature\Security\AuthenticationSecurityTest
  ✓ sql injection prevention in login
  ✓ xss prevention in user input
  ✓ rate limiting on login endpoint
  ✓ password strength requirement
  ✓ unauthorized access is blocked
  ✓ token has expiration
  ✓ session hijacking prevention

   PASS  Tests\Feature\Security\FileUploadSecurityTest
  ✓ malicious file upload is rejected
  ✓ file size limit is enforced
  ✓ only allowed file types can be uploaded
  ✓ file is stored securely
  ✓ path traversal attack is prevented

  Tests:    50 passed (178 assertions)
  Duration: 12.34s

  Coverage:
  App\Http\Controllers\Api ................ 87.5%
  App\Policies ............................ 100.0%
  App\Http\Requests ....................... 92.3%
  
  Total Coverage .......................... 86.7% ✅
```

---

## 12. Best Practices

### ✅ DO

- Use `RefreshDatabase` trait for database isolation
- Seed minimal required data (roles/permissions)
- Use factories for test data creation
- Test both success and failure scenarios
- Test authorization for all roles
- Use descriptive test method names
- Group related tests in same file
- Mock external services (email, storage)

### ❌ DON'T

- Don't use real database in tests
- Don't share state between tests
- Don't skip security tests
- Don't test framework functionality
- Don't use sleep() or fixed delays
- Don't commit sensitive data in tests

---

## 13. Next Steps

### Additional Tests to Implement (Optional)

```
□ Integration Tests
  - Assessment workflow end-to-end
  - Banding/appeal process
  - Multi-user scenarios

□ Performance Tests
  - Load testing for reports
  - Concurrent user handling
  - Database query optimization

□ Browser Tests (Dusk)
  - Frontend interaction testing
  - JavaScript functionality
  - Cross-browser compatibility
```

---

**Testing adalah fondasi quality assurance. Invest waktu di awal untuk testing yang comprehensive akan menghemat waktu debugging di production! 🚀**
