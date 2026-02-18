#!/bin/bash

# Script to display git diffs for all packages

# Function to show diff for a package
show_package_diff() {
    local package_path="$1"
    local package_name="$2"

    if [ -d "$package_path" ]; then
        # Check if there are any changes
        if ! git diff --quiet "$package_path" || ! git diff --cached --quiet "$package_path"; then
            echo ""
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
            echo "📦 Package: $package_name"
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
            echo ""
            git --no-pager diff "$package_path"
            git --no-pager diff --cached "$package_path"
            echo ""
            return 0
        fi
    fi
    return 1
}

# Count packages with changes
has_php_changes=false
has_vue_changes=false

# Check PHP packages for changes
for package in packages/*/; do
    if [ -d "$package" ]; then
        if ! git diff --quiet "$package" || ! git diff --cached --quiet "$package"; then
            has_php_changes=true
            break
        fi
    fi
done

# Check Vue packages for changes
for package in resources/js/vue-packages/*/; do
    if [ -d "$package" ]; then
        if ! git diff --quiet "$package" || ! git diff --cached --quiet "$package"; then
            has_vue_changes=true
            break
        fi
    fi
done

# Only show output if there are changes
if [ "$has_php_changes" = true ] || [ "$has_vue_changes" = true ]; then
    echo "========================================="
    echo "Git Diffs for All Packages"
    echo "========================================="
    echo ""
fi

# Process packages directory
if [ "$has_php_changes" = true ]; then
    echo "═══════════════════════════════════════"
    echo "PHP Packages (packages/)"
    echo "═══════════════════════════════════════"

    for package in packages/*/; do
        if [ -d "$package" ]; then
            package_name=$(basename "$package")
            show_package_diff "$package" "packages/$package_name"
        fi
    done
fi

# Process vue-packages directory
if [ "$has_vue_changes" = true ]; then
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
fi

# Only show done message if there were changes
if [ "$has_php_changes" = true ] || [ "$has_vue_changes" = true ]; then
    echo ""
    echo "========================================="
    echo "Done!"
    echo "========================================="
else
    echo "✓ No changes in any packages"
fi
