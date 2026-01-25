# Quick Testing Guide - Answer Assessment Interface

## Access URL
```
http://localhost/assessments/{assessment_id}/answer-new
```

## Testing Sequence

### 1. Initial Load Test
✅ **Expected**: Page loads with GAMO selector and 4 tabs
- GAMO dropdown populated
- "Level" tab active by default
- Level 2 card active
- Loading spinner in activities table

### 2. Test Tab Level (Main Features)

**Load Activities**:
- Click Level 2 card → Activities table populates
- Check columns: Urutan, Code, Activities, Translate, Evidence, Asesmen, Weight, Aksi
- Verify summary section shows: Values, Compliances

**Test Rating**:
1. Click "Rate" button or rating badge on any activity
2. Modal "Penilaian Kapabilitas" opens
3. Select rating (F/L/P/N/N/A)
4. Add notes (optional)
5. Click "Simpan Penilaian"
6. ✅ Success: Rating badge updated, summary recalculated

**Test Evidence**:
1. Click evidence count badge (or "-" if no evidence)
2. Modal "Daftar Evidence" opens
3. Shows existing evidence or "Belum ada evidence"

**Test Action Menu**:
1. Click 3-dot action menu in table header
2. Options: History, Average, Notes, Evidence
3. Each opens respective modal

### 3. Test Tab PBC

**Load Documents**:
- Switch to "PBC" tab
- Level 2 active by default
- Table shows: Urutan, Code, Kebutuhan Dokumen, Evidence, Status, Keterangan, Aksi

**Test Document Tracking**:
- Status badges: "Belum Dinilai" (secondary), "Ada Evidence" (info), "Selesai" (success)
- Click evidence button → Opens evidence modal
- Click penilaian button → Opens rating modal

**Switch Levels**:
- Click Level 3 card
- PBC table refreshes with Level 3 documents

### 4. Test Tab Repository

**Test Upload**:
1. Switch to "Repository" tab
2. Select activity from dropdown
3. Choose "Upload File" → Select file (max 10MB)
4. OR choose "URL Link" → Enter URL
5. Add description (optional)
6. Click "Upload"
7. ✅ Success: Evidence appears in grid

**Test Filters**:
- Level filter: Select "Level 3" → Grid updates
- Type filter: Select "File" → Shows only files
- Search box: Type keyword → Debounce 500ms, then filter
- Refresh button: Reload evidence

**Test Evidence Actions**:
- Click eye icon → Opens file/URL in new tab
- Click trash icon → Confirmation dialog → Delete

### 5. Test Tab Summary

**Sub-tab: Penilaian**:
- Switch to "Summary" tab
- "Penilaian" sub-tab active by default
- Check statistics cards:
  - Total Activities
  - Dinilai (count + percentage)
  - Belum Dinilai
  - Avg Compliance
- Summary table per level with rating counts
- Total row at bottom
- Export button

**Sub-tab: Progress Kapabilitas**:
- Click "Progress Kapabilitas" sub-tab
- Progress cards: Current Level, Target Level, Progress %
- Bar chart renders (Chart.js)
- Detailed progress table with status badges
- Progress bars per level

### 6. Test Modals

**Modal: Penilaian Kapabilitas**:
- Activity info displays (code, name, level, weight)
- 5 radio buttons with scores
- Notes textarea
- Evidence count link
- Save button → Returns to activities table

**Modal: History Perubahan**:
- Timeline view with action icons
- Filter by action type (Created/Updated/Deleted)
- Filter by date
- User and timestamp displayed
- Old value → New value changes
- Export button

**Modal: Nilai Rata-rata**:
- Large overall score card
- Level scores table with compliance
- Line chart renders
- Rating distribution (F/L/P/N/N/A counts)
- Print button

**Modal: Daftar Catatan**:
- Notes list with activity info
- Filter by level
- Search box
- Statistics: Total, With Rating, Without Rating
- View/Edit buttons → Open penilaian modal
- Export button

**Modal: Daftar Evidence**:
- Activity info (when filtered by activity)
- Upload form (file or URL)
- View mode toggle (List/Grid)
- Evidence list/grid displays
- View/Delete actions

## Browser Console Checks

Open Developer Tools (F12) and check:

### No Errors Expected
```
Console tab: No red errors
Network tab: All AJAX calls return 200 OK
```

### Expected API Calls
```
GET /assessments/{id}/gamo/{gamo}/activities?level=2
GET /assessments/{id}/gamo/{gamo}/history
GET /assessments/{id}/gamo/{gamo}/average-score
GET /assessments/{id}/gamo/{gamo}/notes
GET /assessments/{id}/activity/{activity}/evidence
POST /assessments/{id}/activity/{activity}/answer
POST /assessments/{id}/activity/{activity}/evidence
```

