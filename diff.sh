#!/bin/bash

# Script to display git diffs for all packages

echo "========================================="
echo "Git Diffs for All Packages"
echo "========================================="
echo ""

# Function to show diff for a package
show_package_diff() {
    local package_path="$1"
    local package_name="$2"

    if [ -d "$package_path" ]; then
        echo ""
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        echo "📦 Package: $package_name"
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

        # Check if there are any changes
        if git diff --quiet "$package_path" && git diff --cached --quiet "$package_path"; then
            echo "✓ No changes"
        else
            echo ""
            git --no-pager diff "$package_path"
            git --no-pager diff --cached "$package_path"
        fi
        echo ""
    fi
}

# Process packages directory
echo "═══════════════════════════════════════"
echo "PHP Packages (packages/)"
echo "═══════════════════════════════════════"

for package in packages/*/; do
    if [ -d "$package" ]; then
        package_name=$(basename "$package")
        show_package_diff "$package" "packages/$package_name"
    fi
done

# Process vue-packages directory
echo ""
echo "═══════════════════════════════════════"
echo "Vue Packages (resources/js/vue-packages/)"
echo "═══════════════════════════════════════"

for package in resources/js/vue-packages/*/; do
    if [ -d "$package" ]; then
        package_name=$(basename "$package")
        show_package_diff "$package" "vue-packages/$package_name"
    fi
done

echo ""
echo "========================================="
echo "Done!"
echo "========================================="

