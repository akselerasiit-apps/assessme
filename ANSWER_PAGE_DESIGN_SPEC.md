# Answer Assessment Page - Design Specification

## Overview
Design baru untuk halaman Answer Assessment berdasarkan referensi gambar yang diberikan user pada 8 Januari 2026.

## Struktur Layout

### 1. Header Section
- **GAMO Selector**: Dropdown untuk memilih GAMO (contoh: EDM01)
- Memungkinkan user berpindah antar GAMO yang dipilih untuk assessment
- User bisa switch context antar GAMO tanpa keluar halaman

### 2. Tab Navigation (4 tabs utama)
#### Tab 1: Level
- Menampilkan activities berdasarkan maturity level
- Grouping by level (Level 1-5)
- Main view untuk assessment

#### Tab 2: PBC (Practice-Based Checklist) - Reference Image 9
**Purpose**: Document requirements and compliance status per management practice

**Visual Components** (sama seperti Level Tab):
- Level cards di atas (Level 2, 3, 4, 5) dengan unlock mechanism
- Level heading: "Level 2"
- Table view dengan kolom berbeda

**Table Structure**:

1. **Urutan**: Nomor urut practice (1, 2, 3...)

2. **Management Practice Code**: 
   - Kode practice (EDMO2.01, EDMO2.02...)
   - Deskripsi lengkap practice dalam bahasa Inggris
   - Example: "Create and maintain portfolios of I&T-enabled investment programs, IT services and IT assets, which form the basis for the current IT budget and support the I&T tactical and strategic plans."

3. **Kebutuhan Dokumen** (Document Requirements):
   - List dokumen-dokumen yang dibutuhkan untuk practice ini
   - Bisa berupa text description atau bullet points
   - Panduan untuk auditor/assessor apa saja yang harus dikumpulkan
   - Column bisa kosong (dash "-") jika tidak ada kebutuhan spesifik

4. **Evidence**:
   - List semua evidence files untuk practice ini
   - Multiple files per practice (ditampilkan sebagai clickable links)
   - Format: Nama file sebagai hyperlink
   - Example files:
     - "RKAP Tahun 2025-TI.pdf"
     - "bukti kerja EDM 02.02 - evaluate value, DIVISI SPTIK - PEMBAHASAN USULAN RKAP 2026 15 AGT (1).pdf"
     - "Rencana Kerja Bulanan Tahun 205-Divisi SP & TIK.pdf"
   - **Jika belum ada evidence**: Tampilkan text "Belum ada evidence" (warna abu-abu, italic)
   - Terintegrasi dengan Feature "Daftar Evidence"

5. **Status**:
   - Badge/label menunjukkan status compliance
   - Options:
     - "Belum ada Status" (default, abu-abu)
     - "Compliant" (hijau)
     - "Partially Compliant" (kuning)
     - "Non-Compliant" (merah)
     - "Not Applicable" (abu-abu)
   - Dropdown atau editable field

6. **Keterangan** (Notes/Remarks):
   - Text field untuk catatan tambahan
   - Optional, bisa berisi penjelasan status atau findings
   - Free text input

7. **Aksi** (Actions):
   - Icon buttons untuk:
     - Upload evidence
     - Edit status
     - Add notes
     - View details

**Key Differences from Level Tab**:
- Focus pada **document requirements** bukan assessment ratings
- Shows **practice-level** (bukan activity-level)
- Evidence displayed directly in table (multiple per row)
- Status field for compliance tracking
- No "Weight" column
- No "Translate" column (description already in column 2)

**Use Case**:
- Untuk auditor/assessor yang ingin melihat **kebutuhan dokumen**
- Tracking **compliance status** per practice
- Quick view semua **evidence files** yang sudah dikumpulkan
- Alternative view dari Level Tab yang lebih fokus ke dokumentasi

#### Tab 3: Repository
- Tempat menyimpan/melihat evidence/dokumen
- Upload dan management files per activity

#### Tab 4: Summary - Reference Image 10
**Purpose**: Rekapitulasi dan overview hasil assessment

**Summary Tab memiliki 2 Sub-Tabs:**

##### Sub-Tab 1: Penilaian (Assessment Summary)

**Heading**:
- Title: "Summary Penilaian"
- Subtitle: "Rekapitulasi dari Penilaian Kapabilitas Asesmen"

**Filter**:
- Label: "Filter Summary Berdasarkan Target"
- Dropdown dengan options:
  - "Tampilkan Semua" (default - show all GAMOs)
  - Filter berdasarkan target level tertentu
  - Contoh: "Target Level 3", "Target Level 4", dll

**Table Structure**:

1. **Governance and Management Objectives**:
   - GAMO Code (e.g., EDM01, EDM02, EDM03, EDM04...)
   - Full GAMO description
   - Example:
     - "EDM01 - Ensured & Governance Framework Setting & Maintenance"
     - "EDM02 - Ensured Benefits Delivery"
     - "EDM03 - Ensured Risk Optimization"
     - "EDM04 - Ensured Resource Optimization"

