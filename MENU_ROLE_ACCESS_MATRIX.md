# 📊 Menu & Role Access Matrix - AssessMe COBIT 2019
**Tanggal:** 7 Januari 2026  
**Status:** ✅ Complete & Updated

---

## 🎯 ROLE DEFINITIONS

| Role | Level | Description |
|------|-------|-------------|
| **Super Admin** | 5 | Full system access, all features, all companies |
| **Admin** | 4 | Manage users, assessments, master data (own/all companies) |
| **Manager** | 3 | View & manage assessments, review workflow (own company) |
| **Assessor** | 2 | Create & conduct assessments, answer questions |
| **Viewer** | 1 | Read-only access to assessments & reports |

---

## 📋 COMPLETE MENU STRUCTURE & ACCESS

### 🏠 1. Dashboard
**Route:** `/dashboard`  
**Access:** All authenticated users

| Role | Can Access | Notes |
|------|-----------|-------|
| Super Admin | ✅ Yes | Full dashboard with all metrics |
| Admin | ✅ Yes | Dashboard with company-wide metrics |
| Manager | ✅ Yes | Dashboard with own company metrics |
| Assessor | ✅ Yes | Personal dashboard |
| Viewer | ✅ Yes | Read-only dashboard |

---

### 📋 2. Assessments Menu

#### 2.1 All Assessments
**Route:** `/assessments`  
**Access:** All roles (filtered by permissions)

| Role | Can Access | Can See |
|------|-----------|---------|
| Super Admin | ✅ Yes | All assessments from all companies |
| Admin | ✅ Yes | All assessments from all companies |
| Manager | ✅ Yes | Only own company assessments |
| Assessor | ✅ Yes | Own created/participated assessments |
| Viewer | ✅ Yes | Authorized assessments (read-only) |

#### 2.2 Create Assessment
**Route:** `/assessments/create`  
**Access:** Super Admin, Admin, Manager, Assessor

| Role | Can Access | Notes |
|------|-----------|-------|
| Super Admin | ✅ Yes | Create for any company |
| Admin | ✅ Yes | Create for any company |
| Manager | ✅ Yes | Create for own company |
| Assessor | ✅ Yes | Create for assigned company |
| Viewer | ❌ No | Read-only role |

#### 2.3 My Assessments
**Route:** `/assessments/my`  
**Access:** All roles

| Role | Can Access | Shows |
|------|-----------|-------|
| All Roles | ✅ Yes | Assessments created by or assigned to user |

#### 2.4 Assessment Details
**Route:** `/assessments/{id}`  
**Access:** Based on ownership/participation

**Features per Role:**
- **Super Admin/Admin:** Full access, can edit, delete
- **Manager:** Can view own company, can edit if owner
- **Assessor:** Can view & edit if assigned
- **Viewer:** Can view only (read-only)

#### 2.5 Assessment Taking
**Route:** `/assessments/{id}/take`  
**Access:** Assessor, Manager, Admin, Super Admin

| Role | Can Access | Can Do |
|------|-----------|--------|
| Super Admin | ✅ Yes | Answer questions, upload evidence |
| Admin | ✅ Yes | Answer questions, upload evidence |
| Manager | ✅ Yes | Answer questions, upload evidence |
| Assessor | ✅ Yes | Answer questions, upload evidence |
| Viewer | ❌ No | Cannot answer (read-only) |

#### 2.6 Evidence Management
**Route:** `/assessments/{id}/evidence`  
**Access:** Based on assessment permissions

**Features:**
- ✅ View evidence list (All roles with assessment access)
- ✅ Upload evidence (Assessor, Manager, Admin, Super Admin)
- ✅ Download evidence (All roles)
- ✅ Preview evidence (All roles)
- ✅ Upload new version (Assessor, Manager, Admin, Super Admin)
- ✅ Delete evidence (Assessor, Manager, Admin, Super Admin)

---

### 📊 3. Reports Menu
**Route:** `/reports`  
**Access:** All roles (with view reports permission)

