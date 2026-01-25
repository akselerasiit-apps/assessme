# 🎨 Icon Size Standards - AssessMe Application
**Date:** 7 Januari 2026  
**Framework:** Tabler Icons  
**Purpose:** Standardized icon sizing across all views

---

## 📏 ICON SIZE CLASSES

### 1. **Action Button Icons** (Edit, Delete, View, etc.)
**Class:** `.icon-size-md` or inline `style="font-size: 1.25rem;"`  
**Size:** 1.25rem (20px)  
**Usage:** Icons in action buttons, table row actions, dropdown menus

```html
<!-- Standard action buttons -->
<button class="btn btn-sm btn-primary">
    <i class="ti ti-edit icon-size-md"></i>
</button>

<a href="#" class="btn btn-icon btn-sm btn-primary">
    <i class="ti ti-eye icon-size-md"></i>
</a>

<!-- Link buttons -->
<a href="#" class="btn btn-sm btn-ghost-primary">
    <i class="ti ti-trash icon-size-md me-2"></i>Delete
</a>
```

### 2. **Primary Action Icons** (Create, Add, Submit)
**Class:** `.icon-size-lg` or inline `style="font-size: 1.5rem;"`  
**Size:** 1.5rem (24px)  
**Usage:** Primary CTAs, main action buttons, header actions

```html
<!-- Primary actions -->
<a href="#" class="btn btn-primary">
    <i class="ti ti-plus icon-size-lg me-2"></i>Add Assessment
</a>

<button type="submit" class="btn btn-success">
    <i class="ti ti-check icon-size-lg me-2"></i>Submit
</button>
```

### 3. **Navigation Icons** (Back, Next, Arrows)
**Class:** `.icon-size-md`  
**Size:** 1.25rem (20px)  
**Usage:** Navigation buttons, wizard steps, breadcrumbs

```html
<a href="#" class="btn btn-link">
    <i class="ti ti-arrow-left icon-size-md me-2"></i>Back
</a>

<button class="btn btn-primary">
    Next<i class="ti ti-arrow-right icon-size-md ms-2"></i>
</button>
```

### 4. **Icon-Only Buttons** (Compact actions)
**Class:** `.icon-size-md`  
**Size:** 1.25rem (20px)  
**Usage:** Icon-only buttons in tables, cards

```html
<a href="#" class="btn btn-icon btn-sm btn-primary">
    <i class="ti ti-eye icon-size-md"></i>
</a>

<button class="btn btn-icon btn-sm btn-danger">
    <i class="ti ti-trash icon-size-md"></i>
</button>
```

### 5. **Large Display Icons** (File preview, empty states)
**Class:** `.icon-size-xl` or `.fs-1`, `.fs-2`  
**Size:** 2rem - 3rem (32px - 48px)  
**Usage:** File type indicators, empty state illustrations

```html
<div class="empty-state">
    <i class="ti ti-file-text icon-size-xl text-muted"></i>
</div>

<div class="file-preview">
    <i class="ti ti-file-pdf fs-1 text-danger"></i>
</div>
```

### 6. **Status Icons** (Badges, inline indicators)
**Class:** Default or `.icon-size-sm`  
**Size:** 1rem (16px) or default  
**Usage:** Status badges, inline text icons, alerts

```html
<span class="badge bg-success">
    <i class="ti ti-check me-1"></i>Active
</span>

<div class="alert alert-info">
    <i class="ti ti-info-circle me-2"></i>Information message
</div>
```

---

## 🎯 CSS CLASSES DEFINITION

Add to your main CSS file or create `/resources/css/icon-sizes.css`:

```css
/* ============================================
   ICON SIZE STANDARDS
   ============================================ */

/* Small icons - Status, badges, inline text */
.icon-size-sm {
    font-size: 1rem !important;
    width: 1rem;
    height: 1rem;
    line-height: 1;
}

/* Medium icons - Action buttons, navigation (DEFAULT) */
.icon-size-md {
    font-size: 1.25rem !important;
    width: 1.25rem;
    height: 1.25rem;
    line-height: 1;
}

/* Large icons - Primary CTAs, main actions */
.icon-size-lg {
    font-size: 1.5rem !important;
    width: 1.5rem;
    height: 1.5rem;
    line-height: 1;
}

/* Extra large icons - File previews, empty states */
.icon-size-xl {
    font-size: 2.5rem !important;
    width: 2.5rem;
    height: 2.5rem;
    line-height: 1;
}

/* Huge icons - Hero sections, major illustrations */
.icon-size-xxl {
    font-size: 4rem !important;
    width: 4rem;
    height: 4rem;
    line-height: 1;
}

/* ============================================
   BUTTON ICON SPACING
   ============================================ */

/* Icon before text */
.btn .ti + *:not(i) {
    margin-left: 0.5rem;
}

/* Icon after text */
.btn *:not(i) + .ti {
    margin-left: 0.5rem;
}

/* Icon-only buttons - center alignment */
.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
}

.btn-icon .ti {
    margin: 0 !important;
}

/* ============================================
   RESPONSIVE ADJUSTMENTS
   ============================================ */

@media (max-width: 768px) {
    /* Slightly larger on mobile for touch targets */
    .icon-size-md {
        font-size: 1.375rem !important;
    }
    
    .icon-size-lg {
        font-size: 1.625rem !important;
    }
}
```

---

## 📋 USAGE GUIDELINES