2. **Target Adjustment**:
   - Target maturity level yang ditetapkan untuk GAMO ini
   - Numeric value (1-5)
   - Dari `assessment_gamo_selections.target_maturity_level`
   - Example: "3"

3. **Hasil Assessment** (Assessment Result):
   - Actual capability level achieved
   - Calculated average dari semua level yang sudah diassess
   - Decimal dengan 2 digit (e.g., "3.52", "3.24", "3.09", "3.18")
   - Formula: Weighted average dari compliance scores per level

4. **Gap**:
   - Selisih antara Hasil Assessment dan Target
   - Gap = Hasil Assessment - Target Adjustment
   - Display format:
     - Positive gap: "+0.52" (warna cyan/teal - exceeded target)
     - Zero gap: "0.00" (warna hijau - met target)
     - Negative gap: "-0.15" (warna merah - below target)
   - Shows performance vs target

5. **Level Columns (2, 3, 4, 5)**:
   - Badge/circle indicator untuk setiap level
   - Shows overall compliance rating untuk level tersebut:
     - **F** = Fully achieved (dark blue badge)
     - **L** = Largely achieved (blue badge)
     - **P** = Partially achieved (gray/purple badge)
     - **N** = Not achieved (red badge)
     - **N/A** = Not assessed/Not applicable (light gray)
   - **Tooltip on Hover**: Menampilkan angka penilaian aktual
     - Contoh: Hover pada badge "L" → tooltip shows "3.5"
     - Contoh: Hover pada badge "F" → tooltip shows "4.0" atau "1"
     - Format: Decimal dengan 1-2 digit sesuai nilai aktual
   - Quick visual summary dari achievement per level

**Summary Row (Bottom of Table)**:
- Label: **"Rata-rata"** (Average)
- Shows aggregated statistics across all GAMOs:
  - **Column 1** (after GAMO name): Average target (e.g., "3.45")
  - **Column 2** (Hasil Assessment): Overall average result (e.g., "3.01")
  - **Column 3** (Gap): Overall gap dengan color-coding
    - Format: "Gap : -0.44" (warna merah jika negative)
    - Shows aggregate performance: Rata-rata Hasil - Rata-rata Target
- Background: Dark blue untuk kolom Target & Hasil, Red/Pink untuk Gap jika negative
- Full-width prominent display untuk highlight overall performance

**Data Presentation**:
- One row per GAMO yang sudah diassess
- Sortable columns (click header to sort)
- Color-coded gaps untuk quick identification
- Responsive table dengan horizontal scroll jika perlu
- Summary row sticky/highlighted di bawah table

**Use Case**:
- Overview semua GAMOs dalam satu view
- Quick comparison: Target vs Actual results
- Identify over-performing atau under-performing GAMOs
- Management dashboard untuk decision making
- Aggregate metrics untuk executive summary

##### Sub-Tab 2: Progress Kapabilitas (Capability Progress) - Reference Image 11

**Heading**:
- Title: "Summary Progress Kapabilitas"
- Subtitle: "Rekapitulasi dari Activites Kapabilitas Asesmen"

**Statistics Cards** (Top Section):
Three prominent metric cards displayed horizontally:

1. **Card 1 - Total Activities** (Blue background, icon clipboard):
   - Value: "945"
   - Label: "Total Activities"
   - Shows total number of all activities across all GAMOs and levels

2. **Card 2 - Assessed Activities** (Cyan/Teal background, icon clipboard):
   - Value: "792"
   - Label: "Total Activities Sudah Dinilai"
   - Shows count of activities yang sudah diberi rating/assessment
   - Color-coded untuk highlight progress

3. **Card 3 - Progress Percentage** (Dark Blue background, icon clipboard):
   - Value: "83.81%"
   - Label: "Progress Kapabilitas Asesmen"
   - Calculated: (Total Sudah Dinilai / Total Activities) × 100
   - Main progress indicator untuk assessment completion

**Summary Progress Table**:

**Table Heading**:
- Title: "Summary Progress"
- Subtitle: "Rekapitulasi dari Progress Kapabilitas Asesmen"

**Table Structure**:

1. **Governance and Management Objectives**:
   - GAMO Code (e.g., EDM01, EDM02, EDM03...)
   - Full GAMO description
   - Example:
     - "EDM01 - Ensured & Governance Framework Setting & Maintenance"
     - "EDM02 - Ensured Benefits Delivery"
     - "EDM03 - Ensured Risk Optimization"

2-5. **Level Columns (Level 2, 3, 4, 5)**:
   Each cell contains:
   - **Top number**: Jumlah total activities di level tersebut untuk GAMO ini
   - **Bottom text**: "X Sudah Dinilai" (dalam warna cyan/teal)
   - Example:
     - "7" (black, bold)
     - "7 Sudah Dinilai" (cyan, smaller font)
   - Shows both total dan progress per level