| Role | Can Access | Can Generate |
|------|-----------|--------------|
| Super Admin | ✅ Yes | All reports, all assessments |
| Admin | ✅ Yes | All reports, all assessments |
| Manager | ✅ Yes | Reports for own company |
| Assessor | ✅ Yes | Reports for own assessments |
| Viewer | ✅ Yes | Reports for authorized assessments |

**Available Reports:**
- ✅ Maturity Report (Radar Chart)
- ✅ Gap Analysis Report (Bar Chart)
- ✅ Summary Report
- ✅ PDF Export
- ✅ Excel Export

---

### ✅ 4. Review & Approval Menu (NEW)
**Access:** Super Admin, Admin, Manager

#### 4.1 Pending Review
**Route:** `/review-approval/pending-review`  
**Access:** Admin, Manager

| Role | Can Access | Scope |
|------|-----------|-------|
| Super Admin | ✅ Yes | All companies |
| Admin | ✅ Yes | All companies |
| Manager | ✅ Yes | Own company only |
| Assessor | ❌ No | - |
| Viewer | ❌ No | - |

**Features:**
- View assessments with status "completed"
- Search & filter
- Statistics (total pending, this month, overdue)
- Review form access
- Decision: Approve → Reviewed / Reject → Draft / Request Revision → In Progress

**Badge Notification:** Shows count of pending reviews in menu

#### 4.2 Pending Approval
**Route:** `/review-approval/pending-approval`  
**Access:** Super Admin only

| Role | Can Access | Scope |
|------|-----------|-------|
| Super Admin | ✅ Yes | All companies |
| Admin | ❌ No | - |
| Manager | ❌ No | - |
| Assessor | ❌ No | - |
| Viewer | ❌ No | - |

**Features:**
- View assessments with status "reviewed"
- Search & filter
- Statistics
- Approval form access
- Decision: Approve → Approved / Reject → Draft / Request Re-review → Completed

**Badge Notification:** Shows count of pending approvals in menu

#### 4.3 Review History
**Route:** `/review-approval/{id}/history`  
**Access:** All roles (for accessible assessments)

| Role | Can Access |
|------|-----------|
| All Roles | ✅ Yes (for assessments they can access) |

**Features:**
- View review/approval timeline
- Activity logs
- Status changes
- Reviewer/Approver information

---

### ❓ 5. Questions Menu (NEW)
**Route:** `/questions`  
**Access:** Super Admin, Admin

| Role | Can Access | Can Do |
|------|-----------|--------|
| Super Admin | ✅ Yes | Full CRUD, bulk import |
| Admin | ✅ Yes | Full CRUD, bulk import |
| Manager | ❌ No | - |
| Assessor | ❌ No | - |
| Viewer | ❌ No | - |

**Features:**
- ✅ View all questions
- ✅ Create new question
- ✅ Edit question
- ✅ Delete question
- ✅ Bulk import from CSV
- ✅ Filter by GAMO, category, maturity level
- ✅ Search by code, text
- ✅ Toggle active/inactive status

---

### ⚙️ 6. Administration Menu
**Access:** Super Admin, Admin

#### 6.1 Users Management
**Route:** `/admin/users`  
**Access:** Super Admin, Admin

| Role | Can Access | Can Do |
|------|-----------|--------|
| Super Admin | ✅ Yes | Full CRUD, assign roles |
| Admin | ✅ Yes | Full CRUD, assign roles (except Super Admin) |
| Others | ❌ No | - |

#### 6.2 Roles & Permissions
**Route:** `/admin/roles`  
**Access:** Super Admin, Admin

| Role | Can Access | Can Do |
|------|-----------|--------|
| Super Admin | ✅ Yes | Manage all roles & permissions |
| Admin | ✅ Yes | View roles & permissions |
| Others | ❌ No | - |

#### 6.3 Audit Logs
**Route:** `/admin/audit-logs`  
**Access:** Super Admin, Admin

| Role | Can Access | Can See |
|------|-----------|---------|
| Super Admin | ✅ Yes | All activity logs |
| Admin | ✅ Yes | All activity logs |
| Others | ❌ No | - |

#### 6.4 System Settings
**Route:** `/admin/settings`  
**Access:** Super Admin, Admin

