#!/bin/bash

# commit.sh - Commit and push changes in individual packages

set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to commit and push a package
commit_package() {
    local package_path="$1"
    local package_name=$(basename "$package_path")

    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}Checking: $package_path${NC}"

    # Check if it's a git repository
    if [ ! -d "$package_path/.git" ]; then
        echo -e "${YELLOW}⊘ Not a git repository${NC}"
        return
    fi

    # Change to package directory
    cd "$package_path"

    # Check if there are any changes
    if ! git diff --quiet 2>/dev/null || ! git diff --cached --quiet 2>/dev/null || [ -n "$(git status --porcelain)" ]; then
        echo -e "${GREEN}✓ Changes detected${NC}"

        # Show what changed
        echo -e "\n${YELLOW}Changed files:${NC}"
        git status --short

        # Add all changes
        git add -A

        # Prompt for commit message
        echo -e "\n${YELLOW}Enter commit message for ${package_name} (or press Enter to skip):${NC}"
        read -r commit_message

        if [ -z "$commit_message" ]; then
            echo -e "${YELLOW}⊘ Skipped${NC}"
            git reset 2>/dev/null || true
            cd - > /dev/null
            return
        fi

        # Commit
        if git commit -m "$commit_message"; then
            echo -e "${GREEN}✓ Committed${NC}"
        else
            echo -e "${RED}✗ Commit failed${NC}"
            cd - > /dev/null
            return 1
        fi

        # Push
        echo -e "${YELLOW}Pushing...${NC}"
        if git push; then
            echo -e "${GREEN}✓ Pushed successfully${NC}"
        else
            echo -e "${RED}✗ Push failed${NC}"
            cd - > /dev/null
            return 1
        fi
    else
        echo -e "${YELLOW}⊘ No changes${NC}"
    fi

    # Return to original directory
    cd - > /dev/null
}

echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Package Commit & Push Tool           ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"

# Save current directory
ORIGINAL_DIR=$(pwd)

# Process packages/ directory
echo -e "\n${YELLOW}Processing packages/ directory...${NC}"
for package in packages/*/; do
    if [ -d "$package" ]; then
        commit_package "$package"
    fi
done

# Process resources/js/packages/ directory
if [ -d "resources/js/packages" ]; then
    echo -e "\n${YELLOW}Processing resources/js/packages/ directory...${NC}"
    for package in resources/js/packages/*/; do
        if [ -d "$package" ]; then
            commit_package "$package"
        fi
    done
fi

echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✓ All packages processed${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

# Return to original directory
cd "$ORIGINAL_DIR"