6. **Total Column** (Dark Blue header):
   - Total activities across all levels untuk GAMO tersebut
   - Same format: Top number (total) + Bottom text (sudah dinilai)
   - Example:
     - "21" (total activities)
     - "21 Sudah Dinilai" (all assessed)

**Data Examples from Image**:
- EDM01:
  - Level 2: 7 total, 7 Sudah Dinilai
  - Level 3: 9 total, 9 Sudah Dinilai
  - Level 4: 5 total, 5 Sudah Dinilai
  - Level 5: 0 total
  - **Total: 21, 21 Sudah Dinilai (100% complete)**

- EDM02:
  - Level 2: 6 total, 6 Sudah Dinilai
  - Level 3: 9 total, 9 Sudah Dinilai
  - Level 4: 8 total, 8 Sudah Dinilai
  - Level 5: 2 total
  - **Total: 25, 23 Sudah Dinilai (92% complete)**

- EDM03:
  - Level 2: 9 total, 9 Sudah Dinilai
  - Level 3: 5 total, 5 Sudah Dinilai
  - Level 4: 2 total, 2 Sudah Dinilai
  - Level 5: 0 total
  - **Total: 16, 16 Sudah Dinilai (100% complete)**

**Calculation Logic**:
```
For each GAMO:
  For each Level (2-5):
    Total Activities = Count of activities in that level
    Sudah Dinilai = Count of activities with ratings (F/L/P/N)
  Total Column = Sum across all levels
  
Overall Progress % = (Sum of all Sudah Dinilai / Sum of all Total Activities) × 100
```

**Summary Rows (Bottom of Table)**:
Two prominent summary rows at the bottom:

1. **Total Activites Row** (Dark Blue background):
   - Label: "Total Activites" (left column)
   - Value: "945" (right column, Total column area)
   - Shows grand total dari semua activities across all GAMOs and levels
   
2. **Total Activities Sudah Dinilai Row** (Cyan/Teal background):
   - Label: "Total Activities Sudah Dinilai" (left column)
   - Value: "792" (right column, Total column area)
   - Shows total assessed activities
   - Matches dengan nilai di Statistics Card #2

**Color Scheme**:
- Dark blue background untuk Total Activites (matching header Total column)
- Cyan/teal background untuk Sudah Dinilai (matching "Sudah Dinilai" text in cells)
- Full-width prominent display
- Values aligned right in Total column area

**UI/UX Details**:
- Cyan/teal color for "Sudah Dinilai" text highlights completion
- Dark blue header for Total column emphasizes aggregate
- Clean two-line display per cell for clarity
- Responsive table dengan horizontal scroll
- Shows both absolute numbers and completion status
- Summary rows provide quick overview tanpa scroll

**Use Case**:
- Track assessment completion per GAMO
- Identify which GAMOs need more attention (lower "Sudah Dinilai" counts)
- Monitor overall progress towards 100% completion
- Level-by-level progress tracking
- Executive dashboard untuk project management

**Note**: Tab Summary hanya memiliki 2 sub-tabs (Penilaian dan Progress Kapabilitas). Sub-Tab Status PBC tidak diimplementasikan.

### 3. Level Cards (saat di Level Tab)
**Visual Components:**
- Cards untuk setiap level: Level 1, Level 2, Level 3, Level 4, Level 5
- Info per card:
  - Circle dengan nomor level
  - Text "Level X"
  - Jumlah activities (contoh: "7 Activities")

**States:**
- **Active Level**: Styling bold/dark, currently selected
- **Unlocked Level**: Available, bisa diklik (filled circle dengan nomor)
- **Locked Level**: Icon gembok (🔒), belum bisa diakses, warna abu-abu

**Level Unlock Mechanism** (See Reference Image 8):
- Level unlock secara bertahap berdasarkan pencapaian compliance level sebelumnya
- Visual progression: Connected line antara unlocked levels
- Contoh dari gambar:
  - Level 2 (6 Activities) → Unlocked ✓
  - Level 3 (9 Activities) → Unlocked ✓ (karena Level 2 memenuhi threshold)
  - Level 4 (8 Activities) → Unlocked ✓ (karena Level 3 memenuhi threshold)
  - Level 5 (2 Activities) → Locked 🔒 (Level 4 belum memenuhi threshold)

**Unlock Logic**:
```
Level 1: Always unlocked (baseline)
Level 2: Unlocked if Level 1 Compliance >= threshold (e.g., 0.85 or 85%)
Level 3: Unlocked if Level 2 Compliance >= threshold
Level 4: Unlocked if Level 3 Compliance >= threshold
Level 5: Unlocked if Level 4 Compliance >= threshold

Threshold value (configurable):
- Default: 0.85 (85% compliance)
- Or based on target_maturity_level from assessment_gamo_selections
```

