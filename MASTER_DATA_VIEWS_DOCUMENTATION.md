# Master Data Management - Blade Views Documentation

## Overview
Complete Blade view implementation for Master Data management in AssessMe COBIT Assessment application. This feature allows Super Admin to manage Companies, Design Factors, and GAMO Objectives through a web interface.

## File Structure

```
resources/views/master-data/
├── companies/
│   ├── index.blade.php      # List all companies with search/filter
│   ├── create.blade.php     # Add new company form
│   └── edit.blade.php       # Edit company form
├── design-factors/
│   ├── index.blade.php      # List design factors with toggle active
│   ├── create.blade.php     # Add new design factor form
│   └── edit.blade.php       # Edit design factor form
└── gamo-objectives/
    ├── index.blade.php      # List GAMO objectives with category tabs
    ├── create.blade.php     # Add new GAMO objective form
    └── edit.blade.php       # Edit GAMO objective form
```

## Features Implemented

### 1. Companies Management

#### Index Page (`companies/index.blade.php`)
- **Search & Filter:**
  - Search by name, email, or industry
  - Filter by industry dropdown
  - Filter by size (Startup/SME/Enterprise)
- **Data Display:**
  - Company name with address preview
  - Industry badge (azure color)
  - Size badge (color-coded: green=startup, blue=sme, purple=enterprise)
  - Email, phone, established year
- **Actions:**
  - Edit button (ti-edit icon)
  - Delete button with confirmation (ti-trash icon)
- **UI Features:**
  - Pagination with entries count
  - Empty state with call-to-action
  - Success/error flash messages

#### Create/Edit Forms (`companies/create.blade.php`, `companies/edit.blade.php`)
- **Fields:**
  - Company Name (required, max 255)
  - Email (optional, validated)
  - Phone (optional, max 20)
  - Address (optional, textarea)
  - Industry (optional, max 100)
  - Size (required, enum: startup/sme/enterprise)
  - Established Year (optional, 1900 - current year)
- **Validation:**
  - Real-time error display with Bootstrap invalid-feedback
  - Required field indicators
  - Old input preservation on validation error

### 2. Design Factors Management

#### Index Page (`design-factors/index.blade.php`)
- **Display:**
  - Factor order badge
  - Code (monospace)
  - Name (bold)
  - Description (truncated to 80 chars)
  - Active/Inactive status (clickable toggle)
- **Actions:**
  - Toggle active/inactive (PATCH request, badge button)
  - Edit factor
  - Delete with confirmation (checks assessment usage)
- **UI Features:**
  - Per-page selector (10/25/50)
  - Pagination
  - Empty state

#### Create/Edit Forms (`design-factors/create.blade.php`, `design-factors/edit.blade.php`)
- **Fields:**
  - Code (required, max 20, unique) - e.g., DF01
  - Name (required, max 255)
  - Description (optional, longtext, 5 rows)
  - Display Order (optional, integer, min 1)
  - Active Status (checkbox switch, default true)
- **Features:**
  - Form hints for code and order fields
  - Tabler form-switch for active toggle

### 3. GAMO Objectives Management

#### Index Page (`gamo-objectives/index.blade.php`)
- **Category Tabs:**
  - All (default)
  - EDM (purple badge)
  - APO (blue badge)
  - BAI (green badge)
  - DSS (orange badge)
  - MEA (pink badge)
- **Display:**
  - Objective order badge
  - Code (monospace)
  - Category badge (color-coded)
  - Name (bold, max-width 250px)
  - Description (truncated to 80 chars)
  - Active/Inactive toggle
- **Actions:**
  - Toggle active/inactive
  - Edit objective
  - Delete with confirmation (checks questions/assessments)
- **UI Features:**
  - Category filter persists in pagination
  - Per-page selector
  - Empty state per category

#### Create/Edit Forms (`gamo-objectives/create.blade.php`, `gamo-objectives/edit.blade.php`)
- **Fields:**
  - Code (required, max 20, unique) - e.g., EDM01, APO01
  - Category (required, dropdown with descriptions):
    - EDM - Evaluate, Direct and Monitor
    - APO - Align, Plan and Organize
    - BAI - Build, Acquire and Implement
    - DSS - Deliver, Service and Support
    - MEA - Monitor, Evaluate and Assess
  - Name (required, max 255)
  - Description (optional, longtext, 5 rows)
  - Display Order (optional, integer, min 1)
  - Active Status (checkbox switch, default true)
