# Missing Backend Endpoints - To Be Implemented

## Overview
The frontend has been fully implemented, but several AJAX endpoints called by the JavaScript need to be implemented in the backend controller.

## Status: Already Implemented ✅

These endpoints were created in the initial backend implementation phase:

1. ✅ **GET** `/assessments/{assessment}/gamo/{gamo}/activities`
   - Controller: `AssessmentTakingController::getActivitiesByLevel()`
   - Returns: Activities grouped by level with answers and evidence count

2. ✅ **POST** `/assessments/{assessment}/activity/{activity}/answer`
   - Controller: `AssessmentTakingController::saveActivityAnswer()`
   - Accepts: capability_rating, notes
   - Returns: Success message, updated answer

3. ✅ **GET** `/assessments/{assessment}/gamo/{gamo}/history`
   - Controller: `AssessmentTakingController::getHistoryLog()`
   - Returns: Audit log entries with changes

4. ✅ **GET** `/assessments/{assessment}/gamo/{gamo}/average-score`
   - Controller: `AssessmentTakingController::getAverageScore()`
   - Returns: Compliance scores per level and overall

5. ✅ **GET** `/assessments/{assessment}/gamo/{gamo}/notes`
   - Controller: `AssessmentTakingController::getNotesList()`
   - Returns: Notes list with activity info

6. ✅ **GET** `/assessments/{assessment}/activity/{activity}/evidence`
   - Controller: `AssessmentTakingController::getEvidenceList()`
   - Returns: Evidence files for activity

7. ✅ **POST** `/assessments/{assessment}/activity/{activity}/evidence`
   - Controller: `AssessmentTakingController::uploadEvidence()`
   - Accepts: file or URL, description
   - Returns: Success message

## Status: Need to Be Implemented ❌

### 1. Get Single Activity Details ❌
**Endpoint**: `GET /assessments/{assessment}/activity/{activity}`

**Purpose**: Load activity details for penilaian modal

**Controller Method Signature**:
```php
public function getActivityDetails(Assessment $assessment, GamoQuestion $activity)
{
    // Implementation needed
}
```

**Expected Response**:
```json
{
    "id": 1,
    "code": "EDM01.01",
    "name": "Ensure Governance Framework...",
    "translated_text": "Pastikan Kerangka Tata Kelola...",
    "level": 2,
    "weight": 1,
    "document_requirement": "Governance Charter",
    "evidence_count": 3,
    "answer": {
        "capability_rating": "L",
        "capability_score": 0.67,
        "notes": "Implemented partially..."
    }
}
```

**Used By**:
- Modal: penilaian-kapabilitas.blade.php
- Function: `openAssessmentModal(activityId)`

---

### 2. Get All Evidence for GAMO ❌
**Endpoint**: `GET /assessments/{assessment}/gamo/{gamo}/evidence`

**Purpose**: Load all evidence files for repository tab

**Controller Method Signature**:
```php
public function getGamoEvidence(Assessment $assessment, GamoObjective $gamo, Request $request)
{
    // Filters: level, type, search
    // Implementation needed
}
```

**Query Parameters**:
- `level` (optional): Filter by level (2-5)
- `type` (optional): Filter by type (file/url)
- `search` (optional): Search in description/filename

**Expected Response**:
```json
{
    "evidence": [
        {
            "id": 1,
            "description": "Governance Document",
            "file_path": "/storage/evidence/governance.pdf",
            "url": null,
            "file_size": "2.5 MB",
            "activity_code": "EDM01.01",
            "activity_name": "Ensure Governance...",
            "uploaded_date": "2026-01-08",
            "uploaded_by": "John Doe"
        }
    ],
    "total": 10
}
```

**Used By**:
- Tab: tab-repository.blade.php
- Function: `loadRepositoryEvidence()`

---

### 3. Delete Evidence ❌
**Endpoint**: `DELETE /assessments/{assessment}/evidence/{evidence}`

**Purpose**: Delete evidence file

**Controller Method Signature**:
```php
public function deleteEvidence(Assessment $assessment, AssessmentEvidence $evidence)
{
    // Authorization check
    // Delete file from storage
    // Delete database record
    // Implementation needed
}
```

**Expected Response**:
```json
{
    "success": true,
    "message": "Evidence berhasil dihapus"
}
```

**Used By**:
- Tab: tab-repository.blade.php
- Modal: daftar-evidence.blade.php
- Function: `deleteEvidence(evidenceId)`, `deleteEvidenceFromModal(evidenceId)`

---

### 4. Get Summary Data ❌
**Endpoint**: `GET /assessments/{assessment}/gamo/{gamo}/summary`

**Purpose**: Get summary statistics for Summary tab - Penilaian sub-tab

**Controller Method Signature**:
```php
public function getSummary(Assessment $assessment, GamoObjective $gamo)
{
    // Implementation needed
}
```