**Unlock Rules**:
- Levels harus di-unlock secara berurutan (tidak bisa skip)
- User tidak bisa mengisi activities di level yang masih locked
- Real-time unlock: Saat compliance mencapai threshold, level berikutnya langsung unlock
- Visual feedback: Animation/notification saat level baru unlock

**Layout:**
- Horizontal layout untuk level cards
- Connected line visualization antara unlocked levels
- Below: Detail activities table untuk level yang dipilih

### 4. Activities Table (Detail per Level)

**Kolom Structure:**
1. **Urutan**: Nomor urut activity (1, 2, 3...)
2. **Management Practice Code**: Kode practice (EDM01.01, EDM01.02...)
3. **Activities**: 
   - Deskripsi lengkap activity dalam bahasa Inggris
   - Multi-line text, bisa panjang
4. **Translate**: 
   - Terjemahan bahasa Indonesia dari activity
   - Memudahkan pemahaman user lokal
5. **Evidence**: 
   - Icon/status indicator (checkmark icon ✓)
   - Hijau/cyan jika evidence sudah di-upload
   - Empty/grey jika belum
   - **Clickable**: Klik icon → Opens modal "Daftar Evidence"
   - Modal menampilkan semua evidence files untuk activity tersebut
   - User dapat view/download/add/delete evidence dari modal
   - Integrates dengan Feature 4 "Daftar Evidence" yang sudah didokumentasikan
6. **Asesmen**: 
   - Dropdown select untuk memilih rating
   - Options:
     - **Fully (F)** = Fully achieved
     - **Largely (L)** = Largely achieved  
     - **Partially (P)** = Partially achieved
     - **Not (N)** = Not achieved
   - Show current value (contoh: "Fully (F) 1")
   - **Clickable text**: Klik pada text hasil assessment (e.g., "Fully (F)") → Opens modal "Penilaian Kapabilitas"
   - Modal untuk view/edit assessment details - See detail below

**Modal: Penilaian Kapabilitas** (Assessment Rating Modal) - Reference Image 12

**Modal Title**: "Penilaian Kapabilitas"
- Close button (X) di top-right corner

**Section 1: Activity Information**

1. **Management Practice Code**:
   - Label: "Management Practice Code"
   - Value: Activity code (e.g., "EDM01.01")
   - Large, bold display
   
2. **Activities**:
   - Label: "Activities"
   - Description dalam bahasa Indonesia
   - Multi-line paragraph
   - Example: "Menganalisis dan mengidentifikasi faktor lingkungan internal dan eksternal (kewajiban hukum, peraturan, dan kontrak) dan tren dalam lingkungan bisnis yang dapat memengaruhi desain tata kelola."

3. **Translate**:
   - Label: "Translate"
   - Description dalam bahasa Inggris (italic style)
   - Multi-line paragraph
   - Example: "Analyze and identify the internal and external environmental factors (legal, regulatory and contractual obligations) and trends in the business environment that may influence governance design."

4. **Kebutuhan Dokumen**:
   - Label: "Kebutuhan Dokumen"
   - Document requirements (jika ada)
   - Display "-" jika tidak ada

**Section 2: Assessment Rating**

**Heading**: "Asesmen"

**Radio Button Group** (Horizontal layout):
- 5 rating options dengan numeric values:

1. **Not Value (N/A)**: 0
   - Untuk activities yang tidak applicable
   
2. **Not Achieved (N)**: 0.15
   - Activity belum tercapai sama sekali
   
3. **Partially (P)**: 0.33
   - Activity tercapai sebagian
   
4. **Largelly (L)**: 0.67
   - Activity sebagian besar tercapai
   
5. **Fully (F)**: 1
   - Activity tercapai sepenuhnya

**Display Format per Option**:
- Top row: Label (e.g., "Fully (F)")
- Bottom row: Numeric value (e.g., "1")
- Radio button below

**Behavior**:
- Single selection (radio buttons)
- Currently selected option highlighted dengan dark filled circle
- Click radio button untuk change rating

**Section 3: Notes**

**Label**: "Note"

**Rich Text Editor**:
- Toolbar buttons:
  - Paragraph style dropdown
  - **B** (Bold), *I* (Italic)
  - 🔗 Link insertion
  - Bullet list, Numbered list
  - Quote block
  - ↶ Undo, ↷ Redo
  
**Text Area**:
- Multi-line input field
- Untuk add/edit notes/justification assessment
- Example text: "Memenuhi aktivitas analisa dan identifikasi faktor internal dan eksternal"
- Supports rich formatting (bold, italic, lists, etc.)

**Modal Footer**:
- (Likely) Save and Cancel buttons (not fully visible in screenshot)

**Interaction Flow**:
1. User clicks assessment result text (e.g., "Fully (F)") in table
2. Modal opens showing current assessment details
3. User can:
   - View activity details (code, descriptions, requirements)
   - Change rating by selecting different radio button
   - Add/edit notes dengan rich text editor
4. Click Save → Updates assessment + closes modal
5. Click Cancel/Close → Discard changes + closes modal

