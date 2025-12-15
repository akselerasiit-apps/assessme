# Master Data Management - Implementation Guide

## 📋 SUMMARY: Dimana Menu Master Data?

### ✅ **SOLUSI SUDAH DIIMPLEMENTASIKAN**

Berdasarkan analisis Boilerplate.md dan codebase existing, saya telah menambahkan menu **"Master Data"** untuk mengelola:

1. **Companies** (Perusahaan)
2. **Design Factors** (10 Faktor Desain COBIT 2019)
3. **GAMO Objectives** (23 Objektif EDM/APO/BAI/DSS/MEA)

---

## 🎯 JAWABAN PERTANYAAN ANDA:

### **1. Dimana saya kelola master data Design Factor dan GAMO?**

**Menu:** `Master Data` → `Design Factors` / `GAMO Objectives`
**Role:** Super Admin ONLY
**Lokasi:** Navbar (menu horizontal paling atas)
**Routes:**
- Design Factors: `/master-data/design-factors`
- GAMO Objectives: `/master-data/gamo-objectives`

**Fitur yang tersedia:**
- ✅ View list semua Design Factors (10 faktor)
- ✅ Create new Design Factor
- ✅ Edit existing Design Factor
- ✅ Delete Design Factor
- ✅ Toggle Active/Inactive status
- ✅ View list semua GAMO Objectives (23 objectives)
- ✅ Create new GAMO Objective
- ✅ Edit existing GAMO Objective (termasuk deskripsi Indonesia)
- ✅ Delete GAMO Objective
- ✅ Toggle Active/Inactive status
- ✅ Filter by category (EDM, APO, BAI, DSS, MEA)

---

### **2. Dimana saya menambahkan company?**

**Menu:** `Master Data` → `Companies`
**Role:** Super Admin ONLY
**Lokasi:** Navbar (menu horizontal paling atas)
**Route:** `/master-data/companies`

**Fitur yang tersedia:**
- ✅ View list semua companies
- ✅ Create new company
- ✅ Edit existing company
- ✅ Delete company
- ✅ View company details (nama, address, phone, email, industry, size, established year)

**Fields Company:**
```
- Name (required)
- Address
- Phone
- Email
- Industry (e.g., Finance, Healthcare, IT, Manufacturing)
- Size (startup, sme, enterprise)
- Established Year
```

---

### **3. Dimana saya menambahkan assessment pada setiap company yang saya tambahkan?**

**Menu:** `Assessments` → `Create Assessment`
**Role:** Manager, Admin, Super Admin
**Lokasi:** Navbar (menu horizontal paling atas)
**Route:** `/assessments/create`

**Flow:**
1. Klik menu **"Assessments"** → **"Create Assessment"**
2. Pilih **Company** dari dropdown (semua companies yang sudah dibuat akan muncul)
3. Isi detail assessment:
   - Title
   - Description
   - Assessment Type (initial, periodic, specific)
   - Scope Type (full, tailored)
   - Period Start & End Date
4. **Step 1:** Select Design Factors (10 faktor)
5. **Step 2:** Select GAMO Objectives (pilih dari 23 objectives)
6. Submit assessment

**Lihat Assessment per Company:**
- Menu: `Assessments` → `All Assessments`
- Filter by company dropdown
- View, Edit, Delete assessment

---

## 🔐 ROLE ACCESS MATRIX (Berdasarkan Boilerplate)

