# Testing Checklist - Assessment Interface

## 🎯 **Testing Environment**
- **URL:** http://127.0.0.1:8001/assessments/8/answer-new
- **Assessment Code:** ASM-TEST-20260120050322
- **Assessment ID:** 8
- **Test GAMOs:** EDM01, APO01, BAI01, DSS01, MEA01

---

## ✅ **PRE-TESTING CHECKLIST**

- [ ] Laravel server running (`php artisan serve --port=8001`)
- [ ] Database seeded with 120 COBIT activities
- [ ] User logged in with appropriate permissions
- [ ] Browser console open (F12) to check for errors
- [ ] Network tab open to monitor AJAX calls

---

## 📝 **TEST SCENARIOS**

### **1. TAB LEVEL - Activity Rating**

#### **Test 1.1: Load Activities by Level**
- [ ] Click "Tab Level"
- [ ] Default shows Level 2 activities
- [ ] Click "Level 3" button → activities reload
- [ ] Click "Level 4" button → activities reload
- [ ] Click "Level 5" button → activities reload
- [ ] Back to "Level 2" button → activities reload
- [ ] **Expected:** Activities filtered correctly by level
- [ ] **Check:** Activity codes match level (e.g., EDM01.02.xx for Level 2)

#### **Test 1.2: Rate an Activity**
- [ ] Click "Assessment" button (green check icon) on any activity
- [ ] Modal opens: "Penilaian Aktivitas"
- [ ] Activity name displays correctly (bilingual format)
- [ ] Rating buttons visible: F, L, P, N, N/A
- [ ] Click "L (Largely Achieved)" button
- [ ] Add notes: "Test rating for activity"
- [ ] Click "Save"
- [ ] Modal closes
- [ ] Table refreshes
- [ ] Rating column shows "L" with appropriate color
- [ ] **Expected:** Rating saved and displays correctly
- [ ] **Check:** Database `assessment_answers` table has new record

#### **Test 1.3: View Evidence Count**
- [ ] Find activity with evidence (evidence_count > 0)
- [ ] Click evidence count badge
- [ ] Evidence modal opens
- [ ] Evidence list displays files/URLs
- [ ] **Expected:** Evidence modal shows correct items
- [ ] **Check:** Evidence relates to clicked activity

---

### **2. TAB PBC - Document Requirements**

#### **Test 2.1: Load PBC by Level**
- [ ] Click "Tab PBC"
- [ ] Default shows Level 2 documents
- [ ] Table columns: No, Code, Document Requirement, Evidence, Status, Notes, Actions
- [ ] Activity names display with translated text subtitle
- [ ] Click "Level 3" button → PBC list reloads
- [ ] **Expected:** PBC filtered by level
- [ ] **Check:** Activities match selected level

#### **Test 2.2: Status Badges**
Test all 4 status types:

**Status: Pending (Gray Badge)**
- [ ] Find activity with no evidence and no rating
- [ ] Badge shows "Belum" in gray
- [ ] Hover shows "Belum ada evidence dan belum dinilai"
- [ ] **Expected:** Gray badge for pending status

**Status: Rated (Yellow Badge)**
- [ ] Find activity with rating but no evidence
- [ ] Badge shows "Dinilai" in yellow
- [ ] Hover shows "Sudah dinilai, belum ada evidence"
- [ ] **Expected:** Yellow badge for rated-only status

**Status: Partial (Blue Badge)**
- [ ] Find activity with evidence but no rating
- [ ] Badge shows "Sebagian" in blue
- [ ] Hover shows "Evidence tersedia, belum dinilai"
- [ ] **Expected:** Blue badge for partial status

**Status: Complete (Green Badge)**
- [ ] Find activity with both evidence and rating
- [ ] Badge shows "Selesai" in green
- [ ] Hover shows "Evidence tersedia & sudah dinilai"
- [ ] **Expected:** Green badge for complete status

