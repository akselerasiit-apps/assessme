# 📋 UPDATE SUMMARY - COBIT 2019 Assessment Application

**Date**: December 9, 2025  
**Version**: 2.1  
**Status**: Updated & Ready

---

## 🎯 3 PERUBAHAN UTAMA YANG DILAKUKAN

### 1️⃣ BANDING/APPEAL SYSTEM

#### Deskripsi
Setelah assessor melakukan wawancara, upload evidence, dan memberikan nilai maturity level, sistem memungkinkan **banding** untuk refine hasil assessment jika diperlukan.

#### Alur Banding

```
ASSESSMENT COMPLETION
        │
        ├─→ Manager Review
        │   ├─→ Approve ✓
        │   └─→ Request Banding (jika ada gap/ketidaksesuaian)
        │
        ▼
PHASE 6.5: BANDING/APPEAL
        │
        ├─ Banding dapat diajukan untuk:
        │  ├─ Evidence tidak lengkap
        │  ├─ Pertanyaan kurang detail/tidak sesuai
        │  ├─ Maturity level tidak sesuai dengan evidence
        │  └─ Temuan baru dari stakeholder
        │
        └─ Banding Handler dapat:
           ├─ Tambah/edit jawaban sebelumnya
           ├─ Upload evidence tambahan (baru)
           ├─ Re-conduct interview jika perlu
           ├─ Update maturity level scoring
           └─ Add detailed notes
        │
        ▼
RECORD AUDIT TRAIL
├─ Old Values: Nilai sebelum banding
├─ New Values: Nilai setelah banding  
├─ Banding Reason: Alasan banding
├─ Banding Round: Multiple banding allowed
└─ Approval Status: Draft → Submitted → Approved/Rejected
        │
        ▼
FINAL ASSESSMENT (dengan nilai yang sudah di-refine)
```

#### Database Schema untuk Banding

```sql
-- Tabel assessment_bandings
CREATE TABLE assessment_bandings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    gamo_objective_id BIGINT NOT NULL,
    banding_round INT DEFAULT 1,                    -- Support multiple banding rounds
    initiated_by BIGINT NOT NULL,                   -- Who requested banding
    banding_reason VARCHAR(255) NOT NULL,           -- Reason for banding
    banding_description LONGTEXT,                   -- Detailed description
    old_maturity_level DECIMAL(3,2),                -- Value before banding
    new_maturity_level DECIMAL(3,2),                -- Value after banding
    old_evidence_count INT,
    new_evidence_count INT,
    additional_evidence_files VARCHAR(500),         -- New evidence uploaded
    revised_answers LONGTEXT,                       -- Updated answers
    status ENUM('draft', 'submitted', 'approved', 'rejected'),
    approved_by BIGINT,
    approval_notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id),
    FOREIGN KEY (gamo_objective_id) REFERENCES gamo_objectives(id),
    FOREIGN KEY (initiated_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    KEY idx_status (status),
    KEY idx_round (banding_round)
);
```

#### Fitur Banding
- ✅ Multiple banding rounds (bisa di-banding berkali-kali)
- ✅ Track history setiap banding
- ✅ Audit trail lengkap (before/after values)
- ✅ Evidence tambahan dapat diunggah
- ✅ Jawaban dapat direvisi/dilengkapi
- ✅ Maturity level dapat diubah sesuai evidence baru
- ✅ Approval workflow

---

### 2️⃣ TARGET MATURITY LEVEL PER GAMO + CAPABILITY SCORING

#### Deskripsi
Setiap GAMO Objective memiliki:
1. **Current Maturity Level** - Nilai actual sekarang
2. **Target Maturity Level** - Nilai yang ingin dicapai (set independently per GAMO)
3. **Capability Assessment** - Penilaian detail untuk setiap level (seperti screenshot Anda)

#### Target Maturity Configuration per GAMO

```
Contoh EDM02 (dari screenshot Anda):
┌─────────────────────────────────────────────────┐
│ GAMO EDM02: IT-Related Business Risk            │
├─────────────────────────────────────────────────┤
│ Current Level:     1 (Initial/Ad Hoc)           │
│ Target Level:      3 (Defined)                  │
│ Gap:              2 levels                       │
│ Priority:         HIGH                           │
│ Effort:           Medium (6-9 months)            │
│ Timeline:         Q3 2025                        │
│ Status:           On Track                       │
└─────────────────────────────────────────────────┘

Contoh APO02:
┌─────────────────────────────────────────────────┐
│ GAMO APO02: Manage Strategy                     │
├─────────────────────────────────────────────────┤
│ Current Level:     2 (Repeatable)               │
│ Target Level:      2 (Already Achieved)         │
│ Gap:              0 levels                       │
│ Priority:         LOW                            │
│ Status:           Achieved                       │
└─────────────────────────────────────────────────┘
```

