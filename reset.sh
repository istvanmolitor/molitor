#!/bin/bash

# reset.sh - Run git reset --hard in individual packages

set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to hard reset a package
reset_package() {
    local package_path="$1"

    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}Checking: $package_path${NC}"

    # Check if it's a git repository
    if [ ! -d "$package_path/.git" ]; then
        echo -e "${YELLOW}⊘ Not a git repository${NC}"
        return
    fi

    # Change to package directory
    cd "$package_path"

    echo -e "${YELLOW}Running git reset --hard...${NC}"
    if git reset --hard; then
        echo -e "${GREEN}✓ Reset completed${NC}"
    else
        echo -e "${RED}✗ Reset failed${NC}"
        cd - > /dev/null
        return 1
    fi

    # Return to original directory
    cd - > /dev/null
}

echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Package Hard Reset Tool             ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"

# Save current directory
ORIGINAL_DIR=$(pwd)

# Process packages/ directory
echo -e "\n${YELLOW}Processing packages/ directory...${NC}"
for package in packages/*/; do
    if [ -d "$package" ]; then
        reset_package "$package"
    fi
done

# Process resources/js/packages/ directory
if [ -d "resources/js/packages" ]; then
    echo -e "\n${YELLOW}Processing resources/js/packages/ directory...${NC}"
    for package in resources/js/packages/*/; do
        if [ -d "$package" ]; then
            reset_package "$package"
        fi
    done
fi

echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✓ All packages processed${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

# Return to original directory
cd "$ORIGINAL_DIR"