#### **Test 2.3: Evidence Count Click**
- [ ] Click evidence count badge (green number)
- [ ] Evidence modal opens
- [ ] Shows evidence list for that activity
- [ ] **Expected:** Correct evidence displayed

#### **Test 2.4: Assessment from PBC**
- [ ] Click "Assessment" button (green check icon)
- [ ] Rating modal opens
- [ ] Rate activity with "P (Partially Achieved)"
- [ ] Save rating
- [ ] Return to PBC tab
- [ ] Status badge updates (should be "Dinilai" or "Selesai" if has evidence)
- [ ] **Expected:** Status updates after rating

---

### **3. TAB REPOSITORY - Evidence Management**

#### **Test 3.1: Upload File Evidence**
- [ ] Click "Tab Repository"
- [ ] Evidence list displays for selected GAMO
- [ ] Click "Upload Evidence" or "Add Evidence" button
- [ ] Modal opens: "Daftar Evidence"
- [ ] Switch to "Upload" section
- [ ] Select activity from dropdown
- [ ] Choose evidence type: "File"
- [ ] Input evidence name: "Test Document"
- [ ] Input description: "Test file upload"
- [ ] Choose file (PDF/Word/Excel)
- [ ] Click "Upload"
- [ ] Success message appears
- [ ] Evidence list refreshes
- [ ] New evidence appears in list
- [ ] **Expected:** File uploaded successfully
- [ ] **Check:** File exists in `storage/app/private/evidence/{assessment_id}/{activity_id}/`

#### **Test 3.2: Upload URL Evidence**
- [ ] Open evidence modal
- [ ] Select activity
- [ ] Choose evidence type: "URL"
- [ ] Input evidence name: "External Link"
- [ ] Input description: "Link to external resource"
- [ ] Input URL: "https://www.example.com/document.pdf"
- [ ] Click "Upload"
- [ ] Success message appears
- [ ] Evidence list refreshes
- [ ] New URL evidence appears with external link icon
- [ ] **Expected:** URL saved correctly

#### **Test 3.3: List View**
- [ ] Evidence displays in table format
- [ ] Columns: Type, Evidence Name, Description, File/URL, Activity, Uploaded, Actions
- [ ] Type shows icon (file or link)
- [ ] Evidence name displayed
- [ ] Description shows (truncated if long)
- [ ] File shows download link OR URL shows external link
- [ ] Activity name displayed
- [ ] Upload date formatted correctly
- [ ] Actions show download/delete buttons
- [ ] **Expected:** All information displayed correctly

#### **Test 3.4: Grid View**
- [ ] Click "Grid View" button (grid icon)
- [ ] View switches to card layout
- [ ] Each card shows:
  - Evidence type icon
  - Evidence name (bold)
  - Description
  - Associated activity
  - Upload date
  - Download/delete buttons
- [ ] **Expected:** Grid view displays correctly
- [ ] Click "List View" button to switch back

#### **Test 3.5: Download Evidence**
- [ ] Find file evidence (not URL)
- [ ] Click download button or file name link
- [ ] File downloads to browser
- [ ] Open downloaded file
- [ ] **Expected:** File downloads and opens correctly

#### **Test 3.6: Delete Evidence**
- [ ] Click delete button (red trash icon)
- [ ] Confirmation dialog appears
- [ ] Confirm deletion
- [ ] Evidence removed from list
- [ ] Success message appears
- [ ] **Expected:** Evidence deleted
- [ ] **Check:** Evidence count decreases in other tabs

---

### **4. TAB SUMMARY - Statistics**

#### **Test 4.1: Load Summary Statistics**
- [ ] Click "Tab Summary"
- [ ] Statistics cards display at top
- [ ] **Card 1:** Total Activities (shows number)
- [ ] **Card 2:** Dinilai (shows assessed count)
- [ ] **Card 3:** Belum Dinilai (shows not assessed count)
- [ ] **Card 4:** Compliance (shows percentage)
- [ ] **Expected:** Numbers match actual data