## Test Data Requirements

### Minimum Data Needed
1. **Assessment**: 1 active assessment
2. **GAMO Selection**: At least 1 GAMO selected for assessment
3. **Activities**: Questions for levels 2-5
4. **Answers**: Some activities rated (F/L/P/N/N/A)
5. **Evidence**: Some evidence files uploaded
6. **Notes**: Some notes added
7. **History**: Some audit log entries

### Sample Test Flow
```sql
-- 1. Create test assessment
INSERT INTO assessments (code, title, status) 
VALUES ('TEST-001', 'Test Assessment', 'in_progress');

-- 2. Select GAMO (EDM01)
INSERT INTO assessment_gamo_selections (assessment_id, gamo_objective_id)
VALUES (1, 1);

-- 3. Rate some activities
UPDATE assessment_answers 
SET capability_rating = 'L', capability_score = 0.67
WHERE assessment_id = 1 AND question_id IN (1, 2, 3);

-- 4. Upload evidence
INSERT INTO assessment_evidence (assessment_id, activity_id, file_path)
VALUES (1, 1, '/storage/evidence/test.pdf');
```

## Common Issues & Fixes

### Issue: Activities not loading
**Check**:
- GAMO selected for assessment?
- Questions exist for selected GAMO?
- Console shows 200 OK response?

**Fix**: Verify `assessment_gamo_selections` table has records

### Issue: Rating modal empty
**Check**:
- Activity ID valid?
- AJAX endpoint returns activity data?

**Fix**: Ensure `getActivity()` endpoint implemented

### Issue: Evidence upload fails
**Check**:
- File size < 10MB?
- File type allowed (.pdf, .doc, .xls, .png, .jpg)?
- CSRF token present?

**Fix**: Check storage permissions, validate file in backend

### Issue: Charts not rendering
**Check**:
- Chart.js loaded?
- Canvas element exists?
- Data array not empty?

**Fix**: Include Chart.js CDN in layout

### Issue: Summary shows 0.00
**Check**:
- Activities rated?
- Compliance calculation logic correct?

**Fix**: Rate at least 1 activity per level

## Performance Benchmarks

### Expected Load Times
- Initial page load: < 1 second
- Activities load per level: < 500ms
- Modal open: < 300ms
- Evidence upload: 1-3 seconds (depends on file size)
- Chart render: < 200ms

### Network Traffic
- Initial page: ~500KB (HTML + CSS + JS)
- Activities JSON: ~50KB per level
- Evidence list: ~20KB
- Total page weight: < 1MB

## Mobile Testing

### Responsive Breakpoints
- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: < 768px

### Mobile-Specific Tests
- [ ] GAMO selector full width
- [ ] Tabs stack vertically
- [ ] Tables scroll horizontally
- [ ] Modals full screen on mobile
- [ ] Touch gestures work
- [ ] Form inputs large enough to tap

## Accessibility Testing

### Keyboard Navigation
- Tab key: Navigate through form fields
- Enter: Submit forms, activate buttons
- Esc: Close modals
- Arrow keys: Navigate tabs

### Screen Reader
- ARIA labels on modals
- Alt text on icons
- Semantic HTML structure
- Form labels associated

## Final Checklist

### Before Production
- [ ] All AJAX endpoints implemented
- [ ] Error handling complete
- [ ] Loading states functional
- [ ] Success/error messages display
- [ ] CSRF protection enabled
- [ ] File upload validation
- [ ] Authorization checks
- [ ] XSS prevention
- [ ] SQL injection prevention
- [ ] Browser compatibility tested
- [ ] Mobile responsive
- [ ] Accessibility compliant
- [ ] Performance optimized
- [ ] Documentation complete

### Deployment Steps
1. Run migrations
2. Clear cache: `php artisan cache:clear`
3. Clear view cache: `php artisan view:clear`
4. Optimize autoloader: `composer dump-autoload -o`
5. Test on staging environment
6. User acceptance testing
7. Deploy to production
8. Monitor error logs

## Support Information

### Log Files
```
storage/logs/laravel.log - Application errors
storage/logs/query.log - Database queries
```

### Debugging Tools
- Laravel Debugbar
- Browser DevTools
- Xdebug (for PHP debugging)

### Contact
For issues or questions:
- Check FRONTEND_IMPLEMENTATION_SUMMARY.md
- Review DATABASE_BACKEND_IMPLEMENTATION_SUMMARY.md
- Consult ANSWER_PAGE_DESIGN_SPEC.md

---
*Last Updated: January 8, 2026*