#### Capability Scoring per Level (seperti screenshot)

Dari screenshot Anda, setiap GAMO memiliki penilaian untuk setiap level:

```
GAMO EDM02 Capability Scoring:
┌──────────────────────────────────────────────────┐
│ Level 2 - Managed          Compliance: 1         │
│ Level 3 - Defined          Compliance: 0.85      │
│ Level 4 - Quantitatively   Compliance: 0.39      │
│ RATA-RATA SCORE:           3.24 (Overall)        │
│ WEIGHT untuk setiap level:  [Aksi/Treatment]    │
└──────────────────────────────────────────────────┘

Penjelasan:
- Setiap level memiliki compliance score berbeda
- Score menunjukkan sejauh mana achievement di level tersebut
- Average dari semua levels = Overall Score
- Digunakan untuk prioritizing improvement actions
```

#### Level Details

```
Level 0 - Incomplete
├── Not performed or ineffective
└── Score: Not applicable

Level 1 - Performed
├── Process executed; purpose achieved
├── Compliance: 100% (expected)
├── Questions per GAMO: 3-5
└── Evidence Required: Min 1

Level 2 - Managed (dari screenshot: Compliance 1)
├── Results managed and monitored
├── Compliance Score: 1.00 (Full Compliance)
├── Questions per GAMO: 4-6
├── Evidence Required: Min 2
└── Additional: Documentation, standards, procedures

Level 3 - Defined (dari screenshot: Compliance 0.85)
├── Process tailored; predictable results
├── Compliance Score: 0.85 (High)
├── Questions per GAMO: 5-7
├── Evidence Required: Min 3
├── Additional: Role definition, training, change control
└── Status: Standard procedures established

Level 4 - Quantitatively Managed (dari screenshot: Compliance 0.39)
├── Process measured and controlled
├── Compliance Score: 0.39 (Medium)
├── Questions per GAMO: 6-8
├── Evidence Required: Min 4
└── Additional: Metrics, KPIs, analytics, root cause analysis

Level 5 - Optimizing
├── Continually improved and optimized
├── Questions per GAMO: 7-10
├── Evidence Required: Min 5
└── Additional: Innovation, benchmarking, continuous improvement
```

#### Database Schema untuk Target & Capability

```sql
-- Tabel: assessment_gamo_target_levels
CREATE TABLE assessment_gamo_target_levels (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT NOT NULL,
    gamo_objective_id BIGINT NOT NULL,
    current_maturity_level DECIMAL(3,2) DEFAULT 0,
    target_maturity_level DECIMAL(3,2) DEFAULT 3,  -- Set independently
    priority ENUM('LOW', 'MEDIUM', 'HIGH', 'CRITICAL') DEFAULT 'MEDIUM',
    estimated_effort VARCHAR(100),                  -- e.g., "Medium (6-9 months)"
    target_achievement_date DATE,
    gap_analysis TEXT,
    recommended_actions TEXT,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id),
    FOREIGN KEY (gamo_objective_id) REFERENCES gamo_objectives(id),
    UNIQUE KEY unique_target (assessment_id, gamo_objective_id)
);

-- Tabel: gamo_capability_definitions
CREATE TABLE gamo_capability_definitions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    gamo_objective_id BIGINT NOT NULL,
    level INT (0-5),                         -- Per level capability definition
    level_name VARCHAR(100),
    compliance_score DECIMAL(5,2),           -- e.g., 1.00, 0.85, 0.39
    weight INT DEFAULT 1,                    -- Weight untuk calculation
    min_questions INT,                       -- Min questions for this level
    max_questions INT,
    required_evidence_count INT,
    required_compliance_percentage INT,      -- e.g., 75% untuk Level 2
    additional_requirements LONGTEXT,
    guidance_text LONGTEXT,
    examples LONGTEXT,
    created_at TIMESTAMP,
    FOREIGN KEY (gamo_objective_id) REFERENCES gamo_objectives(id),
    UNIQUE KEY unique_level (gamo_objective_id, level)
);

-- Tabel: assessment_answer_capability_scores
CREATE TABLE assessment_answer_capability_scores (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    assessment_answer_id BIGINT NOT NULL,
    level INT (1-5),                         -- Which level being assessed
    compliance_score DECIMAL(5,2),
    compliance_percentage INT,                -- e.g., 88%
    achievement_status ENUM('NOT_ACHIEVED', 'PARTIALLY_ACHIEVED', 'FULLY_ACHIEVED'),
    evidence_provided BOOLEAN DEFAULT FALSE,
    evidence_count INT DEFAULT 0,
    assessment_notes TEXT,
    created_at TIMESTAMP,
    FOREIGN KEY (assessment_answer_id) REFERENCES assessment_answers(id),
    UNIQUE KEY unique_answer_level (assessment_answer_id, level)
);
```