#### **Test 4.2: Summary Table by Level**
- [ ] Table displays with columns:
  - Level
  - Total Activities
  - Dinilai
  - Belum Dinilai
  - Rating Distribution (N/A, N, P, L, F)
  - Compliance %
- [ ] Row for Level 2 shows correct counts
- [ ] Row for Level 3 shows correct counts
- [ ] Row for Level 4 shows correct counts
- [ ] Row for Level 5 shows correct counts
- [ ] Totals row shows aggregated counts
- [ ] **Expected:** All numbers accurate
- [ ] **Check:** Rating distribution matches rated activities

#### **Test 4.3: Compliance Percentage**
- [ ] Compliance % calculated correctly
- [ ] Formula: `(Assessed - N/A) / (Total - N/A) × 100`
- [ ] Percentage rounded to 2 decimals
- [ ] Green progress bar matches percentage
- [ ] **Expected:** Accurate calculation

---

### **5. MODAL INTERACTIONS**

#### **Test 5.1: Assessment Modal**
- [ ] Open from Level tab or PBC tab
- [ ] Activity name displays
- [ ] Current rating shown (if exists)
- [ ] All rating buttons work (F, L, P, N, N/A)
- [ ] Notes textarea accepts input
- [ ] Save button works
- [ ] Cancel button closes without saving
- [ ] Modal closes after save
- [ ] **Expected:** Rating saved correctly

#### **Test 5.2: Evidence Modal - View Mode**
- [ ] Open by clicking evidence count badge
- [ ] Shows evidence list for specific activity
- [ ] List/Grid view toggle works
- [ ] Evidence details displayed
- [ ] Download links functional
- [ ] Can switch to upload section
- [ ] Close button works
- [ ] **Expected:** Seamless navigation

#### **Test 5.3: Evidence Modal - Upload Mode**
- [ ] Activity selector works
- [ ] Evidence type toggle (File/URL) works
- [ ] Form validation works:
  - Activity required
  - Evidence name required
  - File required (if type=file)
  - URL required (if type=url)
- [ ] Upload button submits form
- [ ] Loading state shows during upload
- [ ] Success message appears
- [ ] Modal refreshes to show new evidence
- [ ] **Expected:** Upload workflow smooth

---

### **6. CROSS-TAB SYNCHRONIZATION**

#### **Test 6.1: Rate Activity → Check PBC Status**
- [ ] Start in Level tab
- [ ] Rate an activity (give it "L")
- [ ] Switch to PBC tab
- [ ] Find same activity
- [ ] Status badge updates to "Dinilai" (yellow) or "Selesai" (green) if has evidence
- [ ] **Expected:** Status reflects new rating

#### **Test 6.2: Upload Evidence → Check Counts**
- [ ] Start in Repository tab
- [ ] Upload evidence for an activity
- [ ] Switch to Level tab
- [ ] Find same activity
- [ ] Evidence count increases
- [ ] Switch to PBC tab
- [ ] Find same activity
- [ ] Evidence count matches
- [ ] Status updates to "Sebagian" (blue) or "Selesai" (green) if rated
- [ ] **Expected:** Evidence count synchronized

#### **Test 6.3: Delete Evidence → Check Counts**
- [ ] Delete evidence from Repository tab
- [ ] Switch to Level tab
- [ ] Evidence count decreases
- [ ] Switch to PBC tab
- [ ] Status may change (from "Selesai" to "Dinilai" if no more evidence)
- [ ] **Expected:** Counts and status synchronized

---

### **7. GAMO SELECTOR**

#### **Test 7.1: Switch GAMO**
- [ ] Current GAMO: EDM01
- [ ] Select different GAMO from dropdown (e.g., APO01)
- [ ] All tabs reset
- [ ] Level tab loads APO01 Level 2 activities
- [ ] PBC tab loads APO01 documents
- [ ] Repository tab loads APO01 evidence
- [ ] Summary tab loads APO01 statistics
- [ ] **Expected:** All data changes to new GAMO