| Role | Can Access | Can Do |
|------|-----------|--------|
| Super Admin | ✅ Yes | Modify all settings |
| Admin | ✅ Yes | View & modify settings |
| Others | ❌ No | - |

---

### 💾 7. Master Data Menu
**Access:** Super Admin only

#### 7.1 Companies
**Route:** `/master-data/companies`  
**Access:** Super Admin

| Role | Can Access | Can Do |
|------|-----------|--------|
| Super Admin | ✅ Yes | Full CRUD |
| Others | ❌ No | - |

#### 7.2 Design Factors
**Route:** `/master-data/design-factors`  
**Access:** Super Admin

| Role | Can Access | Can Do |
|------|-----------|--------|
| Super Admin | ✅ Yes | Full CRUD |
| Others | ❌ No | - |

#### 7.3 GAMO Objectives
**Route:** `/master-data/gamo-objectives`  
**Access:** Super Admin

| Role | Can Access | Can Do |
|------|-----------|--------|
| Super Admin | ✅ Yes | Full CRUD |
| Others | ❌ No | - |

---

### 👤 8. User Profile Menu
**Access:** All authenticated users

#### 8.1 Profile
**Route:** `/profile`  
**Access:** All roles

| Role | Can Access | Can Do |
|------|-----------|--------|
| All | ✅ Yes | View & edit own profile |

#### 8.2 Settings
**Route:** `/profile/settings`  
**Access:** All roles

| Role | Can Access | Can Do |
|------|-----------|--------|
| All | ✅ Yes | Change password, preferences |

---

## 🎯 PERMISSION SUMMARY BY ROLE

### Super Admin (Full Access)
✅ Dashboard  
✅ All Assessments (all companies)  
✅ Create Assessment  
✅ My Assessments  
✅ Assessment Taking  
✅ Evidence Management  
✅ Reports (all)  
✅ Pending Review  
✅ Pending Approval  
✅ Review History  
✅ Questions Management  
✅ Users Management  
✅ Roles & Permissions  
✅ Audit Logs  
✅ System Settings  
✅ Master Data (Companies, Design Factors, GAMO)  
✅ Profile & Settings  

### Admin
✅ Dashboard  
✅ All Assessments (all companies)  
✅ Create Assessment  
✅ My Assessments  
✅ Assessment Taking  
✅ Evidence Management  
✅ Reports (all)  
✅ Pending Review  
❌ Pending Approval (Super Admin only)  
✅ Review History  
✅ Questions Management  
✅ Users Management  
✅ Roles & Permissions (view only)  
✅ Audit Logs  
✅ System Settings  
❌ Master Data  
✅ Profile & Settings  

### Manager
✅ Dashboard (own company)  
✅ All Assessments (own company)  
✅ Create Assessment  
✅ My Assessments  
✅ Assessment Taking  
✅ Evidence Management  
✅ Reports (own company)  
✅ Pending Review (own company)  
❌ Pending Approval  
✅ Review History  
❌ Questions Management  
❌ Users Management  
❌ Roles & Permissions  
❌ Audit Logs  
❌ System Settings  
❌ Master Data  
✅ Profile & Settings  

### Assessor
✅ Dashboard (personal)  
✅ All Assessments (own/participated)  
✅ Create Assessment  
✅ My Assessments  
✅ Assessment Taking  
✅ Evidence Management  
✅ Reports (own assessments)  
❌ Pending Review  
❌ Pending Approval  
✅ Review History (own assessments)  
❌ Questions Management  
❌ Users Management  
❌ Roles & Permissions  
❌ Audit Logs  
❌ System Settings  
❌ Master Data  
✅ Profile & Settings  

### Viewer
✅ Dashboard (read-only)  
✅ All Assessments (authorized, read-only)  
❌ Create Assessment  
✅ My Assessments (read-only)  
❌ Assessment Taking (read-only)  
✅ Evidence Management (view & download only)  
✅ Reports (authorized assessments)  
❌ Pending Review  
❌ Pending Approval  
✅ Review History (authorized assessments)  
❌ Questions Management  
❌ Users Management  
❌ Roles & Permissions  
❌ Audit Logs  
❌ System Settings  
❌ Master Data  
✅ Profile & Settings  

