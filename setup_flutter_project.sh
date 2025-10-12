#!/bin/bash

# Enugu Smart Bus - Flutter Project Setup Script
# This script will generate the complete Flutter project structure locally

echo "🚀 Setting up Enugu Smart Bus Flutter Project..."
echo ""

# Check if Flutter is installed
if ! command -v flutter &> /dev/null; then
    echo "❌ Flutter is not installed!"
    echo "Please install Flutter from: https://docs.flutter.dev/get-started/install"
    echo ""
    exit 1
fi

echo "✅ Flutter detected: $(flutter --version | head -1)"
echo ""

# Backup current files
echo "📦 Backing up existing files..."
mkdir -p _backup_temp
cp -r lib _backup_temp/ 2>/dev/null || true
cp -r assets _backup_temp/ 2>/dev/null || true
cp pubspec.yaml _backup_temp/ 2>/dev/null || true
cp README.md _backup_temp/ 2>/dev/null || true
cp LOCAL_SETUP_GUIDE.md _backup_temp/ 2>/dev/null || true

# Generate Flutter project structure
echo "🏗️  Generating Flutter project structure..."
flutter create --project-name enugu_smart_bus --org com.enugu.smartbus --platforms android,ios,web . --overwrite

# Restore our custom files
echo "♻️  Restoring custom implementation..."
rm -rf lib
cp -r _backup_temp/lib .
cp _backup_temp/pubspec.yaml .
cp _backup_temp/README.md . 2>/dev/null || true
cp _backup_temp/LOCAL_SETUP_GUIDE.md . 2>/dev/null || true

# Clean up backup
rm -rf _backup_temp

# Get dependencies
echo "📥 Installing dependencies..."
flutter pub get

# Enable web support
echo "🌐 Enabling Flutter Web..."
flutter config --enable-web

echo ""
echo "✅ Setup complete!"
echo ""
echo "📱 Next steps:"
echo "   1. Run: flutter run -d chrome (for web)"
echo "   2. Run: flutter run (for mobile)"
echo "   3. Build: flutter build apk --release (for Android)"
echo "   4. Build: flutter build ios --release (for iOS - macOS only)"
echo ""
echo "🎉 Your Enugu Smart Bus app is ready to go!"