#### **Test 7.2: Test Multiple GAMOs**
Test with different GAMO types:
- [ ] EDM (Evaluate, Direct, Monitor) - e.g., EDM01
- [ ] APO (Align, Plan, Organize) - e.g., APO01
- [ ] BAI (Build, Acquire, Implement) - e.g., BAI01
- [ ] DSS (Deliver, Service, Support) - e.g., DSS01
- [ ] MEA (Monitor, Evaluate, Assess) - e.g., MEA01
- [ ] **Expected:** Each GAMO loads correct activities

---

### **8. ERROR HANDLING**

#### **Test 8.1: Network Error**
- [ ] Simulate network error (disconnect internet or stop Laravel server)
- [ ] Try to load activities
- [ ] Error message displays
- [ ] UI doesn't break
- [ ] **Expected:** Graceful error handling

#### **Test 8.2: Empty Data**
- [ ] Select GAMO with no activities (if exists)
- [ ] "No activities found" message displays
- [ ] UI remains functional
- [ ] **Expected:** Empty state handled

#### **Test 8.3: Invalid File Upload**
- [ ] Try to upload file > 10MB
- [ ] Validation error appears
- [ ] Form doesn't submit
- [ ] **Expected:** File size validation works

#### **Test 8.4: Missing Required Fields**
- [ ] Open evidence upload
- [ ] Leave evidence name blank
- [ ] Try to submit
- [ ] Validation error appears
- [ ] Form doesn't submit
- [ ] **Expected:** Required field validation works

---

### **9. PERFORMANCE TESTING**

#### **Test 9.1: Load Time**
- [ ] Measure page load time (should be < 2 seconds)
- [ ] Measure tab switch time (should be < 500ms)
- [ ] Measure AJAX response time (should be < 1 second)
- [ ] **Expected:** Fast response times

#### **Test 9.2: Large Dataset**
- [ ] Test with GAMO that has maximum activities
- [ ] All 4 tabs load without timeout
- [ ] Table rendering smooth (no lag)
- [ ] Pagination works (if implemented)
- [ ] **Expected:** Handles large data well

---

### **10. BROWSER COMPATIBILITY**

Test in multiple browsers:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (if on Mac)
- [ ] Edge (latest)

Check:
- [ ] Layout renders correctly
- [ ] AJAX calls work
- [ ] Modals display properly
- [ ] File uploads work
- [ ] Downloads work
- [ ] **Expected:** Consistent behavior across browsers

---

## 🐛 **BUG REPORTING**

If you find an issue, document it:

```
**Bug ID:** BUG-001
**Severity:** High / Medium / Low
**Component:** Tab Level / Tab PBC / etc.
**Description:** [Clear description of the issue]
**Steps to Reproduce:**
1. [Step 1]
2. [Step 2]
3. [Step 3]
**Expected Result:** [What should happen]
**Actual Result:** [What actually happens]
**Browser:** Chrome 120
**Console Errors:** [Copy console errors here]
**Screenshot:** [If applicable]
```

---

## ✅ **TEST COMPLETION CRITERIA**

Assessment interface is ready for production when:
- [ ] All test scenarios passed (100%)
- [ ] No critical or high-severity bugs
- [ ] Medium/low bugs documented and triaged
- [ ] Performance benchmarks met
- [ ] Cross-browser compatibility verified
- [ ] User acceptance testing completed

---

## 📊 **TEST RESULTS SUMMARY**

**Test Date:** _________________  
**Tester:** _________________  
**Total Tests:** 50+  
**Passed:** _____ / _____  
**Failed:** _____ / _____  
**Blocked:** _____ / _____  
**Pass Rate:** _____% 

**Critical Issues Found:** _____  
**High Issues Found:** _____  
**Medium Issues Found:** _____  
**Low Issues Found:** _____  

**Overall Status:** 🟢 Ready / 🟡 Needs Fixes / 🔴 Not Ready

---

**Generated:** January 21, 2025  
**Last Updated:** ______________  
**Next Test Cycle:** ______________