**Database Integration**:
- Save to `assessment_answers` table:
  - `capability_rating`: Selected value (N/A, N, P, L, F)
  - `capability_score`: Numeric value (0, 0.15, 0.33, 0.67, 1)
  - `note_text`: Rich text HTML dari editor
  - Auto-calculate Values and Compliances setelah save

7. **Weight**: 
   - Bobot/nilai dari activity
   - Numeric value
8. **Aksi**: 
   - Tombol untuk actions
   - Icon buttons (edit, view, etc)

### 5. Action Menu (Three-dot Dropdown per Level)

**Menu Options:**
1. **📊 Lihat History Perubahan Data**
   - Modal popup dengan close button (X)
   - Header: "History Perubahan"
   - Sub-header: "Governance and Management Objectives (GAMO)" + kode GAMO (contoh: EDM02)
   - Timeline layout vertical dengan entries:
     - **Timestamp**: Format "DD Month YYYY pukul HH.mm" (contoh: "20 Oktober 2025 pukul 14.59")
     - **Timeline indicator**: Circle icon dengan vertical line connector
     - **User name**: Nama user yang melakukan perubahan (contoh: "Yasir")
     - **Action description**: "Melakukan Perubahan Data" atau detail action lainnya
   - Scrollable list untuk multiple entries
   - Log semua action/changes yang dilakukan pada GAMO level tersebut
   - Audit trail untuk tracking perubahan

2. **📈 Lihat Nilai Rata-rata**
   - Menampilkan average score dari level tersebut
   - Summary statistik per GAMO
   - Visual chart/graph untuk perbandingan
   - (Detail awaiting screenshot)

3. **📝 Lihat Daftar Catatan / Note Penilaian**
   - List semua notes yang telah dibuat untuk activities di level ini
   - Filter dan search notes
   - Quick access untuk review catatan
   - (Detail awaiting screenshot)

4. **📁 Lihat Daftar Evidence**
   - List semua evidence files yang di-upload untuk level ini
   - Preview dan download files
   - Organized by activity
   - (Detail awaiting screenshot)

**Menu Position:**
- Three-dot button (⋮) di sebelah kanan heading "Level X"
- Dropdown menu muncul saat diklik
- Modal atau sidebar untuk detail view masing-masing fitur

**Table Features:**
- Responsive width
- Fixed header saat scroll
- Zebra striping untuk readability
- Hover effect pada rows

**Summary Row (di bawah table):**
- **Values Row**: 
  - Label: "Values"
  - Total score dari semua activities di level tersebut
  - Format: "7.00" (decimal dengan 2 digit)
  - Breakdown di kolom kanan (jumlah activities: "7")
  
- **Compliances Row**:
  - Label: "Compliances"
  - Compliance score/percentage dari level
  - Format: "1.00" (decimal dengan 2 digit)
  - Background bar berwarna gelap (navy/blue)
  - Menunjukkan tingkat compliance dari assessment

**Summary Calculation:**
- Values = Sum of all activity weights in the level
- Compliances = Achievement percentage based on ratings (F=1.0, L=0.75, P=0.5, N=0)

## Data Flow

### GAMO Selection
```
User selects GAMO from dropdown
↓
Load all activities for selected GAMO
↓
Group activities by maturity level (1-5)
↓
Display level cards with activity count
↓
Auto-select first unlocked level
```

### Level Selection
```
User clicks on a level card
↓
Check if level is unlocked
↓
If unlocked: Load activities for that level
↓
Display in activities table
↓
Load existing answers/evidence if any
```

### Answer Submission
```
User fills assessment rating per activity
↓
Optionally upload evidence
↓
Auto-save every change (AJAX)
↓
Calculate and update summary (Values & Compliances)
↓
Update progress indicators
↓
Unlock next level when current complete
```

### Summary Calculation Logic
```
For each activity in level:
  - Get weight value
  - Get rating (F=1.0, L=0.75, P=0.5, N=0)
  - Calculate: activity_score = weight × rating

Values = Sum of all weights
Compliances = Sum of all activity_scores / Values
Display as decimal with 2 digits (e.g., 1.00)
```

## Technical Requirements

### Database Schema
- Existing: `assessment_answers` table
- Add columns if needed:
  - `capability_rating` (F, L, P, N)
  - `evidence_path` (file storage path)
  - `translated_text` (Indonesian translation)
- New table: `assessment_audit_logs` for history tracking:
  - `id` (primary key)
  - `assessment_id` (foreign key)
  - `gamo_objective_id` (foreign key)
  - `level` (maturity level)
  - `user_id` (who made the change)
  - `action` (type of change: create, update, delete)
  - `description` (action description in Indonesian)
  - `old_value` (JSON of old data)
  - `new_value` (JSON of new data)
  - `ip_address` (user IP)
  - `user_agent` (browser info)
  - `created_at` (timestamp)
  - `updated_at` (timestamp)

