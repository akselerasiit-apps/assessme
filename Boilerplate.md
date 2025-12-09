# Boilerplate Aplikasi Assessment COBIT 2019

## 📋 Daftar Isi
1. [Gambaran Umum Proyek](#gambaran-umum-proyek)
2. [Versi COBIT & Framework](#versi-cobit--framework)
3. [Tech Stack](#tech-stack)
4. [Security Architecture](#security-architecture)
5. [Alur Aplikasi](#alur-aplikasi)
6. [Desain Database](#desain-database)
7. [Role & Permission User](#role--permission-user)
8. [Matriks User Access](#matriks-user-access)
9. [Daftar Modul](#daftar-modul)
10. [Daftar Fitur](#daftar-fitur)
11. [Struktur Folder Project](#struktur-folder-project)
12. [Setup & Instalasi](#setup--instalasi)
13. [API Endpoints](#api-endpoints)
14. [Entity Relationship Diagram](#entity-relationship-diagram)

---

## 1. Gambaran Umum Proyek

### Deskripsi
Aplikasi Assessment COBIT 2019 adalah sistem enterprise-grade yang dirancang untuk mengevaluasi tingkat kematangan pengelolaan IT (IT Governance) sesuai dengan framework COBIT 2019 terbaru. Aplikasi ini membantu organisasi mengidentifikasi gap, memberikan rekomendasi, dan meningkatkan kontrol IT berdasarkan design factors dan customizable assessment scope.

### Fitur Utama
- Assessment berbasis COBIT 2019 dengan Design Factors (10 faktor)
- Customizable assessment scope melalui Design Factors
- GAMO (Governance & Management Objectives) selection - EDM, APO, BAI, DSS, MEA
- Scoring dan penilaian level maturity/capability (0-5)
- Pelaporan dan analisis hasil assessment komprehensif
- Manajemen user dengan role-based access control (RBAC)
- Dashboard interaktif dengan visualisasi data real-time
- Export laporan (PDF, Excel) dengan branding
- Secure evidence management dan file storage
- Complete audit trail dan compliance logging
- Enterprise-grade security architecture

---

## 2. Versi COBIT & Framework

### 2.1 COBIT 2019 Overview

COBIT 2019 adalah framework governance dan management IT terbaru dengan pendekatan yang lebih flexible dan customizable. Framework ini menggunakan Design Factors sebagai fase pertama untuk melakukan tailoring sesuai kebutuhan organisasi.

#### Design Factors (10 Faktor Desain)

```
1. Enterprise Strategy
   - Visi, misi, dan strategi bisnis organisasi
   
2. Enterprise Goals (Tujuan Perusahaan)
   - Aligned dengan strategi dan tujuan bisnis
   
3. Risk Profile (Profil Risiko)
   - Risk appetite dan tolerance level
   
4. I&T Related Issues
   - Isu-isu yang berkaitan dengan IT dan organisasi
   
5. Threat Landscape
   - Ancaman internal dan eksternal
   
6. Compliance Requirements
   - Requirement regulasi dan compliance
   
7. Role of IT (Peran IT dalam Organisasi)
   - Support/Defense/Factory/Strategic
   
8. Sourcing Model for IT
   - Insourced, Outsourced, Co-sourced
   
9. IT Implementation Methods
   - Waterfall, Agile, Hybrid, DevOps
   
10. Technology Strategy Adoption
    - Legacy, Steady, Progressive, Innovative
```

#### GAMO Objectives (23 Objectives)

**EDM (Evaluate, Direct, Monitor) - 5 Governance Objectives**
- EDM01: Evaluate, Direct and Monitor the Set of Enterprise Goals
- EDM02: Evaluate, Direct and Monitor IT-Related Business Risk
- EDM03: Evaluate, Direct and Monitor IT Compliance
- EDM04: Evaluate, Direct and Monitor IT Governance
- EDM05: Evaluate, Direct and Monitor IT Investments

**APO (Align, Plan, Organize) - 7 Management Objectives**
- APO01: Manage IT Management Framework
- APO02: Manage Strategy
- APO03: Manage Enterprise Architecture
- APO04: Manage Innovation
- APO05: Manage Portfolio
- APO06: Manage Budget and Costs
- APO07: Manage Human Resources

**BAI (Build, Acquire, Implement) - 4 Management Objectives**
- BAI01: Manage Programmes and Projects
- BAI02: Manage Requirements Definition
- BAI03: Manage Solutions Identification and Build
- BAI04: Manage Availability and Capacity

**DSS (Deliver, Service, Support) - 4 Management Objectives**
- DSS01: Manage Operations
- DSS02: Manage Service Requests and Incidents
- DSS03: Manage Problems
- DSS04: Manage Continuity
- DSS05: Manage Security Services

**MEA (Monitor, Evaluate, Assess) - 3 Management Objectives**
- MEA01: Monitor, Evaluate and Assess Performance and Conformance
- MEA02: Monitor, Evaluate and Assess the System of Internal Control
- MEA03: Monitor, Evaluate and Assess Compliance with External Requirements

#### Maturity & Capability Levels

```
Level 0: Incomplete
  - Process not performed or largely ineffective

Level 1: Performed
  - Process is performed; purpose is achieved

Level 2: Managed
  - Process is performed; results are managed

Level 3: Defined
  - Process is defined and tailored; results are predictable

Level 4: Quantitatively Managed
  - Process is measured and controlled

Level 5: Optimizing
  - Process is continually improved and optimized
```

---

## 3. Tech Stack

### Backend
```
Framework       : Laravel 10.x / 11.x
Database        : MySQL 8.0 / PostgreSQL 13+
Authentication  : Laravel Sanctum + Custom JWT
Cache           : Redis (Session & Cache)
Queue           : Laravel Queue (Redis)
File Storage    : AWS S3 / Local Encrypted Storage
API Security    : Sanctum, Rate Limiting, CORS
Encryption      : OpenSSL, Laravel Encryption
```

### Frontend
```
Template Engine : Blade (Laravel)
Admin Template  : Tabler (Bootstrap 5.3+) - https://tabler.io
CSS Framework   : Bootstrap 5.3+ (via Tabler CDN)
JavaScript      : jQuery 3.7.x
Chart Library   : Chart.js 4.x
Data Tables     : DataTables.js
Form Validation : jQuery Validation + Server-side
Icons           : Tabler Icons (included in template)
PDF Generation  : DomPDF / TCPDF (server-side)
Excel Export    : PhpOffice/PhpSpreadsheet
```

#### Frontend Development Guidelines
```
✅ MUST USE Tabler Components
   - All UI components must use Tabler's pre-built components
   - Reference: https://tabler.io/docs/components
   - Do NOT create custom HTML/CSS when Tabler component exists

✅ JavaScript Separation (IMPORTANT)
   - NEVER embed <script> tags directly in Blade files
   - All JavaScript MUST be in separate files: resources/js/
   - Use @push('scripts') in Blade, load files from public/js/
   
   Example Structure:
   resources/js/
   ├── app.js                    # Main application JS
   ├── dashboard.js              # Dashboard specific
   ├── assessments/
   │   ├── create.js            # Assessment creation
   │   ├── answer.js            # Answer questions
   │   └── validation.js        # Form validation
   └── components/
       ├── charts.js            # Chart configurations
       └── datatables.js        # DataTable configurations

   In Blade files:
   @push('scripts')
       <script src="{{ asset('js/assessments/create.js') }}"></script>
   @endpush

✅ CSS Customization
   - Use Tabler CSS variables for theming
   - Custom CSS in: resources/css/custom.css
   - Compile with Vite: npm run build

✅ Asset Management
   - Use Vite for asset compilation
   - CDN for Tabler core (faster, no build needed)
   - Local assets for custom JS/CSS only
```

### Development Tools
```
Version Control : Git with GPG signing
Code Style      : PSR-12 with custom rules
Testing         : PHPUnit / Pest
Security Scan   : SonarQube, PHPStan
Debugging       : Laravel Debugbar (dev only)
Deployment      : Docker / Ubuntu Server 22.04 LTS
CI/CD          : GitHub Actions / GitLab CI
Monitoring     : ELK Stack / New Relic
```

---

## 4. Security Architecture

### 4.1 Authentication & Authorization Security

#### Password Security
```php
// Laravel bcrypt dengan cost 12
Hash::make($password); // bcrypt dengan cost 12
Hash::check($password, $hashedPassword);

// Password Policy
- Minimum 12 karakter
- Uppercase, lowercase, number, special char
- Password history (tidak boleh sama 5 password terakhir)
- Password expiry: 90 hari untuk admin, 180 hari untuk user
- Lockout: 5 failed attempts dalam 15 menit
```

#### Token Security
```php
// Laravel Sanctum Implementation
- Token expiry: 24 jam (customizable per role)
- Refresh token: 7 hari
- Token stored di HTTP-only cookies
- CSRF token untuk setiap request
- Token revocation on logout
- Multiple token per user dengan device tracking
```

#### Two-Factor Authentication (2FA)
```php
// TOTP (Time-based One-Time Password) Implementation
- Google Authenticator / Microsoft Authenticator
- Backup codes untuk recovery
- 2FA mandatory untuk Admin dan Manager
- Optional untuk Assessor dan Viewer
- 30-second time window
```

### 4.2 CSRF (Cross-Site Request Forgery) Protection

```php
// Middleware CSRF Protection
Route::post('/assessments', 'AssessmentController@store')
    ->middleware('verified');

// Blade Template
<form method="POST" action="/assessments">
    @csrf <!-- Token otomatis tergenerate -->
    <!-- form fields -->
</form>

// AJAX Request
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// SameSite Cookie
SESSION_COOKIE_SAMESITE=Lax
COOKIE_SAMESITE=Lax

// CSRF Exemption (hanya API public endpoints)
protected $except = [
    'api/webhook/*'
];
```

### 4.3 Encryption & Data Protection

#### Database Encryption
```php
// Model Encryption
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Assessment extends Model
{
    protected $casts = [
        'sensitive_data' => 'encrypted',
        'evidence_path' => 'encrypted:payload',
    ];
    
    // Encrypt specific columns
    protected $encrypted = [
        'company_name',
        'department_info',
        'assessment_details'
    ];
}

// Manual Encryption
$encrypted = Crypt::encryptString($plaintext);
$decrypted = Crypt::decryptString($encrypted);
```

#### File Encryption
```php
// Evidence File Encryption
class EvidenceService
{
    public function storeEncrypted($file)
    {
        $encrypted = Crypt::encryptString(
            file_get_contents($file->getRealPath())
        );
        
        Storage::disk('encrypted')
            ->put($filename, $encrypted);
    }
    
    public function getDecrypted($filename)
    {
        $encrypted = Storage::disk('encrypted')
            ->get($filename);
            
        return Crypt::decryptString($encrypted);
    }
}
```

#### API Communication
```
HTTPS/TLS 1.2+ (mandatory)
- Certificate pinning untuk mobile apps
- Perfect Forward Secrecy (PFS)
- HTTP Strict Transport Security (HSTS)
- Certificate transparency monitoring
```

### 4.4 Input Validation & Output Encoding

```php
// Server-side Validation
public function storeAssessment(AssessmentRequest $request)
{
    // Validation dilakukan di AssessmentRequest
    $validated = $request->validated();
}

// Form Request Validation
class AssessmentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-\.]+$/',
            'description' => 'nullable|string|max:1000',
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date|after:today',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx',
        ];
    }
}

// Output Encoding (Blade Auto-escaping)
<!-- Automatically escaped -->
<h1>{{ $assessment->title }}</h1>

<!-- Raw output (use dengan hati-hati) -->
<h1>{!! $assessment->title !!}</h1>

// XSS Prevention in API Response
return response()->json([
    'title' => htmlspecialchars($assessment->title),
    'description' => strip_tags($assessment->description)
]);
```

### 4.5 SQL Injection Prevention

```php
// Prepared Statements (Laravel Eloquent)
// ✓ AMAN
$assessments = Assessment::where('company_id', $companyId)
    ->get();

// ✓ AMAN dengan parameter binding
$assessments = Assessment::whereRaw(
    'company_id = ?',
    [$companyId]
)->get();

// ✗ BERBAHAYA (avoid)
Assessment::whereRaw("company_id = $companyId")->get();
```

### 4.6 Authentication Bypass Prevention

```php
// Middleware Protection
class VerifyRoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        if (!Auth::user()->hasRole($role)) {
            abort(403, 'Unauthorized');
        }
        
        return $next($request);
    }
}

// Route Protection
Route::group(['middleware' => 'role:admin,manager'], function () {
    Route::get('/assessments', 'AssessmentController@index');
});
```

### 4.7 Session & Cookie Security

```env
# .env Configuration
SESSION_DRIVER=cookie
SESSION_LIFETIME=30 (minutes)
SESSION_EXPIRE_ON_CLOSE=true
SESSION_ENCRYPT=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=Lax

COOKIE_SECURE=true (HTTPS only)
COOKIE_HTTP_ONLY=true (prevent JavaScript access)
COOKIE_SAME_SITE=Lax
COOKIE_DOMAIN=yourdomain.com
```

### 4.8 Rate Limiting & Brute Force Protection

```php
// API Rate Limiting
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/assessments', 'AssessmentController@store');
});

// Custom Rate Limiting
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by(
        $request->ip() . '|' . $request->input('email')
    );
});

// Login Attempt Tracking
class LoginAttempt extends Model
{
    public static function recordAttempt($email, $ip)
    {
        self::create([
            'email' => $email,
            'ip_address' => $ip,
            'attempted_at' => now()
        ]);
    }
    
    public static function isLocked($email, $ip)
    {
        $attempts = self::where('email', $email)
            ->where('ip_address', $ip)
            ->where('attempted_at', '>', now()->subMinutes(15))
            ->count();
            
        return $attempts >= 5;
    }
}
```

### 4.9 File Upload Security

```php
// Secure File Upload
class EvidenceUploadService
{
    public function upload($file)
    {
        // Validate file type
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        $ext = $file->getClientOriginalExtension();
        
        if (!in_array(strtolower($ext), $allowed)) {
            throw new InvalidFileException();
        }
        
        // Validate file size
        if ($file->getSize() > 10 * 1024 * 1024) { // 10MB
            throw new FileTooLargeException();
        }
        
        // Scan for malware (optional - requires ClamAV)
        if (!$this->scanForMalware($file)) {
            throw new MalwareDetectedException();
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $ext;
        
        // Store outside public folder
        $path = Storage::disk('private')
            ->putFileAs('evidence', $file, $filename);
        
        return [
            'filename' => $filename,
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_at' => now()
        ];
    }
    
    private function scanForMalware($file)
    {
        // Implement ClamAV scanning
        // or use external antivirus API
        return true;
    }
}

// Storage Configuration
// config/filesystems.php
'disks' => [
    'private' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
        'visibility' => 'private',
    ]
]
```

### 4.10 API Security

```php
// API Authentication with Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/assessments', 'AssessmentController@index');
    Route::post('/assessments', 'AssessmentController@store');
});

// API Rate Limiting
Route::middleware('throttle:api')->group(function () {
    Route::get('/assessments', 'AssessmentController@index');
});

// CORS Configuration
// config/cors.php
'allowed_origins' => [
    'https://yourdomain.com',
    'https://api.yourdomain.com'
],
'allowed_headers' => ['*'],
'exposed_headers' => ['Authorization'],
'supports_credentials' => true,

// API Versioning
Route::prefix('api/v1')->group(function () {
    Route::get('/assessments', 'AssessmentController@index');
});

// Request Signing (optional)
class SignedRequest
{
    public static function generate($data, $secret)
    {
        return hash_hmac('sha256', json_encode($data), $secret);
    }
}
```

### 4.11 Audit Logging & Monitoring

```php
// Comprehensive Audit Trail
class AuditLogService
{
    public static function log($action, $model, $data = [])
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => class_basename($model),
            'entity_id' => $model->id ?? null,
            'old_values' => json_encode($model->getOriginal()),
            'new_values' => json_encode($model->getAttributes()),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }
}

// Middleware untuk Auto-logging
class AuditLoggingMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        $this->logActivity(
            $request->method(),
            $request->path(),
            $response->status()
        );
        
        return $response;
    }
}
```

### 4.12 Environment Security

```env
# Production Environment
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error

# Encryption Keys
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxx
ENCRYPTION_KEY=xxxxxxxxxxxxxxxxxxxxx

# Database (use strong credentials)
DB_PASSWORD=SecureP@ssw0rd!

# Sensitive Data (use secrets manager)
MAIL_PASSWORD=xxxxxxxxxxxxxxxxxxxxx
API_KEY=xxxxxxxxxxxxxxxxxxxxx
AWS_SECRET_ACCESS_KEY=xxxxxxxxxxxxxxxxxxxxx

# Security Headers
SECURE_HEADERS=true
```

### 4.13 Security Headers Configuration

```php
// config/security-headers.php
return [
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block',
    'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
    'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net;",
    'Referrer-Policy' => 'strict-origin-when-cross-origin',
    'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
];

// Middleware
class SetSecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        foreach (config('security-headers') as $header => $value) {
            $response->header($header, $value);
        }
        
        return $response;
    }
}
```

### 4.14 Dependency Vulnerability Scanning

```bash
# Check for vulnerable packages
composer audit

# NPM dependencies
npm audit

# OWASP Top 10 scanning
composer require --dev sensiolabs/security-checker
php ./vendor/bin/security-checker security:check

# SonarQube integration
composer require --dev nunomaduro/phpstan-rules
./vendor/bin/phpstan analyse app
```

---

## 5. Alur Aplikasi

### 3.1 User Flow - Assessment Process

```
┌─────────────────────────────────────────────────────────────┐
│                    USER AUTHENTICATION                      │
│                   (Login/Register)                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
         ┌─────────────┴─────────────┐
         │                           │
    ┌────▼────┐              ┌──────▼──────┐
    │  Admin  │              │  Assessor   │
    │ Access  │              │  Access     │
    └────┬────┘              └──────┬──────┘
         │                          │
         │                    ┌─────▼────────────┐
         │                    │ View Dashboard   │
         │                    │ Lihat Assessment │
         │                    └─────┬────────────┘
         │                          │
    ┌────▼──────────────────────────▼─────┐
    │                                      │
    │   Assessment Interview Session       │
    │   - Select Domain (PO/AI/DS/ME)      │
    │   - Answer Questions                 │
    │   - Input Evidence                   │
    │   - Rate Maturity Level              │
    │                                      │
    └────┬──────────────────────────────────┘
         │
    ┌────▼────────────────────────────┐
    │  Data Validation & Calculation  │
    │  - Calculate Score              │
    │  - Determine Maturity Level     │
    │  - Generate Recommendations     │
    └────┬─────────────────────────────┘
         │
    ┌────▼────────────────────────────┐
    │  Generate Report                │
    │  - Dashboard Views              │
    │  - PDF Export                   │
    │  - Excel Export                 │
    └────┬─────────────────────────────┘
         │
    ┌────▼────────────────────────────┐
    │  Action Plan                    │
    │  - Create Recommendations       │
    │  - Track Progress               │
    │  - Re-assessment Schedule       │
    └─────────────────────────────────┘
```

### 3.2 Admin Flow

```
Dashboard Admin
    ├── User Management
    │   ├── Create/Edit/Delete User
    │   ├── Assign Role
    │   └── Reset Password
    ├── Assessment Management
    │   ├── Create Assessment
    │   ├── View All Assessments
    │   ├── Generate Reports
    │   └── Archive Assessment
    ├── Question Management
    │   ├── Create/Edit Question
    │   ├── Manage Question Bank
    │   └── Bulk Import
    ├── System Configuration
    │   ├── Company Settings
    │   ├── COBIT Framework Config
    │   └── Email Configuration
    └── Audit Log
        ├── View All Activities
        ├── User Activities
        └── System Changes
```

### 5.1 Assessment Flow dengan Design Factors

```
┌────────────────────────────────────────────────────────┐
│         PHASE 1: Design Factor Selection               │
│     (Tailoring Assessment Scope berdasarkan konteks)   │
└───────────────┬────────────────────────────────────────┘
                │
        ┌───────▼────────┐
        │ Select Design  │
        │ Factors (10):  │
        │ - Enterprise   │
        │   Strategy     │
        │ - Enterprise   │
        │   Goals        │
        │ - Risk Profile │
        │ - I&T Issues   │
        │ - Threats      │
        │ - Compliance   │
        │ - Role of IT   │
        │ - Sourcing     │
        │ - Methods      │
        │ - Technology   │
        └───────┬────────┘
                │
┌───────────────▼──────────────────────────────────────┐
│   PHASE 2: GAMO Objectives Selection                 │
│ (Select applicable EDM/APO/BAI/DSS/MEA Objectives)  │
└───────────────┬──────────────────────────────────────┘
                │
        ┌───────▼────────────────┐
        │ Select GAMO Areas:     │
        │ ☑ EDM01-EDM05          │
        │ ☑ APO01-APO07          │
        │ ☑ BAI01-BAI04          │
        │ ☑ DSS01-DSS05          │
        │ ☐ MEA01-MEA03          │
        └───────┬────────────────┘
                │
┌───────────────▼──────────────────────────────────────┐
│   PHASE 3: Assessment Setup                          │
│ (Tentukan scope, timeline, assessor)                │
└───────────────┬──────────────────────────────────────┘
                │
        ┌───────▼────────────────────────┐
        │ Create Assessment:             │
        │ - Title & Description          │
        │ - Assign Assessor              │
        │ - Set Timeline                 │
        │ - Define Target Maturity Level │
        │ - Add Supporting Docs          │
        └───────┬────────────────────────┘
                │
┌───────────────▼──────────────────────────────────────┐
│   PHASE 4: Assessment Execution                      │
│ (Conduct interview & evidence collection)            │
└───────────────┬──────────────────────────────────────┘
                │
        ┌───────▼──────────────────────┐
        │ For each GAMO Objective:     │
        │ 1. Review guidance docs      │
        │ 2. Conduct interviews        │
        │ 3. Answer questions          │
        │ 4. Upload evidence           │
        │ 5. Rate Maturity Level       │
        │    (0-5 dengan capability     │
        │     assessment per level)     │
        │ 6. Add assessment comments   │
        │ 7. Save & Continue / Submit  │
        └───────┬──────────────────────┘
                │
┌───────────────▼──────────────────────────────────────┐
│   PHASE 5: Data Validation & Scoring                 │
│ (Validate completeness & calculate maturity level)   │
└───────────────┬──────────────────────────────────────┘
                │
        ┌───────▼──────────────────────────┐
        │ - Validate all questions answered│
        │ - Check evidence completeness    │
        │ - Calculate Capability Scores    │
        │ - Determine Maturity Levels      │
        │ - Generate Recommendations       │
        │ - Analyze gaps                   │
        └───────┬──────────────────────────┘
                │
┌───────────────▼──────────────────────────────────────┐
│   PHASE 6: Review & Approval                         │
│ (Manager review & management approval)               │
└───────────────┬──────────────────────────────────────┘
                │
        ┌───────▼──────────────────────┐
        │ Manager Review:              │
        │ - Validate assessment data   │
        │ - Add review comments        │
        │ - Approve or Request Banding │
        │                              │
        │ Management Approval:         │
        │ - Final sign-off             │
        │ - Lock assessment            │
        └───────┬──────────────────────┘
                │
┌───────────────▼──────────────────────────────────────┐
│   PHASE 6.5: Banding/Appeal (Opsional)               │
│ (Challenge & refine hasil assessment jika perlu)     │
└───────────────┬──────────────────────────────────────┘
                │
        ┌───────▼──────────────────────────────────────┐
        │ BANDING PROCESS:                            │
        │ 1. Assessor/Manager dapat mengajukan         │
        │    banding untuk specific GAMO              │
        │                                              │
        │ 2. Alasan banding:                           │
        │    - Evidence tidak lengkap                  │
        │    - Pertanyaan kurang detail               │
        │    - Maturity level tidak sesuai            │
        │    - Temuan baru dari stakeholder           │
        │                                              │
        │ 3. Banding Handler dapat:                    │
        │    - Tambah/edit jawaban sebelumnya         │
        │    - Upload evidence tambahan                │
        │    - Re-conduct interview jika perlu        │
        │    - Update maturity level scoring          │
        │    - Add detailed banding notes             │
        │                                              │
        │ 4. System Records:                           │
        │    - old_values (nilai sebelum banding)     │
        │    - new_values (nilai setelah banding)     │
        │    - banding_reason & detailed notes        │
        │    - banding_handler info                   │
        │    - timestamp & audit trail                │
        │                                              │
        │ 5. Banding Status:                           │
        │    - Draft (belum final)                    │
        │    - Submitted (tunggu review)              │
        │    - Approved (diterima)                    │
        │    - Rejected (ditolak)                     │
        │                                              │
        │ 6. Multiple Banding:                         │
        │    - Bisa multiple rounds jika perlu        │
        │    - Track history semua banding rounds     │
        └───────┬──────────────────────────────────────┘
                │
┌───────────────▼──────────────────────────────────────┐
│   PHASE 7: Reporting & Action Planning               │
│ (Generate reports & create action plan)              │
└───────────────┬──────────────────────────────────────┘
                │
        ┌───────▼─────────────────────────────┐
        │ - Generate Assessment Report        │
        │ - Create Dashboard Visualizations   │
        │ - Develop Recommendations           │
        │ - Assign Action Items               │
        │ - Schedule Follow-up Assessment     │
        │ - Export PDF/Excel Reports          │
        │ - Distribute to Stakeholders        │
        └───────┬─────────────────────────────┘
                │
┌───────────────▼──────────────────────────────────────┐
│   PHASE 8: Follow-up & Continuous Improvement        │
│ (Track improvements & schedule reassessment)         │
└────────────────────────────────────────────────────────┘
```

### 5.2 Target Maturity Level & Capability Scoring per GAMO

Setiap GAMO Objective memiliki **target maturity level** yang dapat diset independently dan memiliki **capability assessment per level**.

#### Target Maturity Level Configuration

```
Setiap GAMO Objective dapat memiliki setting tersendiri:
┌────────────────────────────────────────────────────┐
│ Current Maturity Level: [0-5]                      │
│ Target Maturity Level:  [0-5] (Set independently) │
│ Gap Analysis:          Target - Current            │
│ Priority:              HIGH/MEDIUM/LOW (auto)      │
│ Effort Estimation:     Effort untuk reach target   │
│ Timeline:              Expected achievement date   │
└────────────────────────────────────────────────────┘

Contoh dari screenshot Anda (EDM02):
├── Level 2: Compliance 1 (Full)
├── Level 3: Compliance 0.85 (High)
├── Level 4: Compliance 0.39 (Medium)
├── Rata-rata: 3.24 (Overall Score)
└── Weight: Aksi penentuan untuk setiap level
```

#### Capability Assessment Detail per GAMO

```
Level 0 - Incomplete
└── Not performed or ineffective

Level 1 - Performed  
├── Compliance Score: 100%
├── Questions: 3-5 per GAMO
├── Evidence Required: Min 1
└── Status: Process executed, goal achieved

Level 2 - Managed (dari screenshot: score 1)
├── Compliance Score: 100% (full compliance)
├── Questions: 4-6 per GAMO
├── Evidence Required: Min 2
├── Documentation: Yes (standards, procedures)
└── Status: Results managed & monitored

Level 3 - Defined (dari screenshot: score 0.85)
├── Compliance Score: 0.85 (High)
├── Questions: 5-7 per GAMO
├── Evidence Required: Min 3
├── Requirements: Role definition, training
└── Status: Process tailored, predictable

Level 4 - Quantitatively Managed (dari screenshot: score 0.39)
├── Compliance Score: 0.39 (Medium)
├── Questions: 6-8 per GAMO
├── Evidence Required: Min 4
├── Metrics: KPI, analytics, control
└── Status: Process measured & controlled

Level 5 - Optimizing
├── Questions: 7-10 per GAMO
├── Evidence Required: Min 5
├── Innovation: Benchmarking, continuous improvement
└── Status: Continually optimized
```

---

### 5.3 Security Flow dalam Assessment

```
┌─────────────────────────────────────────────────────┐
│              USER LOGIN                             │
└────────────┬────────────────────────────────────────┘
             │
    ┌────────▼────────────┐
    │ Validate Credentials│
    │ (bcrypt + rate      │
    │  limiting)          │
    └────────┬────────────┘
             │
    ┌────────▼────────────────┐
    │ Check 2FA (if required) │
    │ (TOTP/SMS)              │
    └────────┬────────────────┘
             │
    ┌────────▼──────────────────┐
    │ Generate Secure Token     │
    │ (JWT + HTTP-only cookie)  │
    └────────┬──────────────────┘
             │
    ┌────────▼────────────────────┐
    │ Check RBAC & Permissions    │
    │ (Role-based access control) │
    └────────┬────────────────────┘
             │
    ┌────────▼──────────────────┐
    │ Initialize Session         │
    │ (Secure + encrypted)       │
    └────────┬──────────────────┘
             │
    ┌────────▼──────────────────────────┐
    │ Application Access Granted        │
    │ - Log access in audit trail       │
    │ - Track user activity             │
    │ - CSRF token generated            │
    │ - Security headers applied        │
    └───────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│         ASSESSMENT DATA HANDLING                    │
└────────────┬────────────────────────────────────────┘
             │
    INPUT VALIDATION
    ┌────────▼──────────────────┐
    │ - Server-side validation  │
    │ - Input sanitization      │
    │ - Type checking           │
    │ - Max length validation   │
    └────────┬──────────────────┘
             │
    ENCRYPTION
    ┌────────▼──────────────────┐
    │ - Sensitive data encrypted│
    │ - Evidence files encrypted│
    │ - HTTPS/TLS 1.2+ required │
    │ - Perfect Forward Secrecy │
    └────────┬──────────────────┘
             │
    DATABASE OPERATIONS
    ┌────────▼──────────────────┐
    │ - Prepared statements     │
    │ - Parameterized queries   │
    │ - No SQL injection        │
    │ - Query logging (safe)    │
    └────────┬──────────────────┘
             │
    AUDIT LOGGING
    ┌────────▼──────────────────┐
    │ - Log all changes         │
    │ - Track user actions      │
    │ - Record IP & user agent  │
    │ - Timestamp all events    │
    └────────┬──────────────────┘
             │
    OUTPUT ENCODING
    ┌────────▼──────────────────┐
    │ - HTML encoding           │
    │ - XSS prevention          │
    │ - JSON escaping           │
    │ - Safe API responses      │
    └───────────────────────────┘

┌─────────────────────────────────────────────────────┐
│         FILE UPLOAD HANDLING                        │
└────────────┬────────────────────────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Validate File Type            │
    │ (Whitelist extensions)        │
    └────────┬───────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Validate File Size            │
    │ (Max 10MB per file)           │
    └────────┬───────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Scan for Malware              │
    │ (ClamAV / antivirus API)      │
    └────────┬───────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Generate Unique Filename      │
    │ (Remove original name)        │
    └────────┬───────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Encrypt File Content          │
    │ (AES-256-CBC)                 │
    └────────┬───────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Store Outside Public Folder   │
    │ (Not accessible directly)     │
    └────────┬───────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Record in Database            │
    │ - Filename, size, upload time │
    │ - Uploader info               │
    │ - Access logs                 │
    └───────────────────────────────┘
```

---

## 6. Desain Database

### 4.1 Schema Relasional

#### Tabel: users
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    position VARCHAR(100),
    role_id BIGINT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

#### Tabel: roles
```sql
CREATE TABLE roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Data
INSERT INTO roles (name, description) VALUES
('Super Admin', 'Full system access'),
('Admin', 'System and user management'),
('Manager', 'Assessment management and reporting'),
('Assessor', 'Conduct assessment'),
('Viewer', 'View-only access');
```

#### Tabel: permissions
```sql
CREATE TABLE permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    module VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Tabel: role_permissions
```sql
CREATE TABLE role_permissions (
    role_id BIGINT NOT NULL,
    permission_id BIGINT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);
```

#### Tabel: assessments
```sql
CREATE TABLE assessments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    company_id BIGINT NOT NULL,
    assessment_type ENUM('initial', 'periodic', 'specific') DEFAULT 'initial',
    status ENUM('draft', 'in_progress', 'completed', 'reviewed', 'archived') DEFAULT 'draft',
    start_date DATE,
    end_date DATE,
    created_by BIGINT NOT NULL,
    reviewed_by BIGINT,
    approved_by BIGINT,
    progress_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    KEY idx_status (status),
    KEY idx_company (company_id)
);
```

#### Tabel: companies
```sql
CREATE TABLE companies (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255),
    industry VARCHAR(100),
    size ENUM('startup', 'sme', 'enterprise') DEFAULT 'sme',
    established_year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Tabel: cobit_domains
```sql
CREATE TABLE cobit_domains (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    domain_order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Data
INSERT INTO cobit_domains (code, name, description, domain_order) VALUES
('PO', 'Plan and Organize', 'Planning and organizing IT to deliver value', 1),
('AI', 'Acquire and Implement', 'Acquiring and implementing IT solutions', 2),
('DS', 'Deliver and Support', 'Delivering IT services and support', 3),
('ME', 'Monitor and Evaluate', 'Monitoring and evaluating IT performance', 4);
```

#### Tabel: cobit_processes
```sql
CREATE TABLE cobit_processes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    domain_id BIGINT NOT NULL,
    description TEXT,
    process_order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (domain_id) REFERENCES cobit_domains(id)
);

-- Example Seed Data
INSERT INTO cobit_processes (code, name, domain_id, description, process_order) VALUES
('PO1', 'Define a Strategic IT Plan', 1, 'Define IT strategy aligned with business', 1),
('PO2', 'Define the Information Architecture', 1, 'Define information architecture', 2),
('AI1', 'Identify Automated Solutions', 2, 'Identify and evaluate solutions', 1),
('AI2', 'Acquire and Maintain Application Software', 2, 'Acquire and maintain applications', 2),
('DS1', 'Define and Manage Service Levels', 3, 'Define and manage SLA', 1),
('DS2', 'Manage Third-Party Services', 3, 'Manage third-party providers', 2),
('ME1', 'Monitor and Evaluate IT Performance', 4, 'Monitor IT performance', 1),
('ME2', 'Monitor and Evaluate Internal Control', 4, 'Monitor control effectiveness', 2);
```

#### Tabel: assessment_answers
```sql
CREATE TABLE assessment_answers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,
    answer_text TEXT,
    maturity_level INT DEFAULT 0,
    score INT,
    evidence_file VARCHAR(255),
    notes TEXT,
    answered_by BIGINT NOT NULL,
    answered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id),
    FOREIGN KEY (answered_by) REFERENCES users(id),
    UNIQUE KEY unique_answer (assessment_id, question_id)
);
```

#### Tabel: questions
```sql
CREATE TABLE questions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    text TEXT NOT NULL,
    process_id BIGINT NOT NULL,
    category VARCHAR(100),
    question_type ENUM('text', 'rating', 'multiple_choice', 'yes_no') DEFAULT 'text',
    required BOOLEAN DEFAULT TRUE,
    order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (process_id) REFERENCES cobit_processes(id),
    KEY idx_process (process_id)
);
```

#### Tabel: assessment_scores
```sql
CREATE TABLE assessment_scores (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    process_id BIGINT NOT NULL,
    current_maturity_level DECIMAL(3,2) DEFAULT 0,
    target_maturity_level DECIMAL(3,2) DEFAULT 3,
    capability_score DECIMAL(5,2),
    percentage_complete INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (process_id) REFERENCES cobit_processes(id),
    UNIQUE KEY unique_score (assessment_id, process_id)
);
```

#### Tabel: recommendations
```sql
CREATE TABLE recommendations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    process_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    estimated_effort VARCHAR(50),
    responsible_person_id BIGINT,
    target_date DATE,
    status ENUM('open', 'in_progress', 'completed', 'closed') DEFAULT 'open',
    progress_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (process_id) REFERENCES cobit_processes(id),
    FOREIGN KEY (responsible_person_id) REFERENCES users(id)
);
```

#### Tabel: audit_logs
```sql
CREATE TABLE audit_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50),
    entity_type VARCHAR(100),
    entity_id BIGINT,
    old_values LONGTEXT,
    new_values LONGTEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    KEY idx_user (user_id),
    KEY idx_created (created_at)
);
```

### 6.1 Schema Relasional - COBIT 2019

#### Tabel: design_factors
```sql
CREATE TABLE design_factors (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT,
    factor_order INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_code (code),
    KEY idx_active (is_active)
);

-- Seed Data
INSERT INTO design_factors (code, name, description, factor_order) VALUES
('ES', 'Enterprise Strategy', 'Visi, misi, dan strategi bisnis organisasi', 1),
('EG', 'Enterprise Goals', 'Tujuan perusahaan yang aligned dengan strategi', 2),
('RP', 'Risk Profile', 'Risk appetite dan tolerance level organisasi', 3),
('ITI', 'I&T Related Issues', 'Isu-isu yang berkaitan dengan IT', 4),
('TL', 'Threat Landscape', 'Ancaman internal dan eksternal', 5),
('CR', 'Compliance Requirements', 'Requirement regulasi dan compliance', 6),
('RIT', 'Role of IT', 'Peran IT dalam organisasi (Support/Defense/Factory/Strategic)', 7),
('SM', 'Sourcing Model for IT', 'Model sumber IT (Insourced/Outsourced/Co-sourced)', 8),
('IM', 'IT Implementation Methods', 'Metode implementasi IT (Waterfall/Agile/Hybrid/DevOps)', 9),
('TA', 'Technology Strategy Adoption', 'Strategi adopsi teknologi (Legacy/Steady/Progressive/Innovative)', 10);
```

#### Tabel: assessment_design_factors
```sql
CREATE TABLE assessment_design_factors (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    design_factor_id BIGINT NOT NULL,
    selected_value VARCHAR(500),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (design_factor_id) REFERENCES design_factors(id),
    UNIQUE KEY unique_assessment_factor (assessment_id, design_factor_id)
);
```

#### Tabel: gamo_objectives (Updated dengan deskripsi Indonesia)
```sql
CREATE TABLE gamo_objectives (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    name_id VARCHAR(255),
    description LONGTEXT,
    description_id LONGTEXT,
    category ENUM('EDM', 'APO', 'BAI', 'DSS', 'MEA') NOT NULL,
    objective_order INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_code (code),
    KEY idx_category (category),
    KEY idx_active (is_active)
);

-- Seed Data untuk 23 GAMO Objectives (dengan deskripsi Indonesia)
INSERT INTO gamo_objectives (code, name, name_id, category, description, description_id, objective_order) VALUES
-- EDM (5 objectives)
('EDM01', 'Evaluate, Direct and Monitor the Set of Enterprise Goals', 'Evaluasi, Arahkan, dan Pantau Pemenuhan Tujuan Perusahaan', 'EDM', 
 'Ensure that business goals and objectives are understood, achieved, and monitored in alignment with IT strategy',
 'Memastikan tujuan dan objektif bisnis dipahami, dicapai, dan dipantau sesuai dengan strategi IT', 1),

('EDM02', 'Evaluate, Direct and Monitor IT-Related Business Risk', 'Evaluasi, Arahkan, dan Pantau Risiko Bisnis Terkait IT', 'EDM',
 'Manage and monitor IT-related business risks and ensure proper risk mitigation strategies are in place',
 'Kelola dan pantau risiko bisnis terkait IT serta pastikan strategi mitigasi risiko yang tepat diterapkan', 2),

('EDM03', 'Evaluate, Direct and Monitor IT Compliance', 'Evaluasi, Arahkan, dan Pantau Kepatuhan IT', 'EDM',
 'Ensure IT operations are compliant with laws, regulations, and contractual obligations',
 'Pastikan operasi IT mematuhi hukum, regulasi, dan kewajiban kontraktual', 3),

('EDM04', 'Evaluate, Direct and Monitor IT Governance', 'Evaluasi, Arahkan, dan Pantau Governance IT', 'EDM',
 'Establish and monitor IT governance framework to ensure effective management and oversight',
 'Tetapkan dan pantau kerangka kerja governance IT untuk memastikan manajemen dan pengawasan yang efektif', 4),

('EDM05', 'Evaluate, Direct and Monitor IT Investments', 'Evaluasi, Arahkan, dan Pantau Investasi IT', 'EDM',
 'Manage and optimize IT investments to ensure proper allocation and value realization',
 'Kelola dan optimalkan investasi IT untuk memastikan alokasi yang tepat dan realisasi nilai', 5),

-- APO (7 objectives)
('APO01', 'Manage IT Management Framework', 'Kelola Kerangka Kerja Manajemen IT', 'APO',
 'Establish and maintain an integrated IT management framework aligned with business objectives',
 'Tetapkan dan pertahankan kerangka kerja manajemen IT yang terintegrasi sesuai dengan tujuan bisnis', 1),

('APO02', 'Manage Strategy', 'Kelola Strategi', 'APO',
 'Develop and maintain IT strategy aligned with business strategy and stakeholder needs',
 'Kembangkan dan pertahankan strategi IT yang selaras dengan strategi bisnis dan kebutuhan pemangku kepentingan', 2),

('APO03', 'Manage Enterprise Architecture', 'Kelola Arsitektur Enterprise', 'APO',
 'Define and maintain enterprise architecture to guide IT decision-making and transformation',
 'Tentukan dan pertahankan arsitektur enterprise untuk membimbing pengambilan keputusan IT dan transformasi', 3),

('APO04', 'Manage Innovation', 'Kelola Inovasi', 'APO',
 'Identify and evaluate IT innovations to maintain competitive advantage',
 'Identifikasi dan evaluasi inovasi IT untuk mempertahankan keunggulan kompetitif', 4),

('APO05', 'Manage Portfolio', 'Kelola Portfolio', 'APO',
 'Manage IT portfolio to ensure optimal allocation of resources and value delivery',
 'Kelola portfolio IT untuk memastikan alokasi sumber daya yang optimal dan pengiriman nilai', 5),

('APO06', 'Manage Budget and Costs', 'Kelola Budget dan Biaya', 'APO',
 'Plan, manage, and control IT budget and costs effectively',
 'Rencanakan, kelola, dan kontrol budget dan biaya IT secara efektif', 6),

('APO07', 'Manage Human Resources', 'Kelola Sumber Daya Manusia', 'APO',
 'Ensure IT department has appropriate skills, competencies, and organizational structure',
 'Pastikan departemen IT memiliki keterampilan, kompetensi, dan struktur organisasi yang tepat', 7),

-- BAI (4 objectives)
('BAI01', 'Manage Programmes and Projects', 'Kelola Program dan Proyek', 'BAI',
 'Plan and execute IT programmes and projects according to approved plans and governance',
 'Rencanakan dan eksekusi program dan proyek IT sesuai rencana dan governance yang disetujui', 1),

('BAI02', 'Manage Requirements Definition', 'Kelola Definisi Requirement', 'BAI',
 'Gather, document, and manage IT requirements from business stakeholders',
 'Kumpulkan, dokumentasikan, dan kelola requirement IT dari pemangku kepentingan bisnis', 2),

('BAI03', 'Manage Solutions Identification and Build', 'Kelola Identifikasi dan Pembangunan Solusi', 'BAI',
 'Identify, design, build, and implement IT solutions to address business requirements',
 'Identifikasi, desain, bangun, dan implementasikan solusi IT untuk mengatasi requirement bisnis', 3),

('BAI04', 'Manage Availability and Capacity', 'Kelola Ketersediaan dan Kapasitas', 'BAI',
 'Plan and manage IT availability and capacity to meet current and future business demands',
 'Rencanakan dan kelola ketersediaan dan kapasitas IT untuk memenuhi permintaan bisnis saat ini dan masa depan', 4),

-- DSS (5 objectives)
('DSS01', 'Manage Operations', 'Kelola Operasi', 'DSS',
 'Execute and manage IT operations to ensure reliable and efficient delivery of IT services',
 'Eksekusi dan kelola operasi IT untuk memastikan pengiriman layanan IT yang andal dan efisien', 1),

('DSS02', 'Manage Service Requests and Incidents', 'Kelola Permintaan Layanan dan Insiden', 'DSS',
 'Process and manage IT service requests and incidents to minimize disruption',
 'Proses dan kelola permintaan layanan IT dan insiden untuk meminimalkan gangguan', 2),

('DSS03', 'Manage Problems', 'Kelola Masalah', 'DSS',
 'Identify, analyze, and resolve problems to prevent service disruptions',
 'Identifikasi, analisis, dan selesaikan masalah untuk mencegah gangguan layanan', 3),

('DSS04', 'Manage Continuity', 'Kelola Kontinuitas', 'DSS',
 'Plan and ensure business continuity of IT services during disruptions',
 'Rencanakan dan pastikan kontinuitas bisnis layanan IT selama gangguan', 4),

('DSS05', 'Manage Security Services', 'Kelola Layanan Keamanan', 'DSS',
 'Implement and maintain security controls to protect IT assets and data',
 'Implementasikan dan pertahankan kontrol keamanan untuk melindungi aset dan data IT', 5),

-- MEA (3 objectives)
('MEA01', 'Monitor, Evaluate and Assess Performance and Conformance', 'Pantau, Evaluasi, dan Asesmen Kinerja dan Kesesuaian', 'MEA',
 'Monitor IT performance and conformance to ensure objectives are being met',
 'Pantau kinerja IT dan kesesuaian untuk memastikan tujuan tercapai', 1),

('MEA02', 'Monitor, Evaluate and Assess the System of Internal Control', 'Pantau, Evaluasi, dan Asesmen Sistem Pengendalian Internal', 'MEA',
 'Evaluate the effectiveness of IT internal control systems',
 'Evaluasi efektivitas sistem pengendalian internal IT', 2),

('MEA03', 'Monitor, Evaluate and Assess Compliance with External Requirements', 'Pantau, Evaluasi, dan Asesmen Kepatuhan Terhadap Requirement Eksternal', 'MEA',
 'Monitor IT compliance with external laws, regulations, and standards',
 'Pantau kepatuhan IT terhadap hukum, regulasi, dan standar eksternal', 3);
```

#### Tabel: assessments (Updated untuk COBIT 2019)
```sql
CREATE TABLE assessments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    company_id BIGINT NOT NULL,
    assessment_type ENUM('initial', 'periodic', 'specific') DEFAULT 'initial',
    scope_type ENUM('full', 'tailored') DEFAULT 'tailored',
    status ENUM('draft', 'in_progress', 'completed', 'reviewed', 'approved', 'archived') DEFAULT 'draft',
    start_date DATE,
    end_date DATE,
    created_by BIGINT NOT NULL,
    reviewed_by BIGINT,
    approved_by BIGINT,
    progress_percentage INT DEFAULT 0,
    overall_maturity_level DECIMAL(3,2),
    is_encrypted BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    KEY idx_status (status),
    KEY idx_company (company_id),
    KEY idx_created (created_at)
);
```

#### Tabel: assessment_gamo_selections
```sql
CREATE TABLE assessment_gamo_selections (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    gamo_objective_id BIGINT NOT NULL,
    is_selected BOOLEAN DEFAULT TRUE,
    selection_reason TEXT,
    selected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (gamo_objective_id) REFERENCES gamo_objectives(id),
    UNIQUE KEY unique_selection (assessment_id, gamo_objective_id)
);
```

#### Tabel: gamo_questions (Updated)
```sql
CREATE TABLE gamo_questions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    gamo_objective_id BIGINT NOT NULL,
    question_text LONGTEXT NOT NULL,
    guidance TEXT,
    evidence_requirement TEXT,
    question_type ENUM('text', 'rating', 'multiple_choice', 'yes_no', 'evidence') DEFAULT 'text',
    maturity_level INT DEFAULT 1,
    required BOOLEAN DEFAULT TRUE,
    question_order INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (gamo_objective_id) REFERENCES gamo_objectives(id),
    KEY idx_gamo (gamo_objective_id),
    KEY idx_active (is_active)
);
```

#### Tabel: assessment_answers (Enhanced)
```sql
CREATE TABLE assessment_answers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,
    gamo_objective_id BIGINT NOT NULL,
    answer_text LONGTEXT,
    answer_json JSON,
    maturity_level INT DEFAULT 0,
    capability_score DECIMAL(5,2),
    is_encrypted BOOLEAN DEFAULT TRUE,
    evidence_file VARCHAR(255),
    evidence_encrypted BOOLEAN DEFAULT TRUE,
    notes TEXT,
    answered_by BIGINT NOT NULL,
    answered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES gamo_questions(id),
    FOREIGN KEY (gamo_objective_id) REFERENCES gamo_objectives(id),
    FOREIGN KEY (answered_by) REFERENCES users(id),
    UNIQUE KEY unique_answer (assessment_id, question_id),
    KEY idx_gamo_objective (gamo_objective_id)
);
```

#### Tabel: gamo_scores
```sql
CREATE TABLE gamo_scores (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    gamo_objective_id BIGINT NOT NULL,
    current_maturity_level DECIMAL(3,2) DEFAULT 0,
    target_maturity_level DECIMAL(3,2) DEFAULT 3,
    capability_score DECIMAL(5,2),
    capability_level DECIMAL(3,2),
    percentage_complete INT DEFAULT 0,
    status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (gamo_objective_id) REFERENCES gamo_objectives(id),
    UNIQUE KEY unique_score (assessment_id, gamo_objective_id),
    KEY idx_maturity (current_maturity_level)
);
```

#### Tabel: audit_logs (Enhanced untuk Security)
```sql
CREATE TABLE audit_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50),
    entity_type VARCHAR(100),
    entity_id BIGINT,
    status_code INT,
    old_values LONGTEXT,
    new_values LONGTEXT,
    sensitive_data_accessed BOOLEAN DEFAULT FALSE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(255),
    is_encrypted BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_user (user_id),
    KEY idx_action (action),
    KEY idx_created (created_at),
    KEY idx_sensitive (sensitive_data_accessed)
);
```

#### Tabel: login_attempts
```sql
CREATE TABLE login_attempts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    success BOOLEAN DEFAULT FALSE,
    failure_reason VARCHAR(255),
    user_agent TEXT,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_email_ip (email, ip_address),
    KEY idx_attempted (attempted_at)
);
```

#### Tabel: user_tokens (Enhanced)
```sql
CREATE TABLE user_tokens (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    token_type ENUM('access', 'refresh', 'api') DEFAULT 'access',
    token_hash VARCHAR(255) NOT NULL,
    device_info JSON,
    ip_address VARCHAR(45),
    expires_at TIMESTAMP,
    revoked_at TIMESTAMP NULL,
    is_encrypted BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_user (user_id),
    KEY idx_expires (expires_at),
    UNIQUE KEY unique_token (token_hash)
);
```

#### Tabel: encryption_keys_log
```sql
CREATE TABLE encryption_keys_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    key_version INT,
    key_algorithm VARCHAR(100),
    key_size INT,
    rotation_date TIMESTAMP,
    status ENUM('active', 'inactive', 'compromised') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 7. Role & Permission User

### 5.1 Tabel Role

| Role | Deskripsi |
|------|-----------|
| Super Admin | Akses penuh ke semua fitur sistem |
| Admin | Manajemen user, assessment, konfigurasi sistem |
| Manager | Kelola assessment, lihat laporan, manage assessor |
| Assessor | Conduct assessment, jawab pertanyaan |
| Viewer | Hanya melihat laporan dan dashboard (read-only) |

### 5.2 Permission Matrix

#### Super Admin
```
✓ user.create, user.read, user.update, user.delete
✓ role.manage, permission.manage
✓ assessment.create, assessment.read, assessment.update, assessment.delete
✓ assessment.review, assessment.approve
✓ question.manage
✓ report.generate, report.export
✓ audit.view
✓ system.configure
✓ company.manage
```

#### Admin
```
✓ user.create, user.read, user.update, user.delete
✓ assessment.create, assessment.read, assessment.update, assessment.delete
✓ assessment.review
✗ assessment.approve
✓ question.manage
✓ report.generate, report.export
✓ audit.view
✓ system.configure
✓ company.manage
```

#### Manager
```
✓ user.read
✗ user.create, user.update, user.delete
✓ assessment.create, assessment.read, assessment.update
✗ assessment.delete, assessment.review, assessment.approve
✓ assessment.assign_assessor
✓ question.read
✗ question.create, question.update, question.delete
✓ report.generate, report.export
✓ audit.view (limited)
```

#### Assessor
```
✓ assessment.read
✗ assessment.create, assessment.update, assessment.delete, assessment.review
✓ answer.create, answer.read, answer.update
✓ evidence.upload
✓ report.generate (own assessment only)
✗ audit.view
```

#### Viewer
```
✓ assessment.read
✓ report.view
✓ dashboard.view
✗ All other actions
```

### 7.1 Tabel Role

| Role | Deskripsi | Authorization Level |
|------|-----------|-------------------|
| Super Admin | Akses penuh ke semua fitur sistem & konfigurasi | Level 5 |
| Admin | Manajemen user, assessment, konfigurasi sistem | Level 4 |
| Manager | Kelola assessment, laporan, assessor assignment | Level 3 |
| Assessor | Conduct assessment, jawab pertanyaan, evidence upload | Level 2 |
| Viewer | Hanya melihat laporan dan dashboard (read-only) | Level 1 |

### 7.2 Detailed Permission Matrix

#### Super Admin - Full Access
```
✓ user.create, user.read, user.update, user.delete, user.reset_password
✓ role.manage, permission.manage
✓ assessment.create, assessment.read, assessment.update, assessment.delete
✓ assessment.review, assessment.approve, assessment.archive
✓ design_factor.manage
✓ gamo_objective.manage
✓ question.create, question.read, question.update, question.delete, question.bulk_import
✓ answer.read, answer.edit, answer.delete
✓ report.generate, report.export, report.custom
✓ audit.view, audit.export
✓ encryption.manage_keys
✓ system.configure, system.backup, system.restore
✓ company.manage
✓ security.configure
✓ 2fa.bypass (emergency only)
```

#### Admin
```
✓ user.create, user.read, user.update, user.delete
✗ user.delete (Super Admin users)
✓ assessment.create, assessment.read, assessment.update, assessment.delete
✓ assessment.review
✗ assessment.approve
✓ assessment.archive
✓ design_factor.manage
✓ gamo_objective.read
✓ question.manage
✓ answer.read
✓ report.generate, report.export
✓ audit.view
✓ system.configure
✓ company.manage
✗ encryption.manage_keys
✗ security.configure
```

#### Manager
```
✓ user.read, user.update (limited)
✗ user.create, user.delete
✓ assessment.create, assessment.read, assessment.update
✗ assessment.delete, assessment.review, assessment.approve
✓ assessment.assign_assessor
✓ design_factor.read
✓ gamo_objective.read
✓ question.read
✗ question.create, question.delete
✓ answer.read
✓ report.generate, report.export
✓ audit.view (limited to own assessments)
✗ system.configure
```

#### Assessor
```
✓ assessment.read (assigned only)
✗ assessment.create, assessment.update, assessment.delete, assessment.review
✓ answer.create, answer.read, answer.update (own answers)
✓ evidence.upload, evidence.delete (own uploads)
✓ report.view (own assessment only)
✗ audit.view
✗ system.configure
```

#### Viewer
```
✓ assessment.read
✓ report.view
✓ dashboard.view
✗ All modification actions
✗ system.configure
✓ answer.read (view only)
```

---

## 8. Matriks User Access

### 6.1 Akses Module per Role

| Module | Super Admin | Admin | Manager | Assessor | Viewer |
|--------|:-----------:|:-----:|:-------:|:--------:|:------:|
| Dashboard | ✓ | ✓ | ✓ | ✓ | ✓ |
| User Management | ✓ | ✓ | ✗ | ✗ | ✗ |
| Assessment | ✓ | ✓ | ✓ | ✓ | ✓ |
| Assessment Answer | ✓ | ✓ | ✓ | ✓ | ✗ |
| Question Bank | ✓ | ✓ | ✗ | ✗ | ✗ |
| Report | ✓ | ✓ | ✓ | ✓ | ✓ |
| Recommendation | ✓ | ✓ | ✓ | ✗ | ✓ |
| Audit Log | ✓ | ✓ | ✗ | ✗ | ✗ |
| System Config | ✓ | ✓ | ✗ | ✗ | ✗ |
| Company Settings | ✓ | ✓ | ✗ | ✗ | ✗ |

### 6.2 Akses Data per Role

```
Super Admin   : Semua company, semua assessment
Admin         : Semua company, semua assessment
Manager       : Assign company, lihat assessment own company
Assessor      : Hanya assessment yang di-assign
Viewer        : Read-only semua assessment assigned
```

### 8.1 Akses Module per Role

| Module | Super Admin | Admin | Manager | Assessor | Viewer |
|--------|:-----------:|:-----:|:-------:|:--------:|:------:|
| Dashboard | ✓ Full | ✓ Full | ✓ Limited | ✓ Limited | ✓ View |
| User Management | ✓ Full | ✓ Full | ✗ | ✗ | ✗ |
| Role & Permission | ✓ Full | ✗ | ✗ | ✗ | ✗ |
| Assessment | ✓ Full | ✓ Full | ✓ Limited | ✓ Assigned | ✓ View |
| Design Factors | ✓ Full | ✓ Full | ✓ View | ✗ | ✗ |
| GAMO Objectives | ✓ Full | ✓ Full | ✓ View | ✓ View | ✓ View |
| Questions | ✓ Full | ✓ Full | ✓ View | ✗ | ✗ |
| Assessment Answer | ✓ Full | ✓ Full | ✓ View | ✓ Edit Own | ✓ View |
| Evidence Upload | ✓ Full | ✓ Full | ✓ View | ✓ Own | ✓ View |
| Report | ✓ Full | ✓ Full | ✓ Full | ✓ Own | ✓ View |
| Recommendation | ✓ Full | ✓ Full | ✓ Full | ✓ View | ✓ View |
| Audit Log | ✓ Full | ✓ Full | ✓ Limited | ✗ | ✗ |
| System Config | ✓ Full | ✓ Full | ✗ | ✗ | ✗ |
| Company Settings | ✓ Full | ✓ Full | ✗ | ✗ | ✗ |
| Security & 2FA | ✓ Full | ✓ Manage | ✗ | ✓ Own | ✗ |

### 8.2 Akses Data Level

```
┌─────────────────────────────────────────────┐
│         DATA ACCESS HIERARCHY               │
└─────────────────────────────────────────────┘

Super Admin
├── All Companies
│   ├── All Assessments
│   ├── All Users
│   └── All Audit Logs
└── System-wide Configuration

Admin
├── All Companies
│   ├── All Assessments
│   ├── All Users (view/manage)
│   └── Company-wide Audit Logs
└── System Configuration (partial)

Manager
├── Assigned Company
│   ├── Own Assessment + Team Assessments
│   ├── Team Users (view)
│   └── Company-level Audit Logs (limited)
└── Company Configuration (limited)

Assessor
├── Assigned Company
│   ├── Assigned Assessments Only
│   ├── Own Profile (edit)
│   └── Own Activity Log
└── No System Configuration

Viewer
├── Assigned Company (Read-only)
│   ├── Published Assessments Only
│   ├── Own Profile (view)
│   └── No Activity Log Access
└── No System Configuration
```

### 8.3 Data Encryption & Access Control

```php
// Sensitive Data Protection
- Assessment Details      : Encrypted (AES-256-CBC)
- Answer Content          : Encrypted
- Evidence Files          : Encrypted
- Audit Logs             : Encrypted (sensitive actions)
- User Credentials       : Hashed (bcrypt)
- API Tokens             : Hashed with salt
- Email Addresses        : Encrypted (users table)

// Field-level Encryption
protected $encrypted = [
    'assessment_details',
    'answer_text',
    'notes',
    'sensitive_data'
];

// File-level Encryption
Evidence files stored as:
/storage/app/encrypted/assessments/
- Filename: encrypted (hash + timestamp)
- Content: encrypted with assessment key
- Access: tracked in audit_logs
```

---

## 9. Daftar Modul

### 7.1 Module Structure

```
1. Authentication & Authorization
   ├── Login/Logout
   ├── Password Reset
   ├── Two-Factor Authentication (optional)
   └── Session Management

2. Dashboard
   ├── Home Dashboard
   ├── Assessment Dashboard
   ├── Performance Dashboard
   └── Executive Dashboard

3. User Management
   ├── User CRUD
   ├── Role Assignment
   ├── Department Management
   ├── User Activity Log
   └── Bulk User Import

4. Assessment Management
   ├── Create Assessment
   ├── List Assessment
   ├── Edit Assessment
   ├── Assessment Status Tracking
   ├── Assign Assessor
   ├── Assessment Scheduling
   └── Archive Assessment

5. Assessment Execution
   ├── Answer Questions
   ├── Upload Evidence
   ├── Save Progress (Draft)
   ├── Submit Assessment
   ├── View Assessment Status
   └── Re-assessment

6. Question Management
   ├── Question Bank CRUD
   ├── Question Category Management
   ├── Bulk Import Questions
   ├── Question Validation
   └── Question Versioning

7. Scoring & Maturity Calculation
   ├── Calculate Maturity Level
   ├── Score Calculation Engine
   ├── Level 0-5 Assessment
   ├── Capability Score Calculation
   └── Auto-generate Recommendations

8. Reporting & Analytics
   ├── Assessment Report
   ├── Maturity Report
   ├── Comparative Analysis
   ├── Trend Analysis
   ├── Export to PDF
   ├── Export to Excel
   └── Custom Report Builder

9. Recommendations & Action Plan
   ├── Generate Recommendations
   ├── Recommendation Tracking
   ├── Action Plan Management
   ├── Progress Tracking
   └── Follow-up Assessment

10. Company Management
    ├── Company CRUD
    ├── Company Settings
    ├── Department Management
    └── Company Hierarchy

11. System Configuration
    ├── COBIT Framework Configuration
    ├── Maturity Level Definition
    ├── Email Configuration
    ├── Application Settings
    └── Backup & Restore

12. Audit & Compliance
    ├── Audit Log Viewer
    ├── Activity Tracking
    ├── Change Log
    ├── User Activity Report
    └── Compliance Report
```

### 9.1 Module Structure - COBIT 2019 Edition

```
1. Authentication & Authorization
   ├── Login/Logout
   ├── Password Reset
   ├── Two-Factor Authentication (TOTP)
   ├── Session Management
   ├── Token Management
   └── Account Security Settings

2. Dashboard
   ├── Executive Dashboard (KPI Overview)
   ├── Assessment Dashboard (Progress Tracking)
   ├── Performance Dashboard (Maturity Trends)
   ├── User Activity Dashboard
   └── Compliance Status Dashboard

3. User Management
   ├── User CRUD
   ├── Role & Permission Assignment
   ├── Department Management
   ├── User Activity Log
   ├── Bulk User Import
   ├── Password Reset & Security
   └── 2FA Setup Management

4. Company & Organization Management
   ├── Company CRUD
   ├── Department Hierarchy
   ├── Company Settings
   ├── Contact Information
   └── Company Profile

5. Design Factor Configuration
   ├── View 10 Design Factors
   ├── Assessment Design Factor Selection
   ├── Design Factor Guidance
   ├── Scope Definition
   └── Tailoring Documentation

6. GAMO Objectives Management
   ├── View 23 GAMO Objectives (EDM/APO/BAI/DSS/MEA)
   ├── Assessment GAMO Selection
   ├── Objective Description & Guidance
   ├── Related Processes Mapping
   └── Objective-wise Requirements

7. Assessment Management
   ├── Create Assessment (with Design Factors)
   ├── List & Filter Assessments
   ├── Edit Assessment Details
   ├── Assessment Status Tracking
   ├── Assign Assessor & Team
   ├── Assessment Scheduling
   ├── Archive & Unarchive Assessment
   └── Assessment History & Versioning

8. Assessment Execution
   ├── Answer Questions (by GAMO Objective)
   ├── Progress Tracking
   ├── Save as Draft
   ├── Submit Assessment
   ├── Upload Evidence Files
   ├── Add Assessment Comments
   ├── View Assessment Status
   ├── Re-assessment Capability
   └── Incomplete Items Tracking

9. Question Management
   ├── Question CRUD (GAMO-based)
   ├── Question Category Management
   ├── Bulk Import Questions
   ├── Question Versioning
   ├── Guidance Document Management
   ├── Evidence Requirement Definition
   ├── Question Mapping to GAMO Objectives
   └── Question Difficulty Levels

10. Evidence Management
    ├── File Upload & Storage
    ├── File Encryption
    ├── File Version Control
    ├── File Preview (with security)
    ├── Evidence Linking to Answers
    ├── Evidence Deletion & Archive
    ├── File Access Audit Log
    └── Malware Scanning

11. Scoring & Maturity Calculation
    ├── Capability Score Calculation
    ├── Maturity Level Assessment (0-5)
    ├── Process-wise Scoring
    ├── Domain-wise Aggregation
    ├── Gap Analysis
    ├── Trend Analysis
    ├── Benchmark Comparison
    └── Auto-generate Recommendations

12. Reporting & Analytics
    ├── Assessment Summary Report
    ├── Detailed Assessment Report
    ├── Executive Summary
    ├── Maturity Level Report
    ├── Gap Analysis Report
    ├── Trend Analysis Report
    ├── Comparative Analysis
    ├── Export to PDF
    ├── Export to Excel
    ├── Custom Report Builder
    └── Scheduled Report Generation

13. Recommendations & Action Plan
    ├── Generate Recommendations
    ├── Manual Recommendation Entry
    ├── Priority Categorization
    ├── Effort Estimation
    ├── Owner Assignment
    ├── Due Date Tracking
    ├── Status Tracking
    ├── Progress Monitoring
    ├── Document Attachment
    ├── Timeline & Roadmap
    └── Follow-up Assessment Scheduling

14. Notification System
    ├── Email Notifications
    ├── In-App Notifications
    ├── SMS Notifications (optional)
    ├── Notification Preferences
    ├── Bulk Notification
    └── Notification History

15. System Configuration
    ├── COBIT 2019 Framework Setup
    ├── Maturity Level Definition
    ├── Email Configuration
    ├── Application Settings
    ├── Backup & Restore
    ├── Database Maintenance
    ├── System Logs Viewer
    └── Version Control

16. Security & Encryption Management
    ├── Encryption Key Management
    ├── Key Rotation Policy
    ├── SSL Certificate Management
    ├── API Key Management
    ├── Security Headers Configuration
    ├── CORS Configuration
    ├── Rate Limiting Configuration
    └── Security Audit Trail

17. Audit & Compliance Logging
    ├── Audit Log Viewer
    ├── Activity Tracking
    ├── Change Log
    ├── User Activity Report
    ├── Sensitive Data Access Log
    ├── Login Attempt Log
    ├── Token Management Log
    ├── Compliance Report
    ├── Export Audit Log
    └── Audit Log Retention

18. User Profile & Settings
    ├── View/Edit Profile
    ├── Change Password
    ├── Setup 2FA
    ├── Manage Devices/Sessions
    ├── API Token Management
    ├── Activity History
    ├── Security Settings
    └── Notification Preferences
```

---

## 10. Daftar Fitur

### 8.1 Feature List by Priority

#### Priority 1 (MVP - Must Have)
```
✓ User Authentication & Authorization
✓ Assessment CRUD
✓ Question Answer System
✓ Maturity Scoring (Level 0-5)
✓ Basic Dashboard
✓ Report Generation (PDF/Excel)
✓ User Management (Admin)
✓ Audit Log
```

#### Priority 2 (Should Have)
```
✓ Evidence Upload System
✓ Recommendation Engine
✓ Action Plan Tracking
✓ Email Notifications
✓ Assessment Scheduling
✓ Progress Tracking
✓ Multiple Assessment Type Support
✓ Comparative Analysis
```

#### Priority 3 (Nice to Have)
```
✓ Two-Factor Authentication
✓ Advanced Analytics
✓ API Documentation
✓ Mobile Responsive Optimization
✓ Bulk Operations
✓ Assessment Templates
✓ Custom Report Builder
✓ Integration with External Systems
```

### 8.2 Detail Fitur

#### Authentication & Authorization
- Login dengan email/password
- Password reset via email
- Session timeout
- Logout functionality
- Role-based access control (RBAC)
- Permission-based access

#### Assessment Management
- Create assessment dengan multiple types (Initial, Periodic, Specific)
- Drag-drop question assignment
- Auto-save progress
- Assessment workflow (Draft → In Progress → Completed → Reviewed → Approved)
- Reassessment capability
- Assessment versioning/history

#### Question Management
- CRUD question
- Import bulk questions via Excel
- Question categorization
- Question tagging
- Question difficulty level
- Evidence attachment guidance
- Related document linking

#### Scoring System
- Automatic maturity level calculation
- Capability Score based on CMM/CMMI model
- Evidence-based scoring
- Maturity Level 0-5 scale:
  * Level 0: Non-existent
  * Level 1: Initial/Ad Hoc
  * Level 2: Repeatable
  * Level 3: Defined
  * Level 4: Managed
  * Level 5: Optimized

#### Report Generation
- Assessment Summary Report
- Detailed Assessment Report
- Executive Summary
- Process Maturity Report
- Gap Analysis Report
- Trend Analysis Report
- Recommendation Report
- Export PDF with branding
- Export Excel with formulas
- Print-friendly format

#### Dashboard Features
- Overview metrics
- Assessment progress visualization
- Maturity level comparison charts
- Process-wise scoring
- Domain-wise summary
- Recommendation tracking
- Recent activities

#### Evidence Management
- File upload (PDF, DOC, JPG, PNG, etc.)
- File versioning
- File preview
- Evidence linking to answers
- Evidence validation

#### Recommendations
- Auto-generate based on gap analysis
- Manual recommendations
- Priority categorization (Low, Medium, High, Critical)
- Effort estimation
- Owner assignment
- Due date tracking
- Status tracking (Open, In Progress, Completed, Closed)
- Attachment support

#### Notification System
- Email notifications for assessment updates
- Status change notifications
- Reminder for incomplete assessments
- Deadline notifications
- In-app notifications
- SMS notifications (optional)

#### Reporting & Export
- PDF export with custom template
- Excel export with formatting
- Multi-language support
- Custom date range
- Filter by company/department
- Bulk report generation
- Scheduled report generation

#### Audit Log
- Track all user activities
- Log data changes
- Record user login/logout
- IP address tracking
- Timestamp all actions
- Searchable/filterable log
- Export audit log

### 10.1 Feature List - COBIT 2019

#### Priority 1 (MVP - Must Have)
```
✓ User Authentication (Secure Password + Encrypted Tokens)
✓ RBAC (Role-Based Access Control)
✓ CSRF Protection
✓ Input Validation & Sanitization
✓ Assessment CRUD (with Design Factors)
✓ GAMO Objectives Selection
✓ Question Answer System (GAMO-based)
✓ Evidence Upload & Encrypted Storage
✓ Maturity Scoring (Level 0-5 with Capability Model)
✓ Basic Dashboard
✓ Report Generation (PDF/Excel)
✓ User Management (Admin)
✓ Comprehensive Audit Log
✓ Rate Limiting & Brute Force Protection
✓ HTTPS/TLS Enforcement
```

#### Priority 2 (Should Have)
```
✓ Two-Factor Authentication (TOTP)
✓ Advanced Recommendation Engine
✓ Action Plan Tracking
✓ Email Notifications with Security
✓ Assessment Scheduling
✓ Progress Tracking & Dashboard
✓ Multiple Assessment Type Support
✓ Comparative Analysis
✓ Design Factor Guidance System
✓ GAMO Objective Documentation
✓ Evidence File Versioning
✓ Database Encryption (Field-level)
✓ API Key Management
✓ Login Attempt Tracking
✓ Session Management with Timeout
```

#### Priority 3 (Nice to Have)
```
✓ Advanced Analytics & Reporting
✓ API Documentation (Swagger)
✓ Mobile Responsive Optimization
✓ Bulk Operations (Import/Export)
✓ Assessment Templates (Design Factor based)
✓ Custom Report Builder
✓ Integration with External Systems
✓ Key Rotation Automation
✓ Advanced Encryption Key Management
✓ Real-time Collaboration
✓ Comments & Discussion Thread
✓ Social Features (Tagging, Mentions)
✓ Workflow Customization
✓ Custom Field Definition
```

### 10.2 Security Features Detail

#### Authentication Security
- **Password Policy**: Min 12 char, uppercase, lowercase, number, special char
- **Password Hashing**: bcrypt dengan cost 12
- **Password History**: Tidak boleh sama 5 password terakhir
- **Account Lockout**: 5 failed attempts = lockout 15 menit
- **Password Expiry**: 90 hari (admin), 180 hari (user)
- **Session Timeout**: 30 menit inactivity
- **Token Expiry**: 24 jam (customizable)

#### Data Protection
- **Field Encryption**: AES-256-CBC untuk sensitive fields
- **File Encryption**: Evidence files encrypted at rest
- **Database Encryption**: Optional transparent encryption
- **Backup Encryption**: Encrypted database backups
- **HTTPS/TLS 1.2+**: All communications
- **Certificate Pinning**: For critical connections

#### Access Control
- **RBAC**: 5 role levels dengan permission matrix
- **CSRF Protection**: Token-based pada setiap form
- **XSS Prevention**: Input validation & output encoding
- **SQL Injection Prevention**: Prepared statements
- **Rate Limiting**: API throttling & login attempt limiting
- **2FA**: TOTP (Time-based One-Time Password)

#### Audit & Monitoring
- **Audit Trail**: Semua user activities dicatat
- **Sensitive Data Logging**: Flag untuk access sensitive data
- **Login Tracking**: Timestamp, IP, device info
- **Change Tracking**: Before/after values untuk data changes
- **File Access Log**: Siapa akses file, kapan, dari mana
- **Encryption Key Log**: Key rotation history
- **API Log**: Request/response logging

#### Security Headers
- **X-Content-Type-Options**: nosniff
- **X-Frame-Options**: DENY
- **X-XSS-Protection**: 1; mode=block
- **Strict-Transport-Security**: HSTS enabled
- **Content-Security-Policy**: Strict CSP
- **Referrer-Policy**: strict-origin-when-cross-origin
- **Permissions-Policy**: Restrict browser features

### 10.3 Feature Breakdown

#### Assessment Features
- COBIT 2019 framework aligned
- Design Factor based tailoring
- GAMO Objective selection & customization
- Multi-status workflow (Draft → Approved)
- Team assessment capability
- Scoring automation
- Maturity level calculation
- Gap analysis
- Recommendation generation

#### Evidence Management
- Secure file upload
- File encryption at storage
- File type validation
- Malware scanning
- Version control
- Access tracking
- Secure download/preview

#### Reporting Features
- Executive summary
- Detailed assessment report
- Process maturity report
- Gap analysis report
- Trend analysis
- Benchmark comparison
- PDF export with branding
- Excel export with formulas
- Custom report builder
- Scheduled reporting

#### Notification System
- Email notifications
- In-app notifications
- SMS (optional)
- Webhook support
- Notification templates
- Bulk notifications
- Notification preferences

#### Dashboard Features
- Real-time metrics
- Assessment progress visualization
- Maturity level comparison
- Process-wise scoring
- Domain-wise summary
- Recommendation tracking
- Recent activities
- User-specific views

---

## 11. Struktur Folder Project

### 9.1 Laravel Project Structure

```
assessme/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── AssessmentController.php
│   │   │   ├── QuestionController.php
│   │   │   ├── AnswerController.php
│   │   │   ├── ScoreController.php
│   │   │   ├── ReportController.php
│   │   │   ├── RecommendationController.php
│   │   │   ├── CompanyController.php
│   │   │   └── AuditLogController.php
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── CheckPermission.php
│   │   │   ├── AuditLogging.php
│   │   │   └── VerifyRole.php
│   │   └── Requests/
│   │       ├── AssessmentRequest.php
│   │       ├── QuestionRequest.php
│   │       ├── AnswerRequest.php
│   │       └── UserRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Assessment.php
│   │   ├── Company.php
│   │   ├── Question.php
│   │   ├── Answer.php
│   │   ├── CobitDomain.php
│   │   ├── CobitProcess.php
│   │   ├── AssessmentScore.php
│   │   ├── Recommendation.php
│   │   ├── AuditLog.php
│   │   └── Evidence.php
│   ├── Services/
│   │   ├── AssessmentService.php
│   │   ├── ScoringService.php
│   │   ├── ReportService.php
│   │   ├── RecommendationService.php
│   │   ├── EmailService.php
│   │   └── ExportService.php
│   ├── Repositories/
│   │   ├── AssessmentRepository.php
│   │   ├── QuestionRepository.php
│   │   ├── UserRepository.php
│   │   └── ReportRepository.php
│   └── Traits/
│       ├── AuditTrait.php
│       └── PermissionTrait.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── auth.blade.php
│   │   │   └── navbar.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   └── forgot-password.blade.php
│   │   ├── dashboard/
│   │   │   ├── index.blade.php
│   │   │   ├── admin-dashboard.blade.php
│   │   │   ├── assessor-dashboard.blade.php
│   │   │   └── executive-dashboard.blade.php
│   │   ├── users/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── assessments/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── show.blade.php
│   │   │   ├── edit.blade.php
│   │   │   ├── execute.blade.php
│   │   │   ├── answers.blade.php
│   │   │   └── progress.blade.php
│   │   ├── questions/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── import.blade.php
│   │   ├── reports/
│   │   │   ├── index.blade.php
│   │   │   ├── detail.blade.php
│   │   │   ├── pdf-template.blade.php
│   │   │   └── comparison.blade.php
│   │   ├── recommendations/
│   │   │   ├── index.blade.php
│   │   │   └── tracking.blade.php
│   │   └── audit-logs/
│   │       └── index.blade.php
│   ├── css/
│   │   ├── app.css
│   │   ├── custom.css
│   │   └── responsive.css
│   └── js/
│       ├── app.js
│       ├── assessment.js
│       ├── report.js
│       ├── chart.js
│       └── validation.js
├── routes/
│   ├── web.php
│   ├── api.php
│   └── auth.php
├── database/
│   ├── migrations/
│   │   ├── 2024_create_users_table.php
│   │   ├── 2024_create_roles_table.php
│   │   ├── 2024_create_permissions_table.php
│   │   ├── 2024_create_assessments_table.php
│   │   ├── 2024_create_questions_table.php
│   │   ├── 2024_create_answers_table.php
│   │   └── ... (lebih banyak migrations)
│   ├── seeders/
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   ├── CobitDomainSeeder.php
│   │   ├── CobitProcessSeeder.php
│   │   ├── QuestionSeeder.php
│   │   └── UserSeeder.php
│   └── factories/
│       ├── UserFactory.php
│       ├── AssessmentFactory.php
│       └── AnswerFactory.php
├── storage/
│   ├── app/
│   │   └── evidence/ (upload evidence)
│   ├── logs/
│   └── framework/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── auth.php
│   ├── assessment.php
│   └── cobit.php
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── vendor/
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env.example
├── composer.json
├── artisan
└── README.md
```

### 11.1 Enhanced Laravel Project Structure

```
assessme/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   ├── TwoFactorController.php
│   │   │   │   └── PasswordResetController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── RolePermissionController.php
│   │   │   ├── AssessmentController.php
│   │   │   ├── DesignFactorController.php
│   │   │   ├── GamoObjectiveController.php
│   │   │   ├── QuestionController.php
│   │   │   ├── AnswerController.php
│   │   │   ├── EvidenceController.php
│   │   │   ├── ScoreController.php
│   │   │   ├── ReportController.php
│   │   │   ├── RecommendationController.php
│   │   │   ├── CompanyController.php
│   │   │   ├── SecurityController.php
│   │   │   ├── AuditLogController.php
│   │   │   ├── SettingsController.php
│   │   │   └── ApiController.php
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── CheckPermission.php
│   │   │   ├── VerifyRole.php
│   │   │   ├── AuditLogging.php
│   │   │   ├── EncryptionMiddleware.php
│   │   │   ├── RateLimitMiddleware.php
│   │   │   ├── SecurityHeaders.php
│   │   │   ├── CsrfProtection.php
│   │   │   ├── SessionTimeout.php
│   │   │   └── TrackLoginAttempt.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       │   ├── LoginRequest.php
│   │       │   ├── PasswordResetRequest.php
│   │       │   └── TwoFactorRequest.php
│   │       ├── AssessmentRequest.php
│   │       ├── QuestionRequest.php
│   │       ├── AnswerRequest.php
│   │       ├── UserRequest.php
│   │       ├── EvidenceUploadRequest.php
│   │       └── ReportRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Assessment.php
│   │   ├── DesignFactor.php
│   │   ├── AssessmentDesignFactor.php
│   │   ├── GamoObjective.php
│   │   ├── AssessmentGamoSelection.php
│   │   ├── Company.php
│   │   ├── Question.php
│   │   ├── Answer.php
│   │   ├── Evidence.php
│   │   ├── AssessmentScore.php
│   │   ├── GamoScore.php
│   │   ├── Recommendation.php
│   │   ├── AuditLog.php
│   │   ├── LoginAttempt.php
│   │   ├── UserToken.php
│   │   ├── TwoFactorAuth.php
│   │   └── EncryptionKeyLog.php
│   ├── Services/
│   │   ├── AssessmentService.php
│   │   ├── ScoringService.php
│   │   ├── ReportService.php
│   │   ├── RecommendationService.php
│   │   ├── EmailService.php
│   │   ├── ExportService.php
│   │   ├── EncryptionService.php
│   │   ├── SecurityService.php
│   │   ├── TwoFactorService.php
│   │   ├── EvidenceService.php
│   │   ├── AuditLogService.php
│   │   ├── TokenService.php
│   │   └── NotificationService.php
│   ├── Repositories/
│   │   ├── AssessmentRepository.php
│   │   ├── QuestionRepository.php
│   │   ├── UserRepository.php
│   │   ├── GamoRepository.php
│   │   ├── ReportRepository.php
│   │   └── AuditRepository.php
│   ├── Traits/
│   │   ├── AuditTrait.php
│   │   ├── PermissionTrait.php
│   │   ├── EncryptableTrait.php
│   │   └── HasToken.php
│   ├── Events/
│   │   ├── UserLoggedIn.php
│   │   ├── AssessmentCreated.php
│   │   ├── AnswerSubmitted.php
│   │   ├── SensitiveDataAccessed.php
│   │   └── SecurityAlertTriggered.php
│   ├── Listeners/
│   │   ├── LogUserLogin.php
│   │   ├── SendAssessmentNotification.php
│   │   ├── AuditSensitiveAccess.php
│   │   └── TriggerSecurityAlert.php
│   ├── Jobs/
│   │   ├── GenerateReportJob.php
│   │   ├── SendNotificationJob.php
│   │   ├── RotateEncryptionKeysJob.php
│   │   ├── CleanupLoginAttemptsJob.php
│   │   └── ExportDataJob.php
│   ├── Mail/
│   │   ├── AssessmentInvitation.php
│   │   ├── PasswordResetMail.php
│   │   ├── TwoFactorCodeMail.php
│   │   ├── ReportReadyMail.php
│   │   └── SecurityAlertMail.php
│   ├── Exceptions/
│   │   ├── UnauthorizedException.php
│   │   ├── EncryptionException.php
│   │   ├── ValidationException.php
│   │   ├── MalwareDetectedException.php
│   │   └── SecurityException.php
│   └── Notifications/
│       ├── AssessmentNotification.php
│       └── SecurityAlertNotification.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── auth.blade.php
│   │   │   ├── navbar.blade.php
│   │   │   ├── sidebar.blade.php
│   │   │   └── footer.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   └── two-factor.blade.php
│   │   ├── dashboard/
│   │   │   ├── index.blade.php
│   │   │   ├── admin-dashboard.blade.php
│   │   │   ├── assessor-dashboard.blade.php
│   │   │   └── executive-dashboard.blade.php
│   │   ├── assessments/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── design-factors.blade.php
│   │   │   ├── gamo-selection.blade.php
│   │   │   ├── show.blade.php
│   │   │   ├── edit.blade.php
│   │   │   ├── execute.blade.php
│   │   │   ├── answers.blade.php
│   │   │   └── progress.blade.php
│   │   ├── questions/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── import.blade.php
│   │   ├── evidence/
│   │   │   ├── upload.blade.php
│   │   │   ├── list.blade.php
│   │   │   └── viewer.blade.php
│   │   ├── reports/
│   │   │   ├── index.blade.php
│   │   │   ├── detail.blade.php
│   │   │   ├── pdf-template.blade.php
│   │   │   └── comparison.blade.php
│   │   ├── recommendations/
│   │   │   ├── index.blade.php
│   │   │   └── tracking.blade.php
│   │   ├── security/
│   │   │   ├── two-factor-setup.blade.php
│   │   │   ├── password-change.blade.php
│   │   │   ├── sessions.blade.php
│   │   │   └── api-tokens.blade.php
│   │   └── audit-logs/
│   │       └── index.blade.php
│   ├── css/
│   │   ├── app.css
│   │   ├── custom.css
│   │   └── responsive.css
│   └── js/
│       ├── app.js
│       ├── assessment.js
│       ├── security.js
│       ├── encryption.js
│       ├── report.js
│       ├── chart.js
│       └── validation.js
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── auth.php
│   ├── assessment.php
│   └── admin.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_create_users_table.php
│   │   ├── 2024_02_create_roles_table.php
│   │   ├── 2024_03_create_permissions_table.php
│   │   ├── 2024_04_create_design_factors_table.php
│   │   ├── 2024_05_create_gamo_objectives_table.php
│   │   ├── 2024_06_create_assessments_table.php
│   │   ├── 2024_07_create_assessment_design_factors_table.php
│   │   ├── 2024_08_create_assessment_gamo_selections_table.php
│   │   ├── 2024_09_create_questions_table.php
│   │   ├── 2024_10_create_answers_table.php
│   │   ├── 2024_11_create_evidence_table.php
│   │   ├── 2024_12_create_gamo_scores_table.php
│   │   ├── 2024_13_create_recommendations_table.php
│   │   ├── 2024_14_create_audit_logs_table.php
│   │   ├── 2024_15_create_login_attempts_table.php
│   │   ├── 2024_16_create_user_tokens_table.php
│   │   ├── 2024_17_create_two_factor_auth_table.php
│   │   └── 2024_18_create_encryption_keys_log_table.php
│   ├── seeders/
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   ├── DesignFactorSeeder.php
│   │   ├── GamoObjectiveSeeder.php
│   │   ├── QuestionSeeder.php
│   │   ├── UserSeeder.php
│   │   └── CompanySeeder.php
│   └── factories/
│       ├── UserFactory.php
│       ├── AssessmentFactory.php
│       ├── QuestionFactory.php
│       ├── AnswerFactory.php
│       └── EvidenceFactory.php
├── storage/
│   ├── app/
│   │   ├── evidence/ (encrypted evidence storage)
│   │   ├── reports/ (generated reports)
│   │   └── backups/ (encrypted backups)
│   ├── logs/ (secure application logs)
│   └── framework/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── auth.php
│   ├── assessment.php
│   ├── cobit.php
│   ├── encryption.php
│   ├── security.php
│   └── audit.php
├── public/
│   ├── css/
│   │   └── custom.css (custom styling only)
│   ├── js/
│   │   ├── app.js (compiled from resources/js)
│   │   ├── dashboard.js
│   │   ├── assessments/
│   │   │   ├── create.js
│   │   │   ├── answer.js
│   │   │   └── validation.js
│   │   ├── reports/
│   │   │   ├── maturity.js
│   │   │   └── gap-analysis.js
│   │   └── components/
│   │       ├── charts.js
│   │       └── datatables.js
│   ├── images/
│   │   ├── logo.png
│   │   └── icons/
│   └── vendor/ (Tabler via CDN - not local)
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php (authenticated layout with Tabler)
│   │   │   ├── guest.blade.php (login/register layout)
│   │   │   └── partials/
│   │   │       ├── sidebar.blade.php
│   │   │       ├── navbar.blade.php
│   │   │       └── footer.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   └── forgot-password.blade.php
│   │   ├── dashboard/
│   │   │   └── index.blade.php
│   │   ├── assessments/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── show.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── answer.blade.php
│   │   ├── reports/
│   │   │   ├── index.blade.php
│   │   │   ├── maturity.blade.php
│   │   │   ├── gap-analysis.blade.php
│   │   │   └── summary.blade.php
│   │   ├── admin/
│   │   │   ├── users.blade.php
│   │   │   ├── roles.blade.php
│   │   │   ├── audit-logs.blade.php
│   │   │   └── settings.blade.php
│   │   └── components/ (Blade components)
│   ├── js/
│   │   ├── app.js
│   │   ├── dashboard.js
│   │   ├── assessments/
│   │   │   ├── create.js
│   │   │   ├── answer.js
│   │   │   └── validation.js
│   │   ├── reports/
│   │   │   ├── maturity.js
│   │   │   └── gap-analysis.js
│   │   └── components/
│   │       ├── charts.js
│   │       └── datatables.js
│   └── css/
│       └── custom.css
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── Assessment/
│   │   └── Security/
│   └── Unit/
│       ├── Services/
│       └── Models/
├── .env.example
├── .env.production
├── docker-compose.yml
├── Dockerfile
├── composer.json
├── package.json
├── artisan
└── README.md
```

---

## 11.5 Frontend Development Standards & Best Practices

### 11.5.1 ✅ MANDATORY: Use Tabler Components

**Tabler Template Integration**
- **Admin Template**: Tabler v1.0+ (Bootstrap 5.3+)
- **Documentation**: https://tabler.io/docs
- **Icons**: Tabler Icons (https://tabler.io/icons)

**MUST USE Tabler Pre-built Components:**
```
✅ Cards & Statistics    - https://tabler.io/docs/cards
✅ Tables & DataTables   - https://tabler.io/docs/tables
✅ Forms & Inputs        - https://tabler.io/docs/forms
✅ Buttons & Actions     - https://tabler.io/docs/buttons
✅ Modals & Dialogs      - https://tabler.io/docs/modals
✅ Alerts & Toasts       - https://tabler.io/docs/alerts
✅ Charts (Chart.js)     - https://tabler.io/docs/charts
✅ Navigation            - https://tabler.io/docs/navigation
✅ Badges & Labels       - https://tabler.io/docs/badges
✅ Progress Bars         - https://tabler.io/docs/progress
```

**❌ DO NOT Create Custom Components When Tabler Has It**
```
❌ Jangan buat custom HTML/CSS untuk komponen yang sudah ada di Tabler
❌ Jangan gunakan Bootstrap template lain selain Tabler
❌ Jangan modifikasi core Tabler CSS (gunakan custom.css untuk override)
```

**Example: Using Tabler Card Component**
```blade
<!-- ✅ CORRECT: Menggunakan Tabler Card Component -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Assessment Statistics</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="subheader">Total Assessments</div>
                <div class="h1 mb-3">{{ $total }}</div>
            </div>
        </div>
    </div>
</div>

<!-- ❌ WRONG: Custom HTML tanpa menggunakan Tabler -->
<div class="custom-card">
    <div class="custom-header">
        <h3>Assessment Statistics</h3>
    </div>
    <div class="custom-body">...</div>
</div>
```

---

### 11.5.2 ✅ MANDATORY: JavaScript File Separation

**CRITICAL RULE: NO INLINE JAVASCRIPT IN BLADE FILES**

```
❌ NEVER DO THIS:
<!-- ❌ WRONG: JavaScript embedded in Blade file -->
@extends('layouts.app')

@section('content')
    <div id="chart"></div>
    
    <script>
        // ❌ JavaScript langsung di Blade file
        const ctx = document.getElementById('chart');
        new Chart(ctx, { ... });
    </script>
@endsection
```

```
✅ ALWAYS DO THIS:
<!-- ✅ CORRECT: Blade file hanya HTML -->
@extends('layouts.app')

@section('content')
    <div id="chart"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
```

**JavaScript File Structure:**
```
resources/js/
├── app.js                     # Main application JS
├── dashboard.js               # Dashboard specific
├── assessments/
│   ├── create.js             # Assessment creation logic
│   ├── answer.js             # Answer submission logic
│   ├── validation.js         # Form validation
│   └── design-factors.js     # Design factors selection
├── reports/
│   ├── maturity.js           # Maturity report charts
│   ├── gap-analysis.js       # Gap analysis visualization
│   └── export.js             # Export to PDF/Excel
├── components/
│   ├── charts.js             # Reusable chart configurations
│   ├── datatables.js         # DataTable configurations
│   ├── modals.js             # Modal handlers
│   └── notifications.js      # Toast/Alert notifications
└── admin/
    ├── users.js              # User management
    ├── roles.js              # Role & permission management
    └── audit-logs.js         # Audit log viewer
```

**Blade Template Pattern:**
```blade
<!-- resources/views/assessments/create.blade.php -->
@extends('layouts.app')

@section('title', 'Create Assessment')

@section('content')
    <!-- HTML Content Only -->
    <form id="assessment-form" action="{{ route('assessments.store') }}" method="POST">
        @csrf
        <!-- Form fields -->
    </form>
@endsection

@push('scripts')
    <!-- Load external JavaScript ONLY -->
    <script src="{{ asset('js/assessments/create.js') }}"></script>
    <script src="{{ asset('js/assessments/validation.js') }}"></script>
@endpush
```

**JavaScript File Example:**
```javascript
// resources/js/assessments/create.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('assessment-form');
    
    // Form submission logic
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation
        if (!validateForm()) {
            return false;
        }
        
        // AJAX submission
        submitAssessment(new FormData(form));
    });
    
    function validateForm() {
        // Validation logic
    }
    
    function submitAssessment(formData) {
        // AJAX submission logic
    }
});
```

**Compile JavaScript with Vite:**
```bash
# Development (watch mode)
npm run dev

# Production build
npm run build
```

**vite.config.js Configuration:**
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/custom.css',
                'resources/js/app.js',
                'resources/js/dashboard.js',
                'resources/js/assessments/create.js',
                'resources/js/assessments/answer.js',
                'resources/js/reports/maturity.js',
            ],
            refresh: true,
        }),
    ],
});
```

---

### 11.5.3 CSS Customization Guidelines

**Use Tabler CSS Variables for Theming:**
```css
/* resources/css/custom.css */

/* Override Tabler theme colors */
:root {
    --tblr-primary: #0054a6;        /* Brand color */
    --tblr-secondary: #6c757d;
    --tblr-success: #2fb344;
    --tblr-danger: #d63939;
    --tblr-warning: #f59f00;
    --tblr-info: #4299e1;
}

/* Custom component styling (only if needed) */
.assessment-card {
    border-left: 4px solid var(--tblr-primary);
}

.maturity-level-0 { background-color: #e3e5e8; }
.maturity-level-1 { background-color: #ffc107; }
.maturity-level-2 { background-color: #17a2b8; }
.maturity-level-3 { background-color: #28a745; }
.maturity-level-4 { background-color: #007bff; }
.maturity-level-5 { background-color: #6f42c1; }
```

**Load Custom CSS:**
```blade
<!-- layouts/app.blade.php -->
<link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet"/>

<!-- Custom CSS (after Tabler) -->
@vite(['resources/css/custom.css'])
```

---

### 11.5.4 Chart.js Integration (Dashboard & Reports)

**Chart Configuration Pattern:**
```javascript
// resources/js/components/charts.js

export function createMaturityChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Level 0', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5'],
            datasets: [{
                label: 'Maturity Distribution',
                data: data,
                backgroundColor: '#0054a6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}

export function createGapAnalysisChart(canvasId, currentData, targetData) {
    const ctx = document.getElementById(canvasId);
    
    return new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['EDM', 'APO', 'BAI', 'DSS', 'MEA'],
            datasets: [
                {
                    label: 'Current Maturity',
                    data: currentData,
                    borderColor: '#0054a6',
                    backgroundColor: 'rgba(0, 84, 166, 0.2)'
                },
                {
                    label: 'Target Maturity',
                    data: targetData,
                    borderColor: '#2fb344',
                    backgroundColor: 'rgba(47, 179, 68, 0.2)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
```

**Usage in Blade:**
```blade
<!-- reports/maturity.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <canvas id="maturityChart" height="300"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/components/charts.js') }}" type="module"></script>
    <script type="module">
        import { createMaturityChart } from '{{ asset('js/components/charts.js') }}';
        
        const data = @json($maturityDistribution);
        createMaturityChart('maturityChart', data);
    </script>
@endpush
```

---

### 11.5.5 DataTables.js Configuration

**DataTable Configuration:**
```javascript
// resources/js/components/datatables.js

export function initAssessmentTable(tableId) {
    return $('#' + tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/assessments',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'company.name', name: 'company.name' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        }
    });
}
```

---

### 11.5.6 Form Validation Pattern

**jQuery Validation:**
```javascript
// resources/js/assessments/validation.js

export function validateAssessmentForm(formId) {
    $('#' + formId).validate({
        rules: {
            title: {
                required: true,
                minlength: 5,
                maxlength: 255
            },
            company_id: {
                required: true
            },
            description: {
                required: true,
                minlength: 20
            }
        },
        messages: {
            title: {
                required: 'Assessment title is required',
                minlength: 'Title must be at least 5 characters',
                maxlength: 'Title cannot exceed 255 characters'
            },
            company_id: {
                required: 'Please select a company'
            },
            description: {
                required: 'Description is required',
                minlength: 'Description must be at least 20 characters'
            }
        },
        errorClass: 'invalid-feedback',
        validClass: 'valid-feedback',
        highlight: function(element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        }
    });
}
```

---

### 11.5.7 Summary: Frontend Development Checklist

**Before Writing Code:**
- [ ] Check if Tabler has the component you need
- [ ] Plan JavaScript file structure (avoid inline scripts)
- [ ] Review Tabler documentation for best practices

**During Development:**
- [ ] Use Tabler components exclusively
- [ ] Keep Blade files clean (HTML only)
- [ ] Separate all JavaScript to resources/js/
- [ ] Use @push('scripts') for loading JS files
- [ ] Follow naming conventions (kebab-case for files)

**Testing:**
- [ ] Test on different screen sizes (responsive)
- [ ] Verify JavaScript loads correctly
- [ ] Check browser console for errors
- [ ] Validate HTML with Tabler standards

**Before Commit:**
- [ ] Run npm run build for production assets
- [ ] Check no inline scripts in Blade files
- [ ] Verify all Tabler components used correctly
- [ ] Test all interactive features

---

## 12. Setup & Instalasi

### 12.1 Prerequisites

```bash
- PHP 8.1 atau lebih tinggi
- Composer
- MySQL 8.0 atau PostgreSQL
- Node.js & NPM (untuk asset compilation)
- Git
```

### 12.2 Installation Steps

#### Step 1: Clone Repository
```bash
git clone <repository-url>
cd assessme
```

#### Step 2: Install Dependencies
```bash
composer install
npm install
```

#### Step 3: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

#### Step 4: Edit .env File
```env
APP_NAME="Assessment COBIT"
APP_ENV=local
APP_KEY=base64:... (auto-generated)
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assessme
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@assessme.com

JWT_SECRET=your-secret-key
```

#### Step 5: Database Setup
```bash
php artisan migrate
php artisan db:seed
```

#### Step 6: Storage Link
```bash
php artisan storage:link
```

#### Step 7: Build Assets
```bash
npm run dev
# atau untuk production
npm run build
```

#### Step 8: Start Development Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

### 10.3 Default Credentials

```
Email: admin@assessme.com
Password: password
Role: Super Admin
```

### 12.1 Prerequisites

```bash
- PHP 8.1 atau lebih tinggi (recommended 8.2)
- Composer 2.x
- MySQL 8.0 LTS atau PostgreSQL 13+
- Node.js 16+ & NPM 8+
- Git
- OpenSSL (untuk encryption)
- Redis (untuk cache & queue)
- cURL & wget
```

### 12.2 Installation Steps

#### Step 1: Clone Repository
```bash
git clone https://github.com/your-org/assessme.git
cd assessme
```

#### Step 2: Install Dependencies
```bash
composer install
npm install
```

#### Step 3: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

#### Step 4: Edit .env File
```env
# App Configuration
APP_NAME="Assessment COBIT 2019"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assessme
DB_USERNAME=root
DB_PASSWORD=secure_password

# Security Configuration
ENCRYPTION_METHOD=AES-256-CBC
ENCRYPTION_KEY=base64:xxxxxxxxxxxxx (auto-generated)

# Session Configuration
SESSION_DRIVER=cookie
SESSION_LIFETIME=30
SESSION_ENCRYPT=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=Lax

# Cookie Configuration
COOKIE_SECURE=false (true in production)
COOKIE_HTTP_ONLY=true
COOKIE_SAME_SITE=Lax

# Cache Configuration
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue Configuration
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@assessme.com
MAIL_FROM_NAME="${APP_NAME}"

# JWT Configuration
JWT_SECRET=your-secret-key
JWT_ALGORITHM=HS256
JWT_TTL=1440

# 2FA Configuration
TWOFACTOR_ENABLED=true
TWOFACTOR_ISSUER=AssessmentCOBIT

# Security Settings
SECURE_HEADERS=true
CORS_ENABLED=true
RATE_LIMIT_ENABLED=true
```

#### Step 5: Generate Encryption Key
```bash
# This is done by key:generate, but verify
php artisan key:generate

# Generate JWT secret if using JWT
php artisan jwt:secret
```

#### Step 6: Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE assessme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed
```

#### Step 7: Storage & Permissions
```bash
# Create storage link for public uploads
php artisan storage:link

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Step 8: Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

#### Step 9: Generate API Documentation
```bash
php artisan scribe:generate
```

#### Step 10: Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Step 11: Start Development Server
```bash
# Terminal 1: Artisan server
php artisan serve

# Terminal 2: Queue listener (optional)
php artisan queue:listen

# Terminal 3: Schedule runner (optional)
php artisan schedule:work
```

Akses aplikasi di: `http://localhost:8000`

### 12.3 Default Credentials

```
Email: admin@assessme.com
Password: password
Role: Super Admin

Email: manager@assessme.com
Password: password
Role: Manager

Email: assessor@assessme.com
Password: password
Role: Assessor
```

⚠️ **PENTING**: Ubah password default sebelum production!

### 12.4 Production Deployment Checklist

```
Environment
☐ Set APP_ENV=production
☐ Set APP_DEBUG=false
☐ Set LOG_LEVEL=error
☐ Generate strong APP_KEY
☐ Set all encryption keys

Security
☐ Enable HTTPS/SSL (TLS 1.2+)
☐ Configure CORS properly
☐ Set SECURE_HEADERS=true
☐ Configure CSRF token
☐ Enable rate limiting
☐ Setup firewall rules
☐ Enable 2FA for admins
☐ Configure secure cookies

Database
☐ Configure strong DB_PASSWORD
☐ Enable DB backups (daily)
☐ Enable query logging
☐ Monitor slow queries
☐ Setup database encryption

Mail
☐ Configure production mail service
☐ Setup email authentication
☐ Test email delivery

Cache & Queue
☐ Configure Redis in production
☐ Setup queue worker (supervisor)
☐ Configure cache TTL

Monitoring & Logging
☐ Setup centralized logging (ELK Stack)
☐ Configure error tracking (Sentry)
☐ Setup performance monitoring
☐ Configure alerts

Backup & Recovery
☐ Setup automated backups
☐ Test backup restoration
☐ Document recovery procedures

Documentation
☐ Update README
☐ Document API endpoints
☐ Create user guides
☐ Create admin guides
```

---

## 13. API Endpoints

### 13.1 Authentication Endpoints

```
POST   /api/auth/login
  Request: { email, password }
  Response: { access_token, refresh_token, user }
  Security: CSRF + Rate Limiting

POST   /api/auth/2fa/verify
  Request: { code }
  Response: { access_token }
  Security: 2FA TOTP validation

POST   /api/auth/logout
  Response: { message }
  Security: Token invalidation

POST   /api/auth/refresh
  Request: { refresh_token }
  Response: { access_token }
  Security: Token refresh with expiry

POST   /api/auth/password-reset
  Request: { email }
  Response: { message }
  Security: Email verification

GET    /api/auth/user
  Response: { user_data }
  Security: Sanctum auth required
```

### 13.2 Assessment Endpoints

```
GET    /api/assessments
  Query: page, limit, status, company_id
  Response: { data: [], pagination }
  Security: RBAC + Encryption

POST   /api/assessments
  Request: { title, description, company_id, assessment_type }
  Response: { assessment }
  Security: Validation + Audit log

GET    /api/assessments/{id}
  Response: { assessment, design_factors, gamo_selections }
  Security: Data decryption on read

PUT    /api/assessments/{id}
  Request: { title, description, status }
  Response: { assessment }
  Security: Audit trail + Encryption

DELETE /api/assessments/{id}
  Response: { message }
  Security: Soft delete + Archive

POST   /api/assessments/{id}/design-factors
  Request: { design_factors: {factor_id: value} }
  Response: { assessment }
  Security: Validation

POST   /api/assessments/{id}/gamo-selections
  Request: { gamo_objectives: [objective_id] }
  Response: { assessment }
  Security: RBAC

POST   /api/assessments/{id}/submit
  Response: { assessment }
  Security: State validation + Lock
```

### 13.3 GAMO Objectives Endpoints

```
GET    /api/gamo-objectives
  Query: category (EDM, APO, BAI, DSS, MEA)
  Response: { gamo_objectives: [] }
  Security: Public read

GET    /api/gamo-objectives/{id}
  Response: { gamo_objective, questions, guidance }
  Security: Public read

GET    /api/gamo-objectives/{id}/questions
  Response: { questions: [] }
  Security: Assessment-specific access
```

### 13.4 Question Endpoints

```
GET    /api/questions
  Query: gamo_objective_id, assessment_id
  Response: { questions: [] }
  Security: RBAC

POST   /api/questions
  Request: { code, text, gamo_objective_id, guidance }
  Response: { question }
  Security: Admin only

POST   /api/questions/import
  Request: { file (CSV/Excel) }
  Response: { imported_count, errors }
  Security: Malware scan + Validation

GET    /api/assessments/{id}/questions
  Response: { questions: [] }
  Security: Assessment access control
```

### 13.5 Answer Endpoints

```
GET    /api/assessments/{id}/answers
  Response: { answers: [] }
  Security: Assessment access + Decryption

POST   /api/assessments/{id}/answers
  Request: { question_id, answer_text, maturity_level }
  Response: { answer }
  Security: Encryption + Audit

PUT    /api/assessments/{id}/answers/{question_id}
  Request: { answer_text, maturity_level }
  Response: { answer }
  Security: Encryption + Audit

POST   /api/assessments/{id}/answers/batch
  Request: { answers: [{question_id, answer_text}] }
  Response: { count }
  Security: Transaction + Audit

POST   /api/assessments/{id}/answers/{question_id}/upload
  Request: { file }
  Response: { evidence_url }
  Security: Encryption + Malware scan
```

### 13.6 Scoring Endpoints

```
GET    /api/assessments/{id}/scores
  Response: { gamo_scores: [] }
  Security: Decryption on read

POST   /api/assessments/{id}/calculate
  Response: { overall_maturity, gaps, scores }
  Security: Calculation validation

GET    /api/assessments/{id}/maturity
  Response: { maturity_levels: {} }
  Security: Read-only
```

### 13.7 Report Endpoints

```
GET    /api/assessments/{id}/report
  Response: { report_data }
  Security: RBAC + Decryption

GET    /api/assessments/{id}/report/pdf
  Response: { PDF file }
  Security: Download audit log

GET    /api/assessments/{id}/report/excel
  Response: { Excel file }
  Security: Download audit log

GET    /api/reports/comparison
  Query: assessment_ids
  Response: { comparison_data }
  Security: Multi-assessment RBAC

GET    /api/reports/trend
  Query: company_id, months
  Response: { trend_data }
  Security: Historical data access
```

### 13.8 User Endpoints

```
GET    /api/users
  Query: page, limit, role
  Response: { users: [], pagination }
  Security: Admin only

POST   /api/users
  Request: { name, email, password, role_id }
  Response: { user }
  Security: Admin only + Password validation

GET    /api/users/{id}
  Response: { user }
  Security: Admin + own profile

PUT    /api/users/{id}
  Request: { name, email, department }
  Response: { user }
  Security: Admin + own profile

DELETE /api/users/{id}
  Response: { message }
  Security: Admin only

POST   /api/users/{id}/role
  Request: { role_id }
  Response: { user }
  Security: Admin only

POST   /api/users/{id}/2fa/enable
  Response: { secret, backup_codes }
  Security: Encrypted storage

POST   /api/users/{id}/2fa/verify
  Request: { code }
  Response: { message }
  Security: TOTP validation
```

### 13.9 Evidence Endpoints

```
POST   /api/assessments/{id}/evidence/upload
  Request: { file }
  Response: { evidence }
  Security: File validation + Encryption + Malware scan

GET    /api/evidence/{id}
  Response: { file_stream }
  Security: Access control + Audit log

GET    /api/evidence/{id}/download
  Response: { file_download }
  Security: Access control + Audit log

DELETE /api/evidence/{id}
  Response: { message }
  Security: Owner/admin only
```

### 13.10 Recommendation Endpoints

```
GET    /api/assessments/{id}/recommendations
  Response: { recommendations: [] }
  Security: RBAC

POST   /api/assessments/{id}/recommendations
  Request: { gamo_objective_id, title, description, priority }
  Response: { recommendation }
  Security: Auto-generate or manual

PUT    /api/recommendations/{id}
  Request: { title, description, priority, status }
  Response: { recommendation }
  Security: Owner/admin only

POST   /api/recommendations/{id}/track
  Request: { progress_percentage }
  Response: { recommendation }
  Security: Owner/manager only
```

### 13.11 Audit Log Endpoints

```
GET    /api/audit-logs
  Query: page, limit, action, user_id, date_range
  Response: { audit_logs: [], pagination }
  Security: Admin only

GET    /api/audit-logs/{id}
  Response: { audit_log }
  Security: Admin only + Decryption

GET    /api/audit-logs/sensitive-access
  Response: { sensitive_logs: [] }
  Security: Admin only

POST   /api/audit-logs/export
  Request: { date_range, filters }
  Response: { CSV file }
  Security: Admin only + Audit export
```

### 13.12 Security Endpoints

```
POST   /api/security/password-change
  Request: { old_password, new_password, new_password_confirmation }
  Response: { message }
  Security: Own profile + Password validation

GET    /api/security/sessions
  Response: { sessions: [] }
  Security: Own sessions only

DELETE /api/security/sessions/{id}
  Response: { message }
  Security: Own sessions only

GET    /api/security/api-tokens
  Response: { tokens: [] }
  Security: Own tokens only

POST   /api/security/api-tokens
  Request: { name, scopes }
  Response: { token }
  Security: Token generation + Hashing

DELETE /api/security/api-tokens/{id}
  Response: { message }
  Security: Own tokens only
```

---

## 14. Entity Relationship Diagram

### 14.1 COBIT 2019 ERD (High-Level)

```
┌─────────────────────────────────────────────────────────┐
│                  DESIGN FACTORS                         │
│  (10 Design Factors untuk Assessment Tailoring)         │
└────────────┬────────────────────────────────────────────┘
             │ (1:M)
             │
    ┌────────▼──────────────────────────┐
    │ ASSESSMENT_DESIGN_FACTORS         │
    │ (Selected untuk setiap Assessment)│
    └────────┬───────────────────────────┘
             │ (M:1)
             │
    ┌────────▼──────────────────┐
    │    ASSESSMENTS            │
    │  - Code                   │
    │  - Title                  │
    │  - Status                 │
    │  - Progress               │
    └────────┬──────────────────┘
             │ (1:M)
             ├──────────────────────────┬──────────────────┐
             │                          │                  │
    ┌────────▼─────────────────┐ ┌─────▼──────────────────┐
    │ ASSESSMENT_GAMO_         │ │ GAMO_SCORES            │
    │ SELECTIONS               │ │ - Current Maturity     │
    │ (Selected GAMO for       │ │ - Target Maturity      │
    │  setiap Assessment)      │ │ - Capability Score     │
    └────────┬─────────────────┘ └─────┬──────────────────┘
             │ (M:1)                   │ (M:1)
             │                         │
             └──────────┬──────────────┘
                        │
             ┌──────────▼────────────┐
             │  GAMO_OBJECTIVES      │
             │ - Code (EDM/APO/..)   │
             │ - Name                │
             │ - Category            │
             │ - Description         │
             └──────────┬────────────┘
                        │ (1:M)
                        │
             ┌──────────▼────────────┐
             │  GAMO_QUESTIONS       │
             │ - Question Text       │
             │ - Guidance            │
             │ - Evidence Req        │
             │ - Type                │
             └──────────┬────────────┘
                        │ (1:M)
                        │
    ┌───────────────────▼─────────────────┐
    │     ASSESSMENT_ANSWERS              │
    │ - Answer Text (Encrypted)           │
    │ - Maturity Level                    │
    │ - Capability Score                  │
    │ - Evidence File (Encrypted)         │
    └────────┬────────────────────────────┘
             │
             ├────────────────────────┐
             │                        │
    ┌────────▼──────────────┐ ┌──────▼──────────────┐
    │    EVIDENCE            │ │  AUDIT_LOGS         │
    │ - File (Encrypted)     │ │ - User              │
    │ - Upload Date          │ │ - Action            │
    │ - File Type            │ │ - Entity            │
    │ - Access Log           │ │ - Old/New Values    │
    └────────────────────────┘ └─────────────────────┘


┌─────────────────────────────────────────────────────────┐
│              USER & SECURITY LAYER                      │
└────────────┬────────────────────────────────────────────┘
             │
    ┌────────▼──────────────┐
    │      USERS            │
    │  (Encrypted fields)   │
    │  - Email (Enc)        │
    │  - Password (Hashed)  │
    │  - Role_ID            │
    └────────┬──────────────┘
             │ (M:1)
             ├─────────────────┬──────────────────┐
             │                 │                  │
    ┌────────▼──────┐ ┌────────▼──────────┐ ┌────▼────────────┐
    │   ROLES       │ │  USER_TOKENS      │ │ TWOFACTOR_AUTH  │
    │ - Name        │ │ - Token (Hashed)  │ │ - Secret (Enc)  │
    │ - Permissions │ │ - Expires At       │ │ - Backup Codes  │
    └───────────────┘ │ - Device Info     │ └─────────────────┘
                      │ - IP Address      │
                      └────────────────────┘


    ┌──────────────────────────────────────────────┐
    │  LOGIN_ATTEMPTS                              │
    │ - Email                                      │
    │ - IP Address                                 │
    │ - Success/Failure                            │
    │ - Attempted At                               │
    └──────────────────────────────────────────────┘


    ┌──────────────────────────────────────────────┐
    │  ENCRYPTION_KEYS_LOG                         │
    │ - Key Version                                │
    │ - Algorithm                                  │
    │ - Status                                     │
    │ - Rotation Date                              │
    └──────────────────────────────────────────────┘
```

### 14.2 Detailed Relationship Matrix

```
USERS (1) ────────────────────────── (M) ASSESSMENTS
  |                                      |
  |                                      |
  │ (1:M)                          (1:M) │
  └──────────── AUDIT_LOGS ────────────┘


ASSESSMENTS (1) ──────────────────── (M) ASSESSMENT_DESIGN_FACTORS ──────── (M) DESIGN_FACTORS


ASSESSMENTS (1) ──────────────────── (M) ASSESSMENT_GAMO_SELECTIONS ──────── (M) GAMO_OBJECTIVES


GAMO_OBJECTIVES (1) ───────────────── (M) GAMO_QUESTIONS ──────────── (M) ASSESSMENT_ANSWERS


ASSESSMENT_ANSWERS (M) ────────────── (1) ASSESSMENTS
                  │
                  │ (1:M)
                  │
           EVIDENCE (File Storage)


ASSESSMENTS (1) ───────────────────── (M) GAMO_SCORES ────────────── (M) GAMO_OBJECTIVES


USERS (1) ────────────────────────── (M) USER_TOKENS


USERS (1) ────────────────────────── (1) TWOFACTOR_AUTH


USERS (1) ────────────────────────── (M) LOGIN_ATTEMPTS


ROLES (1) ───────────────────────── (M) USERS


ASSESSMENTS (1) ───────────────────── (M) RECOMMENDATIONS
```

---

## 15. Deployment & Production Considerations

### 15.1 Security Deployment Checklist

```
Pre-Deployment
☐ Run security scanner (phpstan, psalm)
☐ Run dependency audit (composer audit, npm audit)
☐ Penetration testing
☐ OWASP Top 10 validation
☐ Code review for sensitive operations
☐ Database backup test

SSL/TLS Configuration
☐ Obtain SSL certificate (Let's Encrypt)
☐ Configure TLS 1.2+ minimum
☐ Enable HSTS (Strict-Transport-Security)
☐ Test SSL/TLS configuration
☐ Setup certificate auto-renewal

Web Server Configuration
☐ Configure nginx/Apache security headers
☐ Disable directory listing
☐ Hide server version info
☐ Configure firewall rules
☐ Setup WAF (Web Application Firewall)
☐ Configure DDoS protection

Database Security
☐ Enable encryption at rest
☐ Configure strong credentials
☐ Setup database backups (encrypted)
☐ Enable query logging (for audit)
☐ Restrict database user permissions
☐ Setup database replication

Encryption & Keys
☐ Generate strong encryption keys
☐ Setup key rotation schedule
☐ Secure key storage (AWS KMS / HashiCorp Vault)
☐ Test key rotation process
☐ Document key management procedures

Monitoring & Alerts
☐ Setup centralized logging (ELK/Splunk)
☐ Configure real-time alerts
☐ Setup performance monitoring
☐ Configure security monitoring
☐ Setup intrusion detection

Compliance
☐ Implement data retention policy
☐ Setup GDPR compliance (if applicable)
☐ Document data processing
☐ Setup consent management
☐ Implement data privacy controls
```

### 15.2 Docker Deployment Example

```dockerfile
# Dockerfile
FROM php:8.2-fpm-alpine

WORKDIR /app

# Install dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    libfreetype6-dev \
    postgresql-dev \
    redis \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd pdo pdo_mysql pdo_pgsql redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
```

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    container_name: assessme_app
    working_dir: /app
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=db
      - REDIS_HOST=redis
    ports:
      - "8000:9000"
    depends_on:
      - db
      - redis
    networks:
      - assessme
    volumes:
      - ./storage/app:/app/storage/app

  db:
    image: mysql:8.0
    container_name: assessme_db
    environment:
      MYSQL_DATABASE: assessme
      MYSQL_ROOT_PASSWORD: secure_password
      MYSQL_ENCRYPTION: 1
    ports:
      - "3306:3306"
    networks:
      - assessme
    volumes:
      - db_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    container_name: assessme_redis
    ports:
      - "6379:6379"
    networks:
      - assessme
    command: redis-server --requirepass redis_password

  nginx:
    image: nginx:alpine
    container_name: assessme_nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/nginx/ssl
      - ./storage/logs:/var/log/nginx
    depends_on:
      - app
    networks:
      - assessme

volumes:
  db_data:

networks:
  assessme:
    driver: bridge
```

---

## 16. Recommended Package Ecosystem

### 16.1 Composer Packages

#### Authentication & Security
```bash
composer require laravel/sanctum
composer require laravel/passport
composer require spatie/laravel-permission
composer require pragmarx/google2fa
composer require pragmarx/laravel-google2fa-qrcode
composer require "pragmarx/recovery-codes:*"
```

#### Database & Encryption
```bash
composer require spatie/laravel-database-encryption
composer require defuse/php-encryption
```

#### Audit & Logging
```bash
composer require spatie/laravel-activity-log
composer require sentry/sentry-laravel
```

#### Export & Reporting
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
composer require barryvdh/laravel-snappy
```

#### API & Documentation
```bash
composer require knuckleswtf/scribe
composer require fruitcake/laravel-cors
composer require symfony/http-client
```

#### Validation & Data
```bash
composer require illuminate/validation
composer require adamwathan/form
```

#### Development
```bash
composer require --dev laravel/debugbar
composer require --dev barryvdh/laravel-ide-helper
composer require --dev nunomaduro/phpstan-rules
composer require --dev phpstan/phpstan
```

#### Testing
```bash
composer require --dev phpunit/phpunit
composer require --dev pestphp/pest
composer require --dev pestphp/pest-plugin-laravel
composer require --dev fakerphp/faker
```

### 16.2 NPM Packages

```bash
# UI & CSS
npm install bootstrap@5
npm install bootstrap-icons
npm install @fortawesome/fontawesome-free

# JavaScript Utilities
npm install jquery
npm install axios
npm install sweetalert2
npm install toastr
npm install js-cookie

# Data Tables & UI Components
npm install datatables.net
npm install datatables.net-bs5
npm install select2
npm install flatpickr

# Charts & Visualization
npm install chart.js
npm install apexcharts

# Form Validation
npm install jquery-validation

# File Handling
npm install html2pdf
npm install file-saver

# Development
npm install --save-dev sass
npm install --save-dev vite
npm install --save-dev @vitejs/plugin-laravel
```

---

## 17. Security Best Practices Summary

```
┌─────────────────────────────────────────────────────────┐
│         LAYERED SECURITY ARCHITECTURE                   │
└─────────────────────────────────────────────────────────┘

Layer 1: Network Security
  ├── HTTPS/TLS 1.2+ (Mandatory)
  ├── Certificate Pinning
  ├── HSTS Header
  ├── DDoS Protection
  └── WAF (Web Application Firewall)

Layer 2: Authentication
  ├── Strong Password Policy
  ├── bcrypt Hashing (cost 12)
  ├── 2FA (TOTP)
  ├── Session Timeout
  └── Token Management

Layer 3: Authorization
  ├── RBAC (5 roles)
  ├── Permission Matrix
  ├── Field-level Access Control
  ├── Data Encryption
  └── Audit Logging

Layer 4: Input Protection
  ├── Server-side Validation
  ├── Input Sanitization
  ├── SQL Injection Prevention
  ├── XSS Prevention
  └── File Upload Security

Layer 5: Data Protection
  ├── Field-level Encryption (AES-256-CBC)
  ├── File Encryption
  ├── Database Encryption
  ├── Backup Encryption
  └── Key Rotation

Layer 6: Monitoring & Response
  ├── Audit Logging
  ├── Intrusion Detection
  ├── Alert System
  ├── Security Monitoring
  └── Incident Response

Layer 7: Compliance
  ├── Data Retention Policy
  ├── GDPR Compliance
  ├── Security Standards (ISO 27001)
  ├── Audit Trail
  └── Compliance Reporting
```

---

## 18. Timeline Pengembangan (Estimasi)

### Phase 1: Infrastructure & Security (3 minggu)
```
Week 1-2
- Setup project structure
- Configure Laravel security modules
- Implement authentication (password + 2FA)
- Setup database encryption
- Configure CSRF protection

Week 3
- Implement authorization (RBAC)
- Setup audit logging
- Configure security headers
- Test security implementation
```

### Phase 2: Core COBIT 2019 Features (4 minggu)
```
Week 1
- Database design & migration
- Design Factor management
- GAMO Objectives setup

Week 2-3
- Assessment CRUD
- Design Factor selection
- GAMO selection interface
- Question management

Week 4
- Answer system
- Evidence upload & encryption
- Maturity scoring engine
- Basic dashboard
```

### Phase 3: Reporting & Advanced Features (3 minggu)
```
Week 1
- Scoring calculation
- Report generation
- PDF/Excel export

Week 2
- Recommendation engine
- Action plan tracking
- Email notifications

Week 3
- Advanced analytics
- Trend analysis
- Comparison reports
```

### Phase 4: Testing & Optimization (2 minggu)
```
Week 1
- Unit testing
- Integration testing
- Security testing
- Performance optimization

Week 2
- User acceptance testing
- Bug fixes
- Documentation
```

### Phase 5: Deployment & Training (1 minggu)
```
- Deployment setup
- Documentation completion
- User training materials
- Go-live support
```

**Total: 13 minggu (3+ bulan)**

---

## 19. Support & Maintenance

```
Post-Launch Support
├── Monitor system performance
├── Security monitoring & updates
├── Regular database backups
├── User support & bug fixes
├── Feature enhancements
├── Security patches
└── Compliance audits

Recommended Maintenance Schedule
├── Daily: System health checks
├── Weekly: Security updates, backup verification
├── Monthly: Performance optimization, audit log review
├── Quarterly: Security assessment, key rotation
└── Annually: Compliance audit, infrastructure review
```

---

**Versi Dokumen**: 2.0 (COBIT 2019)
**Terakhir Diupdate**: December 2024
**Status**: Ready for Development
**Security Level**: Enterprise Grade



### 13.1 Authentication & Authorization
- Implement JWT atau Laravel Sanctum untuk API
- Password hashing dengan bcrypt
- CSRF protection pada semua form
- Session timeout (30 menit inactivity)
- Two-factor authentication (optional)

### 13.2 Data Protection
- Encrypt sensitive data di database
- HTTPS mandatory
- SQL Injection prevention (prepared statements)
- XSS prevention (blade escaping)
- CORS configuration

### 13.3 Audit & Logging
- Log semua user activities
- Capture IP address dan User Agent
- Track data changes (before/after)
- Immutable audit logs
- Regular audit log review

### 13.4 File Upload Security
- Validate file type (whitelist)
- Limit file size
- Store outside public folder
- Scan for malware (optional)
- Generate unique filename

---

## 14. Performance Optimization

### 14.1 Database Optimization
```php
// Use lazy loading prevention
- Implement eager loading (with())
- Use select() untuk specific columns
- Index pada frequently queried columns
- Database connection pooling
```

### 14.2 Caching
```php
- Cache assessment lists
- Cache COBIT framework data
- Cache user permissions
- Cache report data
- Use Redis untuk session/cache
```

### 14.3 Frontend Optimization
```
- Minify CSS/JS
- Lazy load images
- Compress assets
- Browser caching
- CDN for static assets
```

---

## 15. Deployment Guide

### 15.1 Production Checklist

```
☐ Set APP_ENV=production
☐ Set APP_DEBUG=false
☐ Configure secure database connection
☐ Setup SSL/TLS certificate
☐ Configure firewall rules
☐ Setup backup strategy
☐ Configure email service
☐ Setup monitoring/logging
☐ Performance optimization
☐ Security scanning
```

### 15.2 Docker Deployment (Optional)

```dockerfile
# Dockerfile
FROM php:8.1-fpm

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

RUN docker-php-ext-configure gd
RUN docker-php-ext-install gd pdo pdo_mysql

COPY composer.lock composer.json ./
RUN curl -s http://getcomposer.org/installer | php && \
    php composer.phar install --no-dev --no-interaction

COPY . .

EXPOSE 9000
CMD ["php-fpm"]
```

---

## 16. Testing Strategy

### 16.1 Test Types

```
Unit Tests
├── Model tests
├── Service tests
└── Validation tests

Feature Tests
├── Authentication tests
├── Assessment workflow tests
├── Authorization tests
└── Report generation tests

Integration Tests
├── Database integration
├── API integration
└── External service integration
```

### 16.2 Example Test

```php
// tests/Feature/AssessmentTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Assessment;

class AssessmentTest extends TestCase
{
    public function test_user_can_create_assessment()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/assessments', [
                'title' => 'Assessment 2024',
                'description' => 'Initial Assessment'
            ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('assessments', [
            'title' => 'Assessment 2024'
        ]);
    }
}
```

---

## 17. Daftar Package Laravel

### Required Packages
```bash
# Authentication & Authorization
composer require laravel/sanctum
composer require spatie/laravel-permission

# Export & Report
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
composer require barryvdh/laravel-snappy

# Audit & Logging
composer require spatie/laravel-activity-log

# Validation
composer require illuminate/validation

# API
composer require laravel/passport
composer require fruitcake/laravel-cors

# Development
composer require --dev laravel/debugbar
composer require --dev barryvdh/laravel-ide-helper

# Testing
composer require --dev phpunit/phpunit
composer require --dev fakerphp/faker
```

---

## 18. Frontend Libraries (NPM)

### Essential Libraries
```bash
# CSS & UI
npm install bootstrap
npm install bootstrap-icons
npm install @fortawesome/fontawesome-free

# JavaScript
npm install jquery
npm install datatables.net
npm install datatables.net-bs5
npm install apexcharts
npm install chart.js
npm install jquery-validation
npm install select2
npm install flatpickr
npm install toastr

# Data Handling
npm install axios
npm install sweetalert2
npm install html2pdf

# Development
npm install --save-dev sass
npm install --save-dev vite
```

---

## 19. Development Workflow

### 19.1 Git Branch Strategy

```
main/master          (Production Ready)
  ├── develop        (Development Branch)
  │   ├── feature/*  (Feature Development)
  │   ├── bugfix/*   (Bug Fixes)
  │   └── hotfix/*   (Production Fixes)
```

### 19.2 Commit Convention

```
feat: Add new feature
fix: Fix bug
docs: Documentation changes
style: Code style changes
refactor: Refactoring code
test: Adding tests
chore: Maintenance tasks
```

---

## 20. Timeline Pengembangan (Estimasi)

### Phase 1: Setup & Infrastructure (2 minggu)
```
- Setup project structure
- Database design & migration
- Authentication system
- Basic CRUD operations
```

### Phase 2: Core Functionality (4 minggu)
```
- Assessment management
- Question management
- Answer system
- Scoring engine
- Basic report
```

### Phase 3: Advanced Features (3 minggu)
```
- Recommendation engine
- Export functionality
- Dashboard & analytics
- Email notifications
```

### Phase 4: Testing & Optimization (2 minggu)
```
- Unit testing
- Integration testing
- Performance optimization
- Security hardening
```

### Phase 5: Deployment & Documentation (1 minggu)
```
- Documentation
- Deployment preparation
- User training materials
- Go-live support
```

**Total: 12 minggu (3 bulan)**

---

## 21. Kontak & Support

```
Technical Lead  : [Name]
Database Admin  : [Name]
QA Lead        : [Name]
Documentation  : [Name]

Email: support@assessme.com
Repository: https://github.com/your-org/assessme
Documentation: https://docs.assessme.com
```

---

**Versi Dokumen**: 1.0
**Terakhir Diupdate**: December 2024
**Status**: Draft Ready for Development

