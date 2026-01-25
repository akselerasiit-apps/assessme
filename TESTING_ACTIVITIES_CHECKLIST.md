# Testing Checklist - COBIT 2019 Activities Integration

**Date**: January 20, 2026  
**Test Assessment ID**: 8  
**Assessment Code**: ASM-TEST-20260120050322

---

## ✅ Test Environment Setup

- ✅ Sample assessment created (ID: 8)
- ✅ 5 GAMO objectives selected (EDM01, APO01, BAI01, DSS01, MEA01)
- ✅ 15 activities available (5 GAMOs × 3 activities each)
- ✅ Laravel server running on http://127.0.0.1:8001
- ✅ Browser opened at answer-new interface

---

## 📋 Testing Checklist

### 1. Interface Loading ⏳
- [ ] Page loads without errors
- [ ] GAMO selector populated with 5 options
- [ ] Default GAMO selected (EDM01)
- [ ] 4 tabs visible (Level, PBC, Repository, Summary)
- [ ] Level tab active by default

### 2. Tab Level - Activities Display ⏳
- [ ] Level cards displayed (2, 3, 4, 5)
- [ ] Level 2 active by default
- [ ] Levels 4-5 locked initially
- [ ] Activities table loads
- [ ] Activities table shows columns:
  - [ ] Urutan (sequence)
  - [ ] Management Practice Code
  - [ ] Activities (English)
  - [ ] Translate (Indonesian)
  - [ ] Evidence count
  - [ ] Assessment rating
  - [ ] Weight
  - [ ] Actions dropdown

### 3. Activities Data Verification ⏳
Expected activities for EDM01:
- [ ] EDM01.01.A1 - Level 1
- [ ] EDM01.01.A2 - Level 3
- [ ] EDM01.01.A3 - Level 5

For each activity, verify:
- [ ] English text displayed correctly
- [ ] Indonesian translation displayed
- [ ] Level assignment correct
- [ ] Code format correct (EDM01.01.Ax)

### 4. GAMO Selector ⏳
Test switching between GAMOs:
- [ ] Select APO01 - loads 3 activities
- [ ] Select BAI01 - loads 3 activities
- [ ] Select DSS01 - loads 3 activities
- [ ] Select MEA01 - loads 3 activities
- [ ] Each GAMO shows correct activities

### 5. Level Cards Functionality ⏳
- [ ] Click Level 2 card - loads level 2 activities
- [ ] Click Level 3 card - loads level 3 activities
- [ ] Level 4 card locked (can't click)
- [ ] Level 5 card locked (can't click)
- [ ] Activity counts displayed per level
- [ ] Summary section updates (Values, Compliances)

### 6. Tab PBC (Document Requirements) ⏳
- [ ] PBC tab clickable
- [ ] Shows activities with document requirements
- [ ] Status badges working
- [ ] Evidence button functional
- [ ] Penilaian button opens modal

### 7. Tab Repository (Evidence Management) ⏳
- [ ] Repository tab clickable
- [ ] Upload form visible (File/URL toggle)
- [ ] Activity selector populated
- [ ] Filter section working
- [ ] Evidence grid/list displays

### 8. Tab Summary ⏳
- [ ] Summary tab clickable
- [ ] Sub-tabs: Penilaian & Progress Kapabilitas
- [ ] Statistics cards displayed
- [ ] Summary table per level
- [ ] Charts render

### 9. Rating Modal ⏳
Open rating modal for an activity:
- [ ] Click "Rate" button on activity
- [ ] Modal opens
- [ ] Activity details displayed
- [ ] Rating options visible (N/A, N, P, L, F)
- [ ] Scores shown (0.00, 0.15, 0.33, 0.67, 1.00)
- [ ] Notes textarea available
- [ ] Evidence count displayed
- [ ] Save button functional

### 10. Save Rating ⏳
Test saving a rating:
- [ ] Select rating (e.g., F - Fully Achieved)
- [ ] Add notes
- [ ] Click Save
- [ ] Success message displayed
- [ ] Modal closes
- [ ] Rating badge updated in table
- [ ] Summary section updated

### 11. Evidence Upload ⏳
Test uploading evidence:
- [ ] Click Evidence button for activity
- [ ] Evidence modal opens
- [ ] Upload form visible
- [ ] Select file (PDF/DOC/Image)
- [ ] Upload successful
- [ ] Evidence count badge updated
- [ ] Evidence displayed in list

### 12. Additional Modals ⏳
- [ ] **History Modal**: Shows audit log
- [ ] **Average Score Modal**: Shows scores per level
- [ ] **Notes Modal**: Lists all notes
- [ ] **Evidence List Modal**: Shows all evidence

### 13. Action Menu ⏳
Test dropdown actions per activity:
- [ ] Penilaian option opens rating modal
- [ ] Evidence option opens evidence modal
- [ ] All actions functional

### 14. JavaScript Functions ⏳
Verify console (F12) for errors:
- [ ] No JavaScript errors
- [ ] AJAX calls successful
- [ ] loadActivitiesByLevel() working
- [ ] renderActivities() working
- [ ] All global functions accessible

### 15. API Endpoints ⏳
Test backend endpoints:
- [ ] GET `/assessments/8/gamo/{gamo}/activities` returns data
- [ ] POST `/assessments/8/activity/{activity}/answer` saves rating
- [ ] GET `/assessments/8/gamo/{gamo}/history` returns history
- [ ] GET `/assessments/8/gamo/{gamo}/average-score` returns scores
- [ ] POST `/assessments/8/activity/{activity}/evidence` uploads evidence

---

## 🐛 Issues Found

### Critical Issues
_None found yet_

### Minor Issues  
_None found yet_

### Enhancement Suggestions
_To be added_

---

## 📊 Test Results Summary

| Category | Status | Pass/Total |
|----------|--------|------------|
| Interface Loading | ⏳ Pending | 0/5 |
| Tab Level | ⏳ Pending | 0/8 |
| Activities Data | ⏳ Pending | 0/7 |
| GAMO Selector | ⏳ Pending | 0/5 |
| Level Cards | ⏳ Pending | 0/6 |
| Tab PBC | ⏳ Pending | 0/5 |
| Tab Repository | ⏳ Pending | 0/5 |
| Tab Summary | ⏳ Pending | 0/4 |
| Rating Modal | ⏳ Pending | 0/9 |
| Save Rating | ⏳ Pending | 0/6 |
| Evidence Upload | ⏳ Pending | 0/6 |
| Additional Modals | ⏳ Pending | 0/4 |
| Action Menu | ⏳ Pending | 0/3 |
| JavaScript | ⏳ Pending | 0/4 |
| API Endpoints | ⏳ Pending | 0/5 |
| **TOTAL** | **⏳ In Progress** | **0/82** |

---

## 📝 Notes

- Testing started with browser open at: http://127.0.0.1:8001/assessments/8/answer-new
- Use browser DevTools (F12) to check console for errors
- Test both English and Indonesian text display
- Verify all AJAX calls return valid JSON
- Check database for saved data after each test

---

## ✅ Next Steps

1. **Manual Testing**: Perform UI testing in browser
2. **API Testing**: Use Postman/curl for endpoint verification
3. **Database Verification**: Check saved records after operations
4. **Bug Fixing**: Address any issues found
5. **Documentation**: Update test results

---

**Tester**: Ready for manual testing  
**Status**: ⏳ Test environment prepared, awaiting manual verification