#### Contoh Scoring Calculation

```
GAMO EDM02 Level 3 Assessment:
Question 1: Is IT risk management process defined?
├─ Answer: Yes, documented in policy
├─ Evidence: 2 files uploaded
└─ Score: 100% compliance

Question 2: Are risks monitored regularly?
├─ Answer: Quarterly reviews performed
├─ Evidence: 1 file uploaded
└─ Score: 85% compliance

Question 3: Is documentation maintained?
├─ Answer: Yes, centralized repository
├─ Evidence: 2 files + screenshot
└─ Score: 90% compliance

Question 4: Are procedures communicated?
├─ Answer: Partially, training provided
├─ Evidence: 1 file uploaded
└─ Score: 70% compliance

Question 5: Are tools/systems used?
├─ Answer: Yes, risk management system
├─ Evidence: Screenshot + documentation
└─ Score: 95% compliance

LEVEL 3 RESULT:
├─ Average Compliance: (100+85+90+70+95)/5 = 88%
├─ Target Score for Level 3: 85% (requirement)
├─ Status: ✅ ACHIEVED (88% >= 85%)
├─ Evidence Count: 9 files (>= 3 required)
└─ Capability Score: 0.88
```

---

### 3️⃣ DESKRIPSI GAMO DALAM BAHASA INDONESIA

#### Deskripsi
Setiap GAMO Objective sekarang memiliki **dua deskripsi**: Inggris (English) dan Indonesia (Indonesian).

#### Structure Tabel GAMO Objectives

```sql
CREATE TABLE gamo_objectives (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,         -- e.g., EDM01, APO02
    name VARCHAR(255) NOT NULL,               -- English name
    name_id VARCHAR(255),                     -- NEW: Indonesian name
    description LONGTEXT,                     -- English description
    description_id LONGTEXT,                  -- NEW: Indonesian description
    category ENUM('EDM', 'APO', 'BAI', 'DSS', 'MEA'),
    objective_order INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    KEY idx_code (code),
    KEY idx_category (category)
);
```

#### Contoh Data (23 GAMO dengan deskripsi Indonesia)

```
EDM (Evaluate, Direct, Monitor) - 5 Objectives

EDM01
├─ EN: Evaluate, Direct and Monitor the Set of Enterprise Goals
├─ ID: Evaluasi, Arahkan, dan Pantau Pemenuhan Tujuan Perusahaan
├─ EN Description: Ensure business goals are understood, achieved, and monitored
└─ ID Description: Pastikan tujuan bisnis dipahami, dicapai, dan dipantau

EDM02
├─ EN: Evaluate, Direct and Monitor IT-Related Business Risk
├─ ID: Evaluasi, Arahkan, dan Pantau Risiko Bisnis Terkait IT
├─ EN Description: Manage and monitor IT-related business risks
└─ ID Description: Kelola dan pantau risiko bisnis yang terkait dengan IT

EDM03
├─ EN: Evaluate, Direct and Monitor IT Compliance
├─ ID: Evaluasi, Arahkan, dan Pantau Kepatuhan IT
├─ EN Description: Ensure IT operations are compliant with laws and regulations
└─ ID Description: Pastikan operasi IT mematuhi hukum dan regulasi

EDM04
├─ EN: Evaluate, Direct and Monitor IT Governance
├─ ID: Evaluasi, Arahkan, dan Pantau Governance IT
├─ EN Description: Establish and monitor IT governance framework
└─ ID Description: Tetapkan dan pantau kerangka kerja governance IT

EDM05
├─ EN: Evaluate, Direct and Monitor IT Investments
├─ ID: Evaluasi, Arahkan, dan Pantau Investasi IT
├─ EN Description: Manage and optimize IT investments
└─ ID Description: Kelola dan optimalkan investasi IT


APO (Align, Plan, Organize) - 7 Objectives

APO01
├─ ID: Kelola Kerangka Kerja Manajemen IT
└─ Established framework aligned with business objectives

APO02
├─ ID: Kelola Strategi
└─ Develop strategy aligned with business and stakeholders

APO03
├─ ID: Kelola Arsitektur Enterprise
└─ Define architecture to guide IT decisions

APO04
├─ ID: Kelola Inovasi
└─ Identify innovations for competitive advantage

APO05
├─ ID: Kelola Portfolio
└─ Manage portfolio for optimal resource allocation

APO06
├─ ID: Kelola Budget dan Biaya
└─ Plan and control IT costs effectively

APO07
├─ ID: Kelola Sumber Daya Manusia
└─ Ensure appropriate skills and structure


BAI (Build, Acquire, Implement) - 4 Objectives

BAI01
├─ ID: Kelola Program dan Proyek
└─ Execute projects according to governance

BAI02
├─ ID: Kelola Definisi Requirement
└─ Gather and manage business requirements

BAI03
├─ ID: Kelola Identifikasi dan Pembangunan Solusi
└─ Design and implement solutions

BAI04
├─ ID: Kelola Ketersediaan dan Kapasitas
└─ Plan and manage capacity


DSS (Deliver, Service, Support) - 5 Objectives

DSS01
├─ ID: Kelola Operasi
└─ Execute and manage IT operations

DSS02
├─ ID: Kelola Permintaan Layanan dan Insiden
└─ Process and manage service requests

DSS03
├─ ID: Kelola Masalah
└─ Identify and resolve problems

DSS04
├─ ID: Kelola Kontinuitas
└─ Ensure business continuity

DSS05
├─ ID: Kelola Layanan Keamanan
└─ Implement security controls


MEA (Monitor, Evaluate, Assess) - 3 Objectives

MEA01
├─ ID: Pantau, Evaluasi, dan Asesmen Kinerja dan Kesesuaian
└─ Monitor performance and compliance

MEA02
├─ ID: Pantau, Evaluasi, dan Asesmen Sistem Pengendalian Internal
└─ Evaluate internal control effectiveness

MEA03
├─ ID: Pantau, Evaluasi, dan Asesmen Kepatuhan Terhadap Requirement Eksternal
└─ Monitor compliance with external requirements
```

