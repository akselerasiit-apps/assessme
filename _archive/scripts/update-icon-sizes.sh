#!/bin/bash

# Icon Size Update Script
# Applies standardized icon sizes to remaining views
# Run from project root: bash scripts/update-icon-sizes.sh

echo "🎨 Starting Icon Size Update..."
echo "================================"

# Color codes
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Counters
updated=0
skipped=0

# Function to update file
update_icons() {
    local file=$1
    local backup="${file}.bak"
    
    # Create backup
    cp "$file" "$backup"
    
    # Apply replacements using sed
    sed -i '' \
        -e 's/ti ti-plus me-2/ti ti-plus icon-size-lg me-2/g' \
        -e 's/ti ti-edit me-2/ti ti-edit icon-size-lg me-2/g' \
        -e 's/ti ti-trash me-2/ti ti-trash icon-size-lg me-2/g' \
        -e 's/ti ti-download me-2/ti ti-download icon-size-lg me-2/g' \
        -e 's/ti ti-eye"><\/i>/ti ti-eye icon-size-md"><\/i>/g' \
        -e 's/ti ti-edit"><\/i>/ti ti-edit icon-size-md"><\/i>/g' \
        -e 's/ti ti-trash"><\/i>/ti ti-trash icon-size-md"><\/i>/g' \
        -e 's/ti ti-download"><\/i>/ti ti-download icon-size-md"><\/i>/g' \
        -e 's/ti ti-arrow-left me-/ti ti-arrow-left icon-size-md me-/g' \
        -e 's/ti ti-arrow-right ms-/ti ti-arrow-right icon-size-md ms-/g' \
        "$file"
    
    # Check if file changed
    if ! diff -q "$file" "$backup" > /dev/null; then
        echo -e "${GREEN}✓${NC} Updated: $file"
        ((updated++))
        rm "$backup"
    else
        echo -e "${BLUE}○${NC} No changes: $file"
        ((skipped++))
        mv "$backup" "$file"
    fi
}

# Find and update all blade files
echo ""
echo "Processing Blade files..."
echo "------------------------"

# Assessments
for file in resources/views/assessments/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

# Companies
for file in resources/views/companies/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

# Users
for file in resources/views/users/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

# Review & Approval
for file in resources/views/review-approval/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

# Banding
for file in resources/views/banding/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

# Reports
for file in resources/views/reports/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

# Dashboard
for file in resources/views/dashboard/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

# Profile
for file in resources/views/profile/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

# Master Data
for file in resources/views/master-data/*/*.blade.php; do
    [ -f "$file" ] && update_icons "$file"
done

echo ""
echo "================================"
echo "✅ Icon Size Update Complete!"
echo ""
echo "📊 Summary:"
echo "   Updated: $updated files"
echo "   Skipped: $skipped files"
echo ""
echo "🧪 Next steps:"
echo "   1. Review changes: git diff"
echo "   2. Test application"
echo "   3. Commit changes"
echo ""