### Backend Logic
1. **Level Unlock Mechanism**:
   - Level 1 always unlocked (baseline assessment)
   - Level N unlocked if Level N-1 Compliance >= threshold (default 0.85 or 85%)
   - Compliance calculation: `(Sum of weighted scores) / (Total weights)`
   - Store unlock status per assessment + GAMO combination
   - Real-time check: After each answer save, recalculate compliance and check unlock
   - Visual feedback: Notification when new level unlocked
   - Sequential unlock: Cannot skip levels (must unlock 2→3→4→5 in order)
   - Threshold configurable per assessment or globally
   
   **Database tracking**:
   - Table: `assessment_gamo_level_status`
     - `assessment_id` (foreign key)
     - `gamo_objective_id` (foreign key)
     - `level` (1-5)
     - `is_unlocked` (boolean)
     - `unlocked_at` (timestamp, nullable)
     - `compliance_score` (decimal, current compliance)
   - Or add JSON column in `assessment_gamo_selections`: `level_unlock_status`

2. **Auto-save**:
   - AJAX endpoint for saving answers
   - Debounce 2-3 seconds after last change
   - Visual feedback (saving indicator)

3. **Evidence Management**:
   - File upload per activity
   - Support: PDF, DOC, XLS, JPG, PNG
   - Max size: 10MB per file
   - Store in `/storage/app/evidences/`

### Frontend Components

#### GAMO Selector
```html
<select class="form-select" id="gamoSelector">
  <option value="1">EDM01 - Ensured Governance Framework...</option>
  <option value="2">EDM02 - Ensured Benefits Delivery...</option>
  ...
</select>
```

#### Level Cards
```html
<div class="level-card" data-level="2" data-unlocked="true">
  <div class="level-circle">2</div>
  <div class="level-title">Level 2</div>
  <div class="level-count">7 Activities</div>
</div>
```

#### Activities Table
- Use DataTables or custom implementation
- Fixed header on scroll
- Responsive columns

## UI/UX Considerations

### Progressive Disclosure
- Only show unlocked levels
- Clear visual indication for locked levels
- Tooltip explaining unlock requirements

### Visual Feedback
- Loading spinners for async operations
- Success/error toasts for save operations
- Progress bars for completion status

### Bilingual Support
- English as primary (from COBIT 2019)
- Indonesian translation as secondary
- Toggle option if needed

### Accessibility
- Keyboard navigation support
- ARIA labels for screen readers
- Color contrast compliance

## Implementation Phases

### Phase 1: Structure & Layout
- Create new blade view
- Implement tab navigation
- Level cards layout
- Basic table structure

### Phase 2: Data Integration
- Controller methods for data fetching
- GAMO selector functionality
- Level grouping logic
- Load existing answers

### Phase 3: Interactive Features
- Answer dropdown functionality
- Auto-save mechanism
- Evidence upload
- Level unlock logic
- **Action menu implementation:**
  - History perubahan (audit log)
  - Nilai rata-rata (statistics)
  - Daftar catatan (notes list)
  - Daftar evidence (file list)

### Phase 4: Polish & Testing
- Responsive design
- Loading states
- Error handling
- Cross-browser testing
- Performance optimization for large datasets

## Files to Create/Modify

### Views
- `resources/views/assessments/take.blade.php` (major redesign)

  - Add `getHistoryLog()` method (for history perubahan)
  - Add `getAverageScore()` method (for nilai rata-rata)
  - Add `getNotesList()` method (for daftar catatan)
  - Add `getEvidenceList()` method (for daftar evidence)
### Controllers
- `app/Http/Controllers/Web/AssessmentTakingController.php`
  - Update `take()` method
  - Add `getActivitiesByLevel()` method
  - Add `saveAnswer()` method
  - Add `uploadEvidence()` method
Additional Features from Reference Image 2

### Summary Section
Setiap level memiliki summary section di bawah activities table yang menampilkan:

1. **Values Row**:
   - Total bobot dari semua activities di level tersebut
   - Contoh: "7.00" berarti total weight = 7
   - Kolom breakdown menunjukkan jumlah activities

2. **Compliances Row**:
   - Score compliance berdasarkan achievement rating
   - Range: 0.00 - 1.00 (0% - 100%)
   - Visual: Bar dengan background navy/blue
   - Calculation: (Sum of achieved scores) / (Total possible scores)

### Rating to Score Conversion:
- **Fully (F)** = 1.0 (100% achieved)
- **Largely (L)** = 0.75 (75% achieved)
- **Partially (P)** = 0.5 (50% achieved)
- **Not (N)** = 0.0 (0% achieved)

