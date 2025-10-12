@echo off
REM Enugu Smart Bus - Flutter Project Setup Script (Windows)
REM This script will generate the complete Flutter project structure locally

echo.
echo 🚀 Setting up Enugu Smart Bus Flutter Project...
echo.

REM Check if Flutter is installed
where flutter >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Flutter is not installed!
    echo Please install Flutter from: https://docs.flutter.dev/get-started/install
    echo.
    pause
    exit /b 1
)

echo ✅ Flutter detected
flutter --version | findstr /C:"Flutter"
echo.

REM Backup current files
echo 📦 Backing up existing files...
if not exist "_backup_temp" mkdir "_backup_temp"
xcopy /E /I /Y lib _backup_temp\lib >nul 2>nul
xcopy /E /I /Y assets _backup_temp\assets >nul 2>nul
copy /Y pubspec.yaml _backup_temp\ >nul 2>nul
copy /Y README.md _backup_temp\ >nul 2>nul
copy /Y LOCAL_SETUP_GUIDE.md _backup_temp\ >nul 2>nul

REM Generate Flutter project structure
echo 🏗️  Generating Flutter project structure...
flutter create --project-name enugu_smart_bus --org com.enugu.smartbus --platforms android,ios,web . --overwrite

REM Restore our custom files
echo ♻️  Restoring custom implementation...
rmdir /S /Q lib >nul 2>nul
xcopy /E /I /Y _backup_temp\lib lib >nul
copy /Y _backup_temp\pubspec.yaml . >nul
copy /Y _backup_temp\README.md . >nul 2>nul
copy /Y _backup_temp\LOCAL_SETUP_GUIDE.md . >nul 2>nul

REM Clean up backup
rmdir /S /Q _backup_temp

REM Get dependencies
echo 📥 Installing dependencies...
flutter pub get

REM Enable web support
echo 🌐 Enabling Flutter Web...
flutter config --enable-web

echo.
echo ✅ Setup complete!
echo.
echo 📱 Next steps:
echo    1. Run: flutter run -d chrome (for web)
echo    2. Run: flutter run (for mobile)
echo    3. Build: flutter build apk --release (for Android)
echo    4. Build: flutter build ios --release (for iOS - macOS only)
echo.
echo 🎉 Your Enugu Smart Bus app is ready to go!
echo.
pause