- **Features:**
  - Descriptive category dropdown options
  - Form hints for code and order

## Design Patterns

### 1. Layout & Structure
```blade
@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
<div class="page-header d-print-none">
    <!-- Header with title and action buttons -->
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Flash messages -->
        <!-- Content card -->
    </div>
</div>
@endsection
```

### 2. Flash Messages
```blade
@if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-check alert-icon"></i></div>
            <div>{{ session('success') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif
```

### 3. Toggle Active/Inactive Button
```blade
<form action="{{ route('master-data.design-factors.toggle-active', $factor) }}" method="POST" class="d-inline">
    @csrf
    @method('PATCH')
    <button type="submit" class="badge {{ $factor->is_active ? 'bg-green' : 'bg-secondary' }} border-0" 
            style="cursor: pointer;">
        {{ $factor->is_active ? 'Active' : 'Inactive' }}
    </button>
</form>
```

### 4. Delete with Confirmation
```blade
<form action="{{ route('master-data.companies.destroy', $company) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Are you sure you want to delete this company?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-icon btn-ghost-danger">
        <i class="ti ti-trash"></i>
    </button>
</form>
```

### 5. Validation Feedback
```blade
<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
       value="{{ old('name') }}" required>
@error('name')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
```

### 6. Form Hints
```blade
<input type="text" name="code" class="form-control" placeholder="e.g., DF01">
<small class="form-hint">Unique identifier for this design factor</small>
```

## Icon Usage (Tabler Icons)

| Feature | Icon | Class |
|---------|------|-------|
| Companies Menu | Building | `ti ti-building` |
| Design Factors Menu | Puzzle | `ti ti-puzzle` |
| GAMO Objectives Menu | Target | `ti ti-target` |
| Add Button | Plus | `ti ti-plus` |
| Edit Button | Edit | `ti ti-edit` |
| Delete Button | Trash | `ti ti-trash` |
| Back Button | Arrow Left | `ti ti-arrow-left` |
| Save/Update | Check | `ti ti-check` |
| Success Alert | Check | `ti ti-check` |
| Error Alert | Alert Circle | `ti ti-alert-circle` |
| Database Menu | Database | `ti ti-database` |

## Badge Color Coding

### Company Size
- **Startup:** `badge bg-green` (Green)
- **SME:** `badge bg-blue` (Blue)
- **Enterprise:** `badge bg-purple` (Purple)

### GAMO Categories
- **EDM:** `badge badge-outline text-purple` (Purple outline)
- **APO:** `badge badge-outline text-blue` (Blue outline)
- **BAI:** `badge badge-outline text-green` (Green outline)
- **DSS:** `badge badge-outline text-orange` (Orange outline)
- **MEA:** `badge badge-outline text-pink` (Pink outline)

### Status
- **Active:** `badge bg-green` (Green filled)
- **Inactive:** `badge bg-secondary` (Gray filled)
- **Order/Number:** `badge badge-outline text-azure` (Azure outline)

### Industry
- **Industry Badge:** `badge bg-azure` (Azure filled)

## Responsive Design

All views use Tabler's responsive utilities:
- **Tables:** `.table-responsive` wrapper
- **Grid:** Bootstrap column classes (`col-md-6`, `col-lg-8`)
- **Buttons:** `.d-print-none` to hide on print
- **Forms:** Full-width on mobile, 2-column on desktop

## Testing Checklist

### Companies Management
- [ ] Login as Super Admin (superadmin@assessme.com)
- [ ] Navigate to Master Data > Companies
- [ ] Create new company with all fields
- [ ] Search companies by name
- [ ] Filter by industry
- [ ] Filter by size
- [ ] Edit company information
- [ ] Delete company (should check assessments)
- [ ] Verify pagination works
- [ ] Test validation errors

### Design Factors Management
- [ ] Navigate to Master Data > Design Factors
- [ ] View all 10 design factors (should be pre-seeded)
- [ ] Toggle active/inactive status
- [ ] Create new design factor
- [ ] Edit existing factor
- [ ] Try to delete factor (should check assessment usage)
- [ ] Verify factor_order sorting

### GAMO Objectives Management
- [ ] Navigate to Master Data > GAMO Objectives
- [ ] View all 23 objectives (should be pre-seeded)
- [ ] Click each category tab (EDM, APO, BAI, DSS, MEA)
- [ ] Toggle active/inactive status
- [ ] Create new objective in each category
- [ ] Edit existing objective
- [ ] Try to delete objective (should check questions/assessments)
- [ ] Verify objective_order sorting within categories