**Expected Response**:
```json
{
    "total_activities": 40,
    "total_assessed": 25,
    "overall_compliance": 0.65,
    "levels": {
        "2": {
            "total": 10,
            "assessed": 8,
            "not_assessed": 2,
            "na": 1,
            "n": 2,
            "p": 2,
            "l": 2,
            "f": 1,
            "compliance": 0.55
        },
        "3": { "...": "..." },
        "4": { "...": "..." },
        "5": { "...": "..." }
    }
}
```

**Used By**:
- Tab: tab-summary.blade.php (Penilaian sub-tab)
- Function: `loadSummaryPenilaian()`

---

### 5. Get Progress Data ❌
**Endpoint**: `GET /assessments/{assessment}/gamo/{gamo}/progress`

**Purpose**: Get progress statistics for Summary tab - Progress Kapabilitas sub-tab

**Controller Method Signature**:
```php
public function getProgress(Assessment $assessment, GamoObjective $gamo)
{
    // Calculate current capability level
    // Calculate progress percentage
    // Implementation needed
}
```

**Expected Response**:
```json
{
    "current_level": "Level 3",
    "target_level": 5,
    "overall_progress": 0.60,
    "levels": {
        "2": {
            "description": "Managed",
            "compliance": 0.85,
            "status": "achieved"
        },
        "3": {
            "description": "Established",
            "compliance": 0.65,
            "status": "in_progress"
        },
        "4": {
            "description": "Predictable",
            "compliance": 0.30,
            "status": "not_started"
        },
        "5": {
            "description": "Optimizing",
            "compliance": 0.00,
            "status": "not_started"
        }
    }
}
```

**Used By**:
- Tab: tab-summary.blade.php (Progress Kapabilitas sub-tab)
- Function: `loadProgressCapabilitas()`

---

### 6. Export Summary ❌
**Endpoint**: `GET /assessments/{assessment}/gamo/{gamo}/export-summary`

**Purpose**: Export summary as PDF or Excel

**Controller Method Signature**:
```php
public function exportSummary(Assessment $assessment, GamoObjective $gamo, Request $request)
{
    // Format: pdf or excel
    // Generate export file
    // Implementation needed
}
```

**Query Parameters**:
- `format` (optional): pdf or excel (default: pdf)

**Expected Response**:
- File download (PDF or Excel)

**Used By**:
- Tab: tab-summary.blade.php
- Function: `exportSummary()`

---

### 7. Export History ❌
**Endpoint**: `GET /assessments/{assessment}/gamo/{gamo}/export-history`

**Purpose**: Export audit log history as PDF or Excel

**Controller Method Signature**:
```php
public function exportHistory(Assessment $assessment, GamoObjective $gamo, Request $request)
{
    // Format: pdf or excel
    // Generate export file
    // Implementation needed
}
```

**Query Parameters**:
- `format` (optional): pdf or excel
- `action` (optional): Filter by action type
- `date` (optional): Filter by date

**Expected Response**:
- File download (PDF or Excel)

**Used By**:
- Modal: history-perubahan.blade.php
- Function: `exportHistory()`

---

### 8. Export Notes ❌
**Endpoint**: `GET /assessments/{assessment}/gamo/{gamo}/export-notes`

**Purpose**: Export notes list as PDF or Excel

**Controller Method Signature**:
```php
public function exportNotes(Assessment $assessment, GamoObjective $gamo, Request $request)
{
    // Format: pdf or excel
    // Generate export file
    // Implementation needed
}
```

**Query Parameters**:
- `format` (optional): pdf or excel
- `level` (optional): Filter by level

**Expected Response**:
- File download (PDF or Excel)

**Used By**:
- Modal: daftar-catatan.blade.php
- Function: `exportNotes()`

---

## Implementation Priority

### High Priority (Core Functionality) 🔴
1. ❌ Get Single Activity Details - Required for rating modal
2. ❌ Delete Evidence - Required for evidence management
3. ❌ Get All Evidence for GAMO - Required for repository tab

### Medium Priority (Analytics) 🟡
4. ❌ Get Summary Data - Required for summary tab
5. ❌ Get Progress Data - Required for progress visualization

### Low Priority (Export Features) 🟢
6. ❌ Export Summary - Nice to have
7. ❌ Export History - Nice to have
8. ❌ Export Notes - Nice to have

## Implementation Steps

### Step 1: Add Missing Routes
Edit `routes/web.php`:
```php
// Inside assessments prefix group
Route::get('/{assessment}/activity/{activity}', [AssessmentTakingController::class, 'getActivityDetails'])->name('activity-details');
Route::get('/{assessment}/gamo/{gamo}/evidence', [AssessmentTakingController::class, 'getGamoEvidence'])->name('gamo-evidence');
Route::delete('/{assessment}/evidence/{evidence}', [AssessmentTakingController::class, 'deleteEvidence'])->name('delete-evidence');
Route::get('/{assessment}/gamo/{gamo}/summary', [AssessmentTakingController::class, 'getSummary'])->name('summary');
Route::get('/{assessment}/gamo/{gamo}/progress', [AssessmentTakingController::class, 'getProgress'])->name('progress');
Route::get('/{assessment}/gamo/{gamo}/export-summary', [AssessmentTakingController::class, 'exportSummary'])->name('export-summary');
Route::get('/{assessment}/gamo/{gamo}/export-history', [AssessmentTakingController::class, 'exportHistory'])->name('export-history');
Route::get('/{assessment}/gamo/{gamo}/export-notes', [AssessmentTakingController::class, 'exportNotes'])->name('export-notes');
```

