#!/bin/bash
# Update mobile app from main repository
# Run this in Replit Shell: bash update.sh

REPO="https://github.com/zayed1/tatar.git"
BRANCH="claude/fix-page-loading-issue-2TdZ2"

echo "📥 Pulling latest mobile-app from repository..."

# Clean previous temp
rm -rf _temp_repo

# Sparse clone - only mobile-app folder
git clone --depth 1 --filter=blob:none --sparse --branch "$BRANCH" "$REPO" _temp_repo 2>/dev/null
cd _temp_repo
git sparse-checkout set mobile-app
cd ..

# Copy files (exclude update.sh itself and node_modules)
echo "📋 Copying updated files..."
rsync -av --exclude='node_modules' --exclude='.expo' --exclude='update.sh' _temp_repo/mobile-app/ ./

# Cleanup
rm -rf _temp_repo

# Install dependencies if package.json changed
echo "📦 Installing dependencies..."
npm install

echo "✅ Update complete! Press Run to test."
