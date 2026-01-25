# 🎯 Icon Size Quick Reference Card

## Standard Sizes

| Class | Size | Use For | Example |
|-------|------|---------|---------|
| `.icon-size-sm` | 16px | Status badges, inline text | `<span class="badge"><i class="ti ti-check icon-size-sm"></i></span>` |
| `.icon-size-md` | 20px | **Action buttons, navigation** | `<a class="btn"><i class="ti ti-eye icon-size-md"></i></a>` |
| `.icon-size-lg` | 24px | **Primary CTAs, main actions** | `<button class="btn-primary"><i class="ti ti-plus icon-size-lg me-2"></i>Add</button>` |
| `.icon-size-xl` | 40px | File previews, media icons | `<i class="ti ti-file-pdf icon-size-xl"></i>` |
| `.icon-size-xxl` | 64px | Empty states, hero sections | `<i class="ti ti-inbox icon-size-xxl"></i>` |

---

## Common Patterns

### ✅ Table Action Buttons
```html
<a href="#" class="btn btn-sm btn-icon btn-ghost-info" title="View">
    <i class="ti ti-eye icon-size-md"></i>
</a>
<a href="#" class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
    <i class="ti ti-edit icon-size-md"></i>
</a>
<button class="btn btn-sm btn-icon btn-ghost-danger" title="Delete">
    <i class="ti ti-trash icon-size-md"></i>
</button>
```

### ✅ Primary Action Buttons
```html
<a href="#" class="btn btn-primary">
    <i class="ti ti-plus icon-size-lg me-2"></i>Add New
</a>
<button type="submit" class="btn btn-success">
    <i class="ti ti-check icon-size-lg me-2"></i>Submit
</button>
```

### ✅ Navigation Buttons
```html
<a href="#" class="btn btn-link">
    <i class="ti ti-arrow-left icon-size-md me-2"></i>Back
</a>
<button class="btn btn-primary">
    Next<i class="ti ti-arrow-right icon-size-md ms-2"></i>
</button>
```

### ✅ Dropdown Menu Items
```html
<a class="dropdown-item" href="#">
    <i class="ti ti-edit icon-size-md me-2"></i>Edit
</a>
<button class="dropdown-item">
    <i class="ti ti-trash icon-size-md me-2"></i>Delete
</button>
```

### ✅ Empty States
```html
<div class="empty text-center">
    <div class="empty-icon">
        <i class="ti ti-inbox icon-size-xxl text-muted"></i>
    </div>
    <p class="empty-title">No data found</p>
    <div class="empty-action">
        <a href="#" class="btn btn-primary">
            <i class="ti ti-plus icon-size-lg me-2"></i>Create New
        </a>
    </div>
</div>
```

---

## Quick Find & Replace

### VS Code Regex Patterns

**Find action button icons:**
```regex
ti ti-(eye|edit|trash|download)"><\/i>
```

**Replace with:**
```
ti ti-$1 icon-size-md"></i>
```

**Find primary button icons:**
```regex
ti ti-(plus|check|save) me-2
```

**Replace with:**
```
ti ti-$1 icon-size-lg me-2
```

**Find navigation icons:**
```regex
ti ti-arrow-(left|right) (me|ms)-
```

**Replace with:**
```
ti ti-arrow-$1 icon-size-md $2-
```

---

## Before/After Examples

### ❌ Before (Too Small)
```html
<i class="ti ti-edit"></i>
<i class="ti ti-plus me-2"></i>Add
<i class="ti ti-arrow-left me-2"></i>Back
```

### ✅ After (Proper Size)
```html
<i class="ti ti-edit icon-size-md"></i>
<i class="ti ti-plus icon-size-lg me-2"></i>Add
<i class="ti ti-arrow-left icon-size-md me-2"></i>Back
```

---

## Decision Tree

```
Is it a PRIMARY action button? (Add, Create, Submit, Save)
├─ YES → Use icon-size-lg (24px)
└─ NO → Continue...

Is it an ACTION button? (View, Edit, Delete, Download)
├─ YES → Use icon-size-md (20px)
└─ NO → Continue...

Is it NAVIGATION? (Back, Next, Arrows)
├─ YES → Use icon-size-md (20px)
└─ NO → Continue...

Is it EMPTY STATE or BIG DISPLAY?
├─ YES → Use icon-size-xl or icon-size-xxl
└─ NO → Use icon-size-sm or default
```

---

## Don'ts ❌

1. ❌ Don't use inline styles: `style="font-size: 20px"`
2. ❌ Don't mix sizes in same context
3. ❌ Don't forget spacing: Always use `me-2` or `ms-2`
4. ❌ Don't use default size for action buttons
5. ❌ Don't use huge icons for simple actions

---

## Checklist for New Features

When creating new views:

- [ ] Import icon-sizes.css in layout
- [ ] Use icon-size-lg for primary CTAs
- [ ] Use icon-size-md for action buttons
- [ ] Use icon-size-md for navigation
- [ ] Add proper spacing (me-2, ms-2)
- [ ] Test on mobile (min 44x44px touch target)
- [ ] Verify icon alignment with text
- [ ] Check consistency with existing views

---

**Quick Access:**
- 📄 Full Documentation: `/ICON_SIZE_STANDARD.md`
- 📄 Implementation Log: `/ICON_SIZE_UPDATE_COMPLETE.md`
- 🎨 CSS File: `/resources/css/icon-sizes.css`
- 🔧 Update Script: `/scripts/update-icon-sizes.sh`

**Last Updated:** 7 Januari 2026