### Example Calculation:
```
Level 2 has 7 activities with weight 1 each
Activity 1: Rating F = 1.0 × 1 = 1.0
Activity 2: Rating F = 1.0 × 1 = 1.0
... (all 7 are Fully)
 (Added Summary Section from Reference Image 2)
**Status**: Complete - Ready for implementation
Compliances = 7.0 / 7.0 = 1.00 (100% compliance)
``**Image 1**: Main table structure with level cards and activities
- **Image 2**: Summary section showing Values and Compliances
- **Image 3**: Action menu (three-dot dropdown) with 4 additional features
  - Feature details awaiting screenshots from user:
    1. History Perubahan Data (Audit Log)
    2. Nilai Rata-rata (Average Score)
    3. Daftar Catatan/Note Penilaian (Notes List)
    4. Daftar Evidence (Evidence Files List)

#### Feature 1: History Perubahan (COMPLETED - See Reference Image 4)
[Already documented above]

#### Feature 2: Nilai Rata-rata (Average Score) - Reference Image 5

**Modal Title**: "Nilai Rata-rata Level Kapabilitas {GAMO_CODE}"
- Example: "Nilai Rata-rata Level Kapabilitas EDMO2"

**Modal Content**:

1. **Table Structure**:
   - Column 1: "Level" (showing maturity levels)
   - Column 2: "Compliance" (showing compliance scores)
   
2. **Data Rows**:
   - One row per assessed level
   - Example data:
     - Level 2 → Compliance: 1
     - Level 3 → Compliance: 0.85
     - Level 4 → Compliance: 0.39
   - Only show levels that have been assessed (have answers)

3. **Average Row**:
   - Label: "Rata-rata"
   - Value displayed in prominent dark blue background
   - Shows calculated average (e.g., "3.24")
   - Full-width cell spanning both columns
   
4. **Close Button**:
   - Label: "Tutup" (Close)
   - Positioned bottom-right
   - Primary button style

**Calculation Logic**:
```
For each assessed level:
  - Get Compliance value for that level
  - Compliance = (Sum of weighted scores) / (Total weights)

Average Capability Level = Sum(Level × Compliance) for all assessed levels

Example from image:
  Level 2: Compliance = 1.00
  Level 3: Compliance = 0.85
  Level 4: Compliance = 0.39
  
  Average = (2 × 1.00) + (3 × 0.85) + (4 × 0.39)
          = 2.00 + 2.55 + 1.56
          = 6.11 (if sum) or 3.24 (if weighted average)