#### Frontend Implementation

```html
<!-- Saat menampilkan GAMO -->
<div class="gamo-detail">
    <h3 class="gamo-code">{{ gamo.code }}</h3>
    
    <div class="gamo-names">
        <h4 class="gamo-name-en">{{ gamo.name }}</h4>
        <h4 class="gamo-name-id">{{ gamo.name_id }}</h4>
    </div>
    
    <div class="gamo-descriptions">
        <p class="description-en">
            <strong>English:</strong><br>
            {{ gamo.description }}
        </p>
        <p class="description-id">
            <strong>Bahasa Indonesia:</strong><br>
            {{ gamo.description_id }}
        </p>
    </div>
</div>
```

---

## 📊 SUMMARY OF CHANGES

| Aspect | Change | Impact |
|--------|--------|--------|
| **Alur Assessment** | Tambah PHASE 6.5: Banding/Appeal | Memungkinkan refine hasil sebelum final |
| **Evidence & Jawaban** | Bisa di-tambah/direvisi di banding | Flexibility untuk accuracy |
| **Banding Tracking** | Audit trail lengkap (old/new values) | Transparency dan accountability |
| **Target Maturity** | Set independently per GAMO | Customizable per objective |
| **Capability Scoring** | Detail penilaian per level (0-5) | Precise measurement seperti screenshot |
| **Compliance Score** | Berbeda untuk setiap level | Weighted assessment |
| **Database** | 4 table baru untuk support features | Scalable architecture |
| **GAMO Deskripsi** | Bilingual (EN + ID) | Better accessibility untuk pengguna Indonesia |
| **Multiple Rounds** | Support banding berkali-kali | Iterative refinement possible |
| **Documentation** | Lengkap dengan contoh | Easy to implement |

---

## ✅ FILES UPDATED

1. **Boilerplate.md** - Main documentation updated with:
   - Section 5.2: Updated target maturity & capability scoring
   - Section 5.3: Security flow
   - Section 6: Updated GAMO objectives dengan desc Indonesia
   - Database schema untuk banding & target maturity

2. **UPDATE_SUMMARY.md** - This file
   - Complete explanation of 3 changes
   - Database schema
   - Implementation examples

---

## 🚀 NEXT STEPS

1. **Database Migration** - Create 4 new tables:
   - `assessment_bandings`
   - `assessment_gamo_target_levels`
   - `gamo_capability_definitions`
   - `assessment_answer_capability_scores`

2. **Controller Implementation** - Create:
   - `BandingController.php` (handle banding logic)
   - `TargetMaturityController.php` (manage target levels)
   - `CapabilityController.php` (calculate capability scores)

3. **Views Development**:
   - Banding form/modal
   - Target maturity setting interface
   - Capability scoring display
   - Bilingual GAMO display

4. **Services**:
   - `BandingService.php` - Process banding workflow
   - `CapabilityScoringService.php` - Calculate scores
   - `TargetMaturityService.php` - Manage targets

5. **API Endpoints**:
   - POST `/api/assessments/{id}/banding`
   - PUT `/api/assessments/{id}/targets`
   - GET `/api/gamo-objectives` (dengan filter language)

---

**Ready for Development! 🎉**