| Menu / Feature | Super Admin | Admin | Manager | Assessor | Viewer |
|----------------|-------------|-------|---------|----------|--------|
| **Master Data** |
| - Companies | ✅ CRUD | ❌ | ❌ | ❌ | ❌ |
| - Design Factors | ✅ CRUD | ❌ | ❌ | ❌ | ❌ |
| - GAMO Objectives | ✅ CRUD | ❌ | ❌ | ❌ | ❌ |
| **Assessments** |
| - View All | ✅ | ✅ | ✅ | ✅ Own | ✅ Approved |
| - Create | ✅ | ✅ | ✅ | ❌ | ❌ |
| - Edit | ✅ | ✅ | ✅ Owner | ❌ | ❌ |
| - Delete | ✅ | ✅ | ❌ | ❌ | ❌ |
| - Answer Questions | ✅ | ✅ | ✅ | ✅ Assigned | ❌ |
| - Submit for Review | ✅ | ✅ | ✅ | ✅ | ❌ |
| - Approve | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Reports** |
| - View Reports | ✅ | ✅ | ✅ | ✅ | ✅ |
| - Export PDF/Excel | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Administration** |
| - User Management | ✅ | ✅ | ❌ | ❌ | ❌ |
| - Roles & Permissions | ✅ | ✅ | ❌ | ❌ | ❌ |
| - Audit Logs | ✅ | ✅ | ❌ | ❌ | ❌ |
| - System Settings | ✅ | ❌ | ❌ | ❌ | ❌ |

---

## 📱 STRUCTURE MENU NAVBAR (Updated)

```
┌─────────────────────────────────────────────────────────────┐
│  🏠 Dashboard  📋 Assessments  📊 Reports  ⚙️ Admin  🗄️ Master │
└─────────────────────────────────────────────────────────────┘

**1. Dashboard**
   └── (Dashboard overview)

**2. Assessments** (Manager, Admin, Super Admin, Assessor, Viewer)
   ├── All Assessments
   ├── Create Assessment  ← CREATE ASSESSMENT PER COMPANY DISINI
   └── My Assessments

**3. Reports** (All roles)
   └── View Reports

**4. Administration** (Admin, Super Admin)
   ├── Users
   ├── Roles & Permissions
   ├── Audit Logs
   └── System Settings

**5. Master Data** (Super Admin ONLY) ← MENU BARU
   ├── Companies           ← KELOLA COMPANY DISINI
   ├── Design Factors      ← KELOLA 10 DESIGN FACTORS DISINI
   └── GAMO Objectives     ← KELOLA 23 GAMO OBJECTIVES DISINI
```

---

## 🔄 WORKFLOW: Membuat Assessment untuk Company

### **Alur Lengkap:**

```
Step 1: Login sebagai Super Admin
        └── Buka menu "Master Data" → "Companies"

Step 2: Tambah Company Baru
        ├── Click "Add Company"
        ├── Isi data company:
        │   ├── Name: PT Teknologi Indonesia
        │   ├── Industry: Information Technology
        │   ├── Size: Enterprise
        │   ├── Address, Phone, Email
        │   └── Established Year: 2020
        └── Save Company

Step 3: Login sebagai Manager/Admin
        └── Buka menu "Assessments" → "Create Assessment"

Step 4: Pilih Company & Buat Assessment
        ├── Select Company: PT Teknologi Indonesia (dari dropdown)
        ├── Title: "Initial COBIT Assessment 2024"
        ├── Description: "First assessment untuk governance evaluation"
        ├── Assessment Type: Initial
        ├── Scope Type: Tailored (pakai design factors)
        ├── Period: 01/01/2024 - 31/03/2024
        └── Click "Next"

Step 5: Select Design Factors (Phase 1 - Tailoring)
        ├── ☑ Enterprise Strategy
        ├── ☑ Enterprise Goals
        ├── ☑ Risk Profile
        ├── ☑ Threat Landscape
        ├── ☑ Compliance Requirements
        ├── ☑ Role of IT
        └── Click "Next"

Step 6: Select GAMO Objectives (Phase 2 - Scope Definition)
        ├── EDM Category:
        │   ├── ☑ EDM01 - Ensure Governance Framework
        │   ├── ☑ EDM02 - Ensure Benefits Delivery
        │   └── ☑ EDM03 - Ensure Risk Optimization
        ├── APO Category:
        │   ├── ☑ APO01 - Manage IT Management Framework
        │   ├── ☑ APO02 - Manage Strategy
        │   └── ☑ APO03 - Manage Architecture
        └── Click "Create Assessment"

Step 7: Assessment Created!
        └── Sekarang assessment sudah terbuat untuk company tersebut
        └── Assign ke Assessor untuk mulai answer questions

Step 8: Answer Questions (Assessor)
        ├── Buka "My Assessments"
        ├── Click assessment yang assigned
        ├── Answer questions untuk setiap GAMO yang dipilih
        ├── Upload evidence
        ├── Rate maturity level (0-5)
        └── Submit answers

Step 9: Review & Approve (Manager)
        ├── Review assessment results
        ├── Approve or Request Banding
        └── Generate Reports

Step 10: View Reports
        └── Dashboard dengan visualisasi
        └── Export PDF/Excel
```