```

**UI/UX Details**:
- Modal appears centered on screen
- Close button (X) in top-right corner
- Table has clean, minimal design
- Average value prominently highlighted
- Responsive modal size
- Semi-transparent backdrop

**Backend Requirements**:
- Method: `getAverageScore(Assessment $assessment, GamoObjective $gamo)`
- Returns:
  - Array of [level → compliance] pairs
  - Calculated average capability level
  - Only includes levels with at least one answer
- Reuses existing compliance calculation logic

#### Feature 3: Daftar Catatan / Note Penilaian (Notes List) - Reference Image 6

**Modal Title**: "Daftar Catatan / Note Penilaian"

**Modal Header**:
- Subtitle line 1: "Governance and Management Objectives (GAMO)"
- Subtitle line 2: "{GAMO_CODE} - Level {LEVEL_NUMBER}"
- Example: "EDMO2 - Level 2"

**Modal Content**:

1. **List Structure**:
   - Vertical list of all activities in the selected level
   - Each list item contains:
     - Note icon (document/comment icon) on the left
     - Activity code (e.g., "EDM02.01", "EDM02.02")
     - Activity description in Indonesian
   
2. **List Items**:
   - Clean, card-like appearance
   - Light background with subtle hover effect
   - Adequate padding for readability
   - Each item is clickable to view/edit notes
   
3. **Example Activities Displayed**:
   - EDM02.01: "Memenuhi aktivitas pemeliharaan program investasi yang mendukung TI"
   - EDM02.01: "Memenuhi aktivitas pemahaman bersama antara fungsi TI dan fungsi bisnis"
   - EDM02.01: "Memenuhi aktivitas identifikasi luasan scope kategori dukungan TI terhadap perusahaan"
   - EDM02.01: "Memenuhi aktivitas kesepakatan tujuan IT yang selaras dengan strategi perusahaan"
   - EDM02.02: "memenuhi aktivitas pemahaman persyaratan bisnis dan bagaimana IT dapat mendukung perusahaan"
   - EDM02.02: "Memenuhi aktivitas penetapan dan sosialisasi pedoman investasi"

4. **Close Button**:
   - Label: "Tutup" (Close)
   - Positioned bottom-right
   - Primary button style

**Interaction Flow**:
1. User clicks on any activity item
2. Opens note editor modal/inline editor
3. User can add/edit notes for that specific activity
4. Notes are saved per activity (assessment_id + activity_id)

**UI/UX Details**:
- Modal appears centered on screen
- Close button (X) in top-right corner
- Scrollable list if many activities
- Note icon indicates presence of notes (filled if has note, outlined if empty)
- Responsive modal size
- Semi-transparent backdrop

**Backend Requirements**:
- Method: `getNotesList(Assessment $assessment, GamoObjective $gamo, $level)`
- Returns:
  - List of all activities in the specified level
  - Activity code and description
  - Indication if note exists for each activity
- Database: Store notes in `assessment_answers` table or separate `assessment_notes` table
  - Fields: assessment_id, activity_id, level, note_text, created_by, updated_at

#### Feature 4: Daftar Evidence (Evidence Files List) - Reference Image 7

**Modal/Page Title**: "Evidence {NUMBER}"
- Subtitle: "{ACTIVITY_CODE}" (e.g., "EDMO2.01")

**Key Feature**: Each activity can have **multiple evidence items**

**Evidence Item Structure**:

Each evidence record displays:

1. **Nama Evidence** (Evidence Name)
   - File name or document title
   - Example: "RKAP Tahun 2025-TI.pdf"
   - Required field

2. **Deskripsi Evidence** (Evidence Description)
   - Detailed description of what the evidence contains
   - Example: "rencana anggaran untuk divisi SP & TIK tahun 2025"
   - Text area field
   - Optional but recommended

3. **URL**
   - Link to the document/file
   - Example: "https://1drv.ms/b/c/3a9..."
   - Can be external link (Google Drive, OneDrive, etc.) or internal file path
   - Clickable hyperlink

**Layout Structure**:
- Each evidence item displayed as a card/section
- Clear separation between multiple evidence items
- Image/document preview area (gray placeholder box shown in reference)
- Three-dot menu (⋯) for actions: Edit, Delete, View

**Interaction Flow**:
1. User clicks "Daftar Evidence" from action menu
2. Opens modal/page showing all evidence for selected activity
3. User can:
   - View list of all evidence items
   - Click URL to open document in new tab
   - Add new evidence (+ button)
   - Edit existing evidence (⋯ menu → Edit)
   - Delete evidence (⋯ menu → Delete)
   - View evidence details (⋯ menu → View)

**Add Evidence Form**:
- Upload file OR paste URL
- Enter evidence name (auto-filled if file uploaded)
- Enter description
- Save button

**UI/UX Details**:
- Scrollable list if many evidence items
- Document preview placeholder (for images/PDFs)
- Clickable URLs open in new tab
- File upload with drag-and-drop support
- Support for multiple file types (PDF, DOC, XLS, images, etc.)
- File size limit indicator
- Progress bar during upload

**Backend Requirements**:
- Method: `getEvidenceList(Assessment $assessment, $activity_id)`
- Returns: Array of evidence items for the activity
- File storage: `storage/app/assessments/{assessment_id}/evidence/`
- Database table: `assessment_evidence`
  - `id` (primary key)
  - `assessment_id` (foreign key)
  - `activity_id` (foreign key - to management_practice_activities table)
  - `evidence_name` (varchar)
  - `evidence_description` (text, nullable)
  - `file_path` (varchar, nullable - for uploaded files)
  - `url` (varchar, nullable - for external links)
  - `file_type` (varchar - pdf, doc, xls, image, etc.)
  - `file_size` (integer - in bytes)
  - `uploaded_by` (foreign key to users)
  - `created_at`
  - `updated_at`

**Validation**:
- At least one of `file_path` or `url` must be provided
- File size limit: 10MB (configurable)
- Allowed file types: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, ZIP
- URL validation for external links
## Notes
- Design based on image references from user dated 2026-01-08
- Image 1: Main table structure with level cards and activities
- Image 2: Summary section showing Values and Compliances
- Prioritize user experience and intuitive flow
- Maintain consistency with existing application design (Tabler theme)
- Consider performance for large number of activities
- Real-time calculation of summary when ratings change
  - Auto-save logic
  - File upload

### CSS
- Add to `resources/css/app.css`
  - Level cards styling
  - Table responsive styles
  - Tab navigation styles

## Notes
- Design based on image reference from user dated 2026-01-08
- Prioritize user experience and intuitive flow
- Maintain consistency with existing application design (Tabler theme)
- Consider performance for large number of activities

## Next Steps
1. Wait for additional reference images from user
2. Finalize design specifications
3. Begin implementation starting with Phase 1
4. Iterative development with user feedback

---
**Document Created**: 2026-01-08
**Last Updated**: 2026-01-08
- Added Summary Section from Reference Image 2
- Added Action Menu (4 features) from Reference Image 3
- Added History Perubahan detail from Reference Image 4
- Added Nilai Rata-rata detail from Reference Image 5
- Added Daftar Catatan detail from Reference Image 6
- Added Daftar Evidence detail from Reference Image 7
- Added Level Unlock Mechanism detail from Reference Image 8
- Added Tab PBC structure from Reference Image 9
- Added Tab Summary Sub-Tab Penilaian from Reference Image 10
- Added Tab Summary Sub-Tab Progress Kapabilitas from Reference Image 11
- Added Modal Penilaian Kapabilitas (Assessment Rating Modal) from Reference Image 12
**Status**: ✅ Complete specification - All 12 reference images documented and ready for implementation