### Step 2: Implement Controller Methods
Edit `app/Http/Controllers/Web/AssessmentTakingController.php`:

1. Add methods following existing patterns
2. Use policy authorization: `$this->authorize('take-assessment', $assessment)`
3. Return JSON responses for AJAX calls
4. Use Laravel's file download response for exports

### Step 3: Test Each Endpoint
Use testing guide: FRONTEND_TESTING_GUIDE.md

### Step 4: Update Documentation
Mark endpoints as implemented in this file.

## Code Templates

### Template: Get Method
```php
public function getActivityDetails(Assessment $assessment, GamoQuestion $activity)
{
    $this->authorize('take-assessment', $assessment);
    
    $activityData = $activity->load([
        'gamoObjective',
        'answers' => function($query) use ($assessment) {
            $query->where('assessment_id', $assessment->id);
        }
    ]);
    
    $answer = $activityData->answers->first();
    $evidenceCount = AssessmentEvidence::where('assessment_id', $assessment->id)
        ->where('activity_id', $activity->id)
        ->count();
    
    return response()->json([
        'id' => $activity->id,
        'code' => $activity->question_code,
        'name' => $activity->question_text,
        'translated_text' => $activity->translated_text,
        'level' => $activity->maturity_level,
        'weight' => $activity->weight ?? 1,
        'document_requirement' => $activity->document_requirement,
        'evidence_count' => $evidenceCount,
        'answer' => $answer ? [
            'capability_rating' => $answer->capability_rating,
            'capability_score' => $answer->capability_score,
            'notes' => $answer->notes,
        ] : null
    ]);
}
```

### Template: Delete Method
```php
public function deleteEvidence(Assessment $assessment, AssessmentEvidence $evidence)
{
    $this->authorize('take-assessment', $assessment);
    
    // Verify evidence belongs to this assessment
    if ($evidence->assessment_id !== $assessment->id) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }
    
    // Delete file from storage if exists
    if ($evidence->file_path && Storage::exists($evidence->file_path)) {
        Storage::delete($evidence->file_path);
    }
    
    // Delete record
    $evidence->delete();
    
    // Log the deletion
    AssessmentAuditLog::logChange(
        $assessment->id,
        'evidence_deleted',
        'Deleted evidence: ' . ($evidence->description ?? 'Unnamed'),
        null,
        null
    );
    
    return response()->json([
        'success' => true,
        'message' => 'Evidence berhasil dihapus'
    ]);
}
```

### Template: Export Method
```php
use App\Exports\AssessmentSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

public function exportSummary(Assessment $assessment, GamoObjective $gamo, Request $request)
{
    $this->authorize('take-assessment', $assessment);
    
    $format = $request->get('format', 'pdf');
    
    // Get summary data
    $summaryData = $this->getSummaryData($assessment, $gamo);
    
    if ($format === 'excel') {
        return Excel::download(
            new AssessmentSummaryExport($summaryData),
            "summary_{$assessment->code}_{$gamo->code}.xlsx"
        );
    }
    
    // PDF export
    $pdf = Pdf::loadView('exports.summary-pdf', [
        'assessment' => $assessment,
        'gamo' => $gamo,
        'data' => $summaryData
    ]);
    
    return $pdf->download("summary_{$assessment->code}_{$gamo->code}.pdf");
}
```

## Dependencies Required

### For Excel Export
```bash
composer require maatwebsite/excel
```

### For PDF Export
Already installed:
- barryvdh/laravel-dompdf

## Testing Commands

### Test Endpoint with CURL
```bash
# Get activity details
curl -X GET "http://localhost/assessments/1/activity/1" \
  -H "Accept: application/json"

# Delete evidence
curl -X DELETE "http://localhost/assessments/1/evidence/1" \
  -H "X-CSRF-TOKEN: {token}"

# Export summary
curl -X GET "http://localhost/assessments/1/gamo/1/export-summary?format=pdf" \
  --output summary.pdf
```

## Estimated Implementation Time

- High Priority (1-3): **4-6 hours**
- Medium Priority (4-5): **3-4 hours**
- Low Priority (6-8): **4-6 hours**
- **Total**: 11-16 hours

## Notes

- All endpoints should use policy authorization
- Return proper HTTP status codes (200, 404, 403, 422, 500)
- Use Laravel's validation for request data
- Log important actions (delete, export)
- Handle errors gracefully with try-catch
- Use database transactions where needed
- Follow existing code patterns in controller

---
*Status: 7 of 15 endpoints implemented (46.7%)*
*Last Updated: January 8, 2026*