---

## 🎨 UI/UX DESIGN (Using Tabler Bootstrap 5)

### **Master Data - Companies List Page**
```html
┌────────────────────────────────────────────────────────────┐
│  🏢 Companies Management                    [+ Add Company]│
├────────────────────────────────────────────────────────────┤
│  Search: [____________]  Industry: [All ▼]  Size: [All ▼] │
├────────────────────────────────────────────────────────────┤
│  Name              Industry      Size        Actions       │
│  ─────────────────────────────────────────────────────────│
│  PT Tech Indo      IT            Enterprise  [✏️][🗑️]      │
│  CV Digital Maju   Finance       SME         [✏️][🗑️]      │
│  Bank Central      Banking       Enterprise  [✏️][🗑️]      │
│  Startup XYZ       Technology    Startup     [✏️][🗑️]      │
└────────────────────────────────────────────────────────────┘
```

### **Master Data - Design Factors List Page**
```html
┌────────────────────────────────────────────────────────────┐
│  🧩 Design Factors (10 Factors)       [+ Add Design Factor]│
├────────────────────────────────────────────────────────────┤
│  Code  Name                          Status    Actions     │
│  ───────────────────────────────────────────────────────────│
│  ES    Enterprise Strategy            ✅ Active [✏️][🗑️]   │
│  EG    Enterprise Goals               ✅ Active [✏️][🗑️]   │
│  RP    Risk Profile                   ✅ Active [✏️][🗑️]   │
│  ITI   I&T Related Issues             ✅ Active [✏️][🗑️]   │
│  TL    Threat Landscape               ✅ Active [✏️][🗑️]   │
│  CR    Compliance Requirements        ✅ Active [✏️][🗑️]   │
│  RIT   Role of IT                     ✅ Active [✏️][🗑️]   │
│  SM    Sourcing Model for IT          ✅ Active [✏️][🗑️]   │
│  IM    IT Implementation Methods      ✅ Active [✏️][🗑️]   │
│  TA    Technology Strategy Adoption   ✅ Active [✏️][🗑️]   │
└────────────────────────────────────────────────────────────┘
```

### **Master Data - GAMO Objectives List Page**
```html
┌────────────────────────────────────────────────────────────┐
│  🎯 GAMO Objectives (23 Objectives)  [+ Add GAMO Objective]│
├────────────────────────────────────────────────────────────┤
│  Filter: [All Categories ▼] [EDM][APO][BAI][DSS][MEA]    │
├────────────────────────────────────────────────────────────┤
│  Code   Name                            Category  Actions  │
│  ───────────────────────────────────────────────────────────│
│  EDM01  Ensure Governance Framework     EDM      [✏️][🗑️] │
│  EDM02  Ensure Benefits Delivery        EDM      [✏️][🗑️] │
│  EDM03  Ensure Risk Optimization        EDM      [✏️][🗑️] │
│  APO01  Manage IT Management Framework  APO      [✏️][🗑️] │
│  APO02  Manage Strategy                 APO      [✏️][🗑️] │
│  BAI01  Manage Programs & Projects      BAI      [✏️][🗑️] │
│  DSS01  Manage Operations               DSS      [✏️][🗑️] │
│  MEA01  Monitor & Evaluate Performance  MEA      [✏️][🗑️] │
└────────────────────────────────────────────────────────────┘
```

---

## 📂 FILES YANG SUDAH DIBUAT/DIMODIFIKASI

### **1. Navbar Menu (Updated)**
```
File: resources/views/layouts/partials/navbar.blade.php
Status: ✅ Updated
Changes:
- Added "Master Data" dropdown menu
- Added 3 submenu: Companies, Design Factors, GAMO Objectives
- Restricted to Super Admin role only
```