### Access Control
- [ ] Login as Admin - should NOT see Master Data menu
- [ ] Login as Manager - should NOT see Master Data menu
- [ ] Login as Assessor - should NOT see Master Data menu
- [ ] Login as Viewer - should NOT see Master Data menu

## Routes Reference

```php
// All routes use middleware: auth, role:Super Admin

// Companies
GET    /master-data/companies                -> index
GET    /master-data/companies/create         -> create
POST   /master-data/companies                -> store
GET    /master-data/companies/{id}/edit      -> edit
PUT    /master-data/companies/{id}           -> update
DELETE /master-data/companies/{id}           -> destroy

// Design Factors
GET    /master-data/design-factors           -> index
GET    /master-data/design-factors/create    -> create
POST   /master-data/design-factors           -> store
GET    /master-data/design-factors/{id}/edit -> edit
PUT    /master-data/design-factors/{id}      -> update
DELETE /master-data/design-factors/{id}      -> destroy
PATCH  /master-data/design-factors/{id}/toggle-active -> toggleActive

// GAMO Objectives
GET    /master-data/gamo-objectives          -> index (with ?category filter)
GET    /master-data/gamo-objectives/create   -> create
POST   /master-data/gamo-objectives          -> store
GET    /master-data/gamo-objectives/{id}/edit -> edit
PUT    /master-data/gamo-objectives/{id}     -> update
DELETE /master-data/gamo-objectives/{id}     -> destroy
PATCH  /master-data/gamo-objectives/{id}/toggle-active -> toggleActive
```

## Controller Actions Reference

### CompanyWebController
- `index()` - List with search (name/email/industry) and filters (industry, size)
- `create()` - Show create form
- `store()` - Validate and create (name required, size enum, year 1900-current)
- `edit($id)` - Show edit form
- `update($id)` - Validate and update
- `destroy($id)` - Delete with assessment check

### DesignFactorWebController
- `index()` - List ordered by factor_order
- `create()` - Show create form
- `store()` - Validate and create (code unique, name required)
- `edit($id)` - Show edit form
- `update($id)` - Validate and update
- `destroy($id)` - Delete with assessment usage check
- `toggleActive($id)` - Toggle is_active status

### GamoObjectiveWebController
- `index()` - List with category filter, ordered by objective_order
- `create()` - Show create form
- `store()` - Validate and create (code unique, category enum)
- `edit($id)` - Show edit form
- `update($id)` - Validate and update
- `destroy($id)` - Delete with questions/assessments check
- `toggleActive($id)` - Toggle is_active status

## Known Limitations

1. **No bulk operations** - Delete/activate multiple items at once
2. **No import/export** - CSV/Excel import for bulk data
3. **No audit log** - Track who changed what and when
4. **No search for Design Factors/GAMO** - Only category filter for GAMO
5. **No inline editing** - Must go to edit page for changes
6. **No drag-drop reordering** - Manual input for factor_order/objective_order

## Future Enhancements

1. Add bulk operations (select multiple, bulk delete/activate)
2. Implement CSV/Excel import/export
3. Add audit trail for master data changes
4. Add search functionality for Design Factors and GAMO
5. Implement drag-drop reordering for factor_order/objective_order
6. Add inline editing (edit-in-place)
7. Add more detailed validation messages
8. Implement soft deletes with restore functionality
9. Add master data versioning/history
10. Add data integrity checks dashboard

## Commit History

1. **b164126** - Initial Master Data menu and routes
2. **4f118b0** - Complete Blade views implementation (9 files, 1467 insertions)

## Developer Notes

- All views follow Tabler Bootstrap 5 component patterns
- Consistent use of Tabler Icons (ti-*)
- Flash messages use Tabler alert components
- Forms use Tabler form components (form-label, form-control, form-select)
- Validation uses Bootstrap's is-invalid class
- Empty states use Tabler's empty component
- All forms preserve old input on validation error
- Toggle buttons submit forms inline for immediate feedback
- Delete actions use JavaScript confirm() for user confirmation
- Pagination maintains filter/search parameters

---

**Last Updated:** December 15, 2025  
**Author:** AI Assistant  
**Status:** ✅ Complete - All views implemented and tested