---

## 🔔 NOTIFICATION BADGES IN MENU

### Review & Approval Menu
**For Admin/Manager:**
- Badge menampilkan jumlah assessments dengan status "completed" yang menunggu review
- Badge berwarna orange/warning
- Update otomatis setiap page load

**For Super Admin:**
- Badge menampilkan jumlah assessments dengan status "reviewed" yang menunggu approval
- Badge berwarna red/danger
- Update otomatis setiap page load

**Contoh:**
```
Review & Approval
├── Pending Review [5]  ← Orange badge untuk Admin/Manager
├── Pending Approval [2]  ← Red badge untuk Super Admin
```

---

## 🚀 MENU NAVIGATION UPDATES COMPLETED

### Changes Made:
✅ Added "Review & Approval" dropdown menu  
✅ Added "Pending Review" menu item (Admin/Manager)  
✅ Added "Pending Approval" menu item (Super Admin)  
✅ Added badge notifications with counts  
✅ Added "Questions" menu item (Super Admin/Admin)  
✅ Applied proper role-based access control  
✅ Added active state highlighting  
✅ Added icons for all menu items  

### Files Modified:
📝 `/resources/views/layouts/partials/navbar.blade.php`

---

## ✅ VERIFICATION CHECKLIST

- [x] Dashboard accessible to all roles
- [x] Assessments menu with proper role filtering
- [x] Create assessment limited to authorized roles
- [x] Evidence management accessible
- [x] Reports accessible to all roles
- [x] Review & Approval menu for Admin/Manager/Super Admin
- [x] Badge notifications working
- [x] Questions menu for Super Admin/Admin
- [x] Administration menu for Super Admin/Admin
- [x] Master Data menu for Super Admin only
- [x] Profile accessible to all users
- [x] Role-based menu visibility
- [x] Active state highlighting
- [x] Mobile responsive menu

---

## 📊 MENU ITEM COUNT BY ROLE

| Role | Menu Items | Dropdown Menus |
|------|-----------|----------------|
| Super Admin | 9 | 5 |
| Admin | 7 | 4 |
| Manager | 5 | 2 |
| Assessor | 3 | 1 |
| Viewer | 3 | 1 |

---

## 🎨 MENU ICONS REFERENCE

| Menu Item | Icon | Color Context |
|-----------|------|---------------|
| Dashboard | `ti-home` | Default |
| Assessments | `ti-clipboard-check` | Default |
| Reports | `ti-chart-bar` | Default |
| Review & Approval | `ti-checklist` | Default |
| - Pending Review | `ti-eye-check` | Orange badge |
| - Pending Approval | `ti-circle-check` | Red badge |
| Questions | `ti-help` | Default |
| Administration | `ti-settings` | Default |
| - Users | `ti-users` | Default |
| - Roles | `ti-shield-lock` | Default |
| - Audit Logs | `ti-file-search` | Default |
| - Settings | `ti-adjustments` | Default |
| Master Data | `ti-database` | Default |
| - Companies | `ti-building` | Default |
| - Design Factors | `ti-puzzle` | Default |
| - GAMO | `ti-target` | Default |

---

## 🔐 SECURITY NOTES

1. **Middleware Protection:** All routes protected by appropriate middleware
2. **Role Verification:** Menu items conditionally rendered based on user roles
3. **Authorization Checks:** Controller-level authorization for all actions
4. **Company Isolation:** Managers can only access own company data
5. **Audit Trail:** All actions logged in audit_logs table
6. **Access Logging:** Evidence access tracked in evidence_access_logs

---

## 📝 IMPLEMENTATION STATUS

**Menu Structure:** ✅ 100% Complete  
**Role-Based Access:** ✅ 100% Complete  
**Badge Notifications:** ✅ 100% Complete  
**Mobile Responsive:** ✅ 100% Complete  
**Icons & Styling:** ✅ 100% Complete  

**Total Menu Items:** 26  
**Role Variations:** 5  
**Protected Routes:** All  

---

**Last Updated:** 7 Januari 2026  
**Status:** ✅ Production Ready  
**Tested:** Role-based access verified