### ✅ DO's:
1. **Action buttons** (edit, delete, view) → Use `.icon-size-md` (1.25rem)
2. **Primary CTAs** (add, create, submit) → Use `.icon-size-lg` (1.5rem)
3. **Icon-only buttons** → Always use `.btn-icon` class
4. **Consistent spacing** → Use `me-2` or `ms-2` for icon-text spacing
5. **Touch targets** → Minimum 44x44px for mobile (use `btn-sm` cautiously)

### ❌ DON'Ts:
1. **Don't mix sizes** in the same context (e.g., all table actions should be same size)
2. **Don't use default size** for action buttons (too small)
3. **Don't forget spacing** between icon and text
4. **Don't use huge icons** for simple actions
5. **Don't use inline styles** unless necessary (prefer CSS classes)

---

## 🔧 IMPLEMENTATION CHECKLIST

### Files to Update:
- [x] `/resources/css/icon-sizes.css` - Create standard CSS
- [ ] `/resources/views/assessments/index.blade.php` - Action buttons
- [ ] `/resources/views/assessments/show.blade.php` - Action buttons
- [ ] `/resources/views/assessments/create.blade.php` - Navigation, CTAs
- [ ] `/resources/views/questions/index.blade.php` - Action buttons
- [ ] `/resources/views/questions/show.blade.php` - Action buttons
- [ ] `/resources/views/questions/edit.blade.php` - Action buttons
- [ ] `/resources/views/companies/index.blade.php` - Action buttons
- [ ] `/resources/views/users/index.blade.php` - Action buttons
- [ ] `/resources/views/review-approval/*.blade.php` - Action buttons
- [ ] `/resources/views/evidence/*.blade.php` - Action buttons
- [ ] `/resources/views/reports/*.blade.php` - Action buttons
- [ ] `/resources/views/layouts/partials/navbar.blade.php` - Menu icons

### Pattern to Replace:
```html
<!-- OLD (too small) -->
<i class="ti ti-edit"></i>
<i class="ti ti-trash"></i>
<i class="ti ti-eye"></i>

<!-- NEW (proper size) -->
<i class="ti ti-edit icon-size-md"></i>
<i class="ti ti-trash icon-size-md"></i>
<i class="ti ti-eye icon-size-md"></i>
```

---

## 📊 ICON SIZE COMPARISON TABLE

| Context | Old Size | New Size | Class | Visual Difference |
|---------|----------|----------|-------|-------------------|
| Action buttons | ~14px | 20px | `.icon-size-md` | +43% larger |
| Primary CTAs | ~14px | 24px | `.icon-size-lg` | +71% larger |
| Icon-only | ~14px | 20px | `.icon-size-md` | +43% larger |
| Status badges | 14px | 16px | `.icon-size-sm` | +14% larger |
| File previews | 16px | 32px | `.fs-2` | +100% larger |

---

## 🎨 VISUAL EXAMPLES

### Action Buttons (Table Row)
```
Before: [👁️] [✏️] [🗑️]  (14px - hard to see)
After:  [👁️ ] [✏️ ] [🗑️ ]  (20px - clear & clickable)
```

### Primary CTA
```
Before: [+ Add Assessment]  (14px icon)
After:  [➕ Add Assessment]  (24px icon - prominent)
```

### Icon-Only Button
```
Before: [👁️]  (14px in 32px button - small)
After:  [👁️ ]  (20px in 36px button - balanced)
```

---

## 🚀 MIGRATION STEPS

1. **Create CSS file:**
   ```bash
   # Create icon-sizes.css
   touch resources/css/icon-sizes.css
   ```

2. **Import in app.blade.php:**
   ```html
   <link rel="stylesheet" href="{{ asset('css/icon-sizes.css') }}">
   ```

3. **Find & replace in views:**
   ```bash
   # Find all icon usage
   grep -r "ti ti-" resources/views/
   
   # Replace systematically by context
   ```

4. **Test on different screens:**
   - Desktop (1920x1080)
   - Tablet (768x1024)
   - Mobile (375x667)

5. **Verify touch targets:**
   - Minimum 44x44px for mobile
   - Easy to tap without zooming

---

## 📱 RESPONSIVE CONSIDERATIONS

### Mobile (< 768px)
- Action buttons: 1.375rem (22px)
- Primary CTAs: 1.625rem (26px)
- Minimum touch target: 44x44px

### Tablet (768px - 1024px)
- Standard sizes apply
- Adequate spacing between actions

### Desktop (> 1024px)
- Standard sizes optimal
- Can use larger icons for emphasis

---

## 🎯 QUICK REFERENCE CARD

```
┌─────────────────────────────────────────────────┐
│ ICON SIZE QUICK REFERENCE                       │
├─────────────────────────────────────────────────┤
│ Context              Class          Size        │
├─────────────────────────────────────────────────┤
│ Action buttons       .icon-size-md  1.25rem     │
│ Primary CTAs         .icon-size-lg  1.5rem      │
│ Navigation           .icon-size-md  1.25rem     │
│ Icon-only buttons    .icon-size-md  1.25rem     │
│ File previews        .fs-2          2rem        │
│ Status badges        (default)      1rem        │
└─────────────────────────────────────────────────┘
```

---

## ✅ VERIFICATION CHECKLIST

After implementation, verify:
- [ ] All action buttons have consistent size
- [ ] Icons are clearly visible without strain
- [ ] Touch targets are adequate (44x44px minimum)
- [ ] Icons align properly with text
- [ ] No visual jumping between pages
- [ ] Responsive sizes work on mobile
- [ ] Icon-only buttons are properly centered
- [ ] Spacing is consistent throughout

---

**Status:** 📝 Documentation Complete  
**Next Steps:** Implement CSS classes and update all views  
**Priority:** High (User Experience Impact)