### **2. Web Routes (Updated)**
```
File: routes/web.php
Status: ✅ Updated
Changes:
- Added /master-data/* routes group
- Companies CRUD routes
- Design Factors CRUD routes
- GAMO Objectives CRUD routes
- All routes protected with role:Super Admin middleware
```

### **3. Web Controllers (Created)**
```
Files Created:
✅ app/Http/Controllers/Web/CompanyWebController.php
✅ app/Http/Controllers/Web/DesignFactorWebController.php
✅ app/Http/Controllers/Web/GamoObjectiveWebController.php

Status: Controllers created, need implementation
```

### **4. Views (Need to Create)**
```
Files to Create:
📝 resources/views/master-data/companies/index.blade.php
📝 resources/views/master-data/companies/create.blade.php
📝 resources/views/master-data/companies/edit.blade.php
📝 resources/views/master-data/design-factors/index.blade.php
📝 resources/views/master-data/design-factors/create.blade.php
📝 resources/views/master-data/design-factors/edit.blade.php
📝 resources/views/master-data/gamo-objectives/index.blade.php
📝 resources/views/master-data/gamo-objectives/create.blade.php
📝 resources/views/master-data/gamo-objectives/edit.blade.php
```

---

## 🚀 NEXT STEPS - Implementasi Controllers & Views

### **Priority 1: Company Web Controller Implementation**
- index() - List all companies with DataTables
- create() - Show create company form
- store() - Save new company
- edit() - Show edit company form
- update() - Update existing company
- destroy() - Delete company

### **Priority 2: Design Factor Web Controller Implementation**
- index() - List all 10 design factors
- create() - Add new design factor
- store() - Save design factor
- edit() - Edit design factor
- update() - Update design factor
- destroy() - Delete design factor
- toggleActive() - Toggle active/inactive status

### **Priority 3: GAMO Objective Web Controller Implementation**
- index() - List all 23 GAMO objectives with filter by category
- create() - Add new GAMO objective
- store() - Save GAMO objective
- edit() - Edit GAMO objective
- update() - Update GAMO objective
- destroy() - Delete GAMO objective
- toggleActive() - Toggle active/inactive status

### **Priority 4: Blade Views Creation**
- Use Tabler Bootstrap 5 components
- DataTables for listing pages
- Form validation
- Modal dialogs for delete confirmation
- Toast notifications for success/error messages

---

## ✅ VERIFICATION CHECKLIST

Before deploying to production, verify:

- [ ] Login sebagai Super Admin
- [ ] Bisa akses menu "Master Data" (3 submenu visible)
- [ ] Menu tidak muncul untuk role lain (Admin, Manager, Assessor, Viewer)
- [ ] Bisa CRUD Companies
- [ ] Bisa CRUD Design Factors
- [ ] Bisa CRUD GAMO Objectives
- [ ] Dropdown company muncul di "Create Assessment"
- [ ] Bisa create assessment dengan company yang sudah dibuat
- [ ] Design Factors selection berfungsi
- [ ] GAMO Objectives selection berfungsi
- [ ] Assessment terbuat dengan benar dan linked ke company

---

## 📞 SUPPORT & QUESTIONS

Jika masih ada pertanyaan:

**Q: Kenapa menu Master Data tidak muncul?**
A: Menu ini hanya untuk Super Admin. Login dengan role Super Admin untuk melihat menu.

**Q: Bagaimana cara login sebagai Super Admin?**
A: Gunakan credentials:
   - Email: superadmin@assessme.com
   - Password: Password123!

**Q: Bisa Role Admin akses Master Data?**
A: Tidak. Hanya Super Admin. Ini sesuai best practice security.

**Q: Bagaimana edit data master yang sudah di-seed?**
A: Buka Master Data, click Edit pada row yang ingin diubah.

**Q: Bisa hapus Design Factor atau GAMO yang sudah ada assessment?**
A: Tidak. System akan prevent delete jika ada foreign key constraint.

---

**Status:** ✅ READY TO IMPLEMENT VIEWS & TEST
**Next:** Implementasi controller logic dan create Blade views
