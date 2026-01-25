# Authorization Guards Added - Answer Assessment Interface

## Summary
Added comprehensive role-based access control (RBAC) to answer-new interface following Laravel's policy-based authorization.

## Changes Made

### 1. Main View (answer-new.blade.php)
✅ Added readonly mode warning banner for users without answer permission
✅ Added "Read-Only" badge in page title
✅ Added JavaScript variable `canAnswer` for conditional rendering
✅ Disabled all form inputs for readonly users
✅ Conditional button display in activities table

### 2. Tab Level (tab-level.blade.php)
✅ Hidden "Lihat Daftar Catatan" menu item for readonly users
✅ Conditional action dropdown (rate/evidence buttons)
✅ Readonly users only see "View" button

### 3. Tab Repository (tab-repository.blade.php)
✅ Upload form hidden with `@can('answer', $assessment)`
✅ Delete evidence button hidden for readonly users
✅ Only view button available for viewers

### 4. Modal Penilaian (penilaian-kapabilitas.blade.php)
✅ Readonly info alert for users without permission
✅ Disabled rating radio buttons for readonly users
✅ Disabled notes textarea
✅ Save button replaced with "Read-Only Mode" disabled button

### 5. Modal Evidence (daftar-evidence.blade.php)
✅ Upload form hidden with `@can('answer', $assessment)`
✅ Delete button hidden for readonly users
✅ Only view action available

### 6. JavaScript Updates
✅ `canAnswer` variable exposed to all scripts
✅ Conditional rendering in `renderActivities()`
✅ Conditional delete buttons in evidence grids
✅ Form fields disabled on page load for readonly users

## Authorization Matrix

| Role | View | Answer/Rate | Upload Evidence | Delete Evidence | View History | Export |
|------|------|-------------|-----------------|-----------------|--------------|--------|
| **Super Admin** | ✅ All | ✅ All | ✅ Yes | ✅ Yes | ✅ Yes | ✅ Yes |
| **Admin** | ✅ All | ✅ Not locked | ✅ Yes | ✅ Yes | ✅ Yes | ✅ Yes |
| **Manager** | ✅ Own company | ✅ Own company | ✅ Yes | ✅ Yes | ✅ Yes | ✅ Yes |
| **Assessor** | ✅ Assigned | ✅ Assigned | ✅ Assigned | ✅ Assigned | ✅ Assigned | ❌ No |
| **Viewer** | ✅ Published | ❌ No | ❌ No | ❌ No | ✅ Yes | ❌ No |

## Policy Method Used

```php
// In AssessmentPolicy.php
public function answer(User $user, Assessment $assessment): bool
{
    // Super Admin & Admin: answer all (except approved/archived)
    // Manager: answer own company (not locked)
    // Assessor: answer assigned only (draft/in_progress)
    // Viewer: cannot answer
}

// Alias used in views
public function takeAssessment(User $user, Assessment $assessment): bool
{
    return $this->answer($user, $assessment);
}
```

## Usage in Blade

```blade
{{-- Check if user can answer --}}
@can('answer', $assessment)
    <!-- Show editable forms -->
@else
    <!-- Show readonly content --}}
@endcan

{{-- Or use @cannot --}}
@cannot('answer', $assessment)
    <div class="alert alert-warning">Read-Only Mode</div>
@endcannot
```

## JavaScript Variable

```javascript
// Set from Laravel
const canAnswer = {{ auth()->user()->can('answer', $assessment) ? 'true' : 'false' }};

// Exposed to global scope
window.canAnswer = canAnswer;

// Used in conditional rendering
if (canAnswer) {
    // Show edit buttons
} else {
    // Show view-only buttons
}
```

## Visual Indicators for Readonly Mode

1. **Warning Banner**: Yellow alert at top of page
2. **Badge**: "Read-Only" badge next to title
3. **Disabled Forms**: All inputs grayed out and disabled
4. **Hidden Buttons**: Upload, delete, save buttons hidden
5. **Info Alert**: Blue info box in modals
6. **Icon Change**: Lock icon on disabled buttons

## Testing Scenarios

### Test as Super Admin
- ✅ Should see all buttons (rate, upload, delete)
- ✅ Can submit all forms
- ✅ Can modify any assessment

### Test as Manager
- ✅ Can answer own company assessments
- ❌ Cannot answer other company assessments (readonly)
- ✅ Can upload/delete evidence for own assessments

### Test as Assessor
- ✅ Can answer assigned assessments
- ❌ Cannot answer unassigned (should not even see them)
- ✅ Can upload evidence for assigned

### Test as Viewer
- ❌ No edit buttons visible
- ❌ All forms disabled
- ✅ Can view data and history
- ✅ Warning banner displayed

### Test Locked Assessment (approved/archived)
- ❌ Even Admin cannot edit
- ✅ Readonly mode for all users

## Backend Protection (Already Exists)

```php
// In AssessmentTakingController
public function answerNew(Assessment $assessment)
{
    $this->authorize('take-assessment', $assessment); // ✅
}

public function saveActivityAnswer(Request $request, Assessment $assessment, GamoQuestion $activity)
{
    $this->authorize('take-assessment', $assessment); // ✅
}

public function uploadEvidence(Request $request, Assessment $assessment, GamoQuestion $activity)
{
    $this->authorize('take-assessment', $assessment); // ✅
}

public function deleteEvidence(Assessment $assessment, AssessmentEvidence $evidence)
{
    $this->authorize('take-assessment', $assessment); // ✅
}
```

## Security Benefits

1. **Defense in Depth**: Both frontend and backend protected
2. **Clear UX**: Users know immediately if they can edit
3. **No Wasted API Calls**: Forms hidden instead of failing on submit
4. **Consistent**: Follows same pattern as other views
5. **Professional**: Clear readonly indicators

## Files Modified

1. `resources/views/assessments/answer-new.blade.php`
2. `resources/views/assessments/partials/tab-level.blade.php`
3. `resources/views/assessments/partials/tab-repository.blade.php`
4. `resources/views/assessments/modals/penilaian-kapabilitas.blade.php`
5. `resources/views/assessments/modals/daftar-evidence.blade.php`

**Total Lines Changed**: ~150 lines across 5 files

## Status
✅ **Authorization guards fully implemented**
✅ **Tested with policy methods**
✅ **Consistent with existing views**
✅ **Ready for production**

---
*Updated: January 8, 2026*
