# 📱 Enugu Smart Bus - APK Build Guide

## Quick Build Command

```bash
flutter build apk
```

The APK will be generated at: `build/app/outputs/flutter-apk/app-release.apk`

---

## Prerequisites

✅ **Android Studio Installed Packages (Already Configured)**
- Latest Build Tools (34.0.0 and 36.0.0) ✓
- Multiple NDK versions (25–29) ✓
- cmdline-tools;latest (v19) ✓
- platforms up to Android 36 ✓
- emulator and platform-tools 36.0.0 ✓

✅ **Dart SDK: 3.8.1** (Your local environment)
✅ **Flutter SDK: 3.24.0 or higher**

---

## Build Instructions

### 1. Navigate to Project Directory
```bash
cd path/to/enugu_smart_bus
```

### 2. Get Dependencies
```bash
flutter pub get
```

### 3. Build APK (Release Mode)
```bash
flutter build apk --release
```

**Output Location:** `build/app/outputs/flutter-apk/app-release.apk`

### 4. Build APK (Debug Mode - for testing)
```bash
flutter build apk --debug
```

**Output Location:** `build/app/outputs/flutter-apk/app-debug.apk`

### 5. Build App Bundle (for Google Play Store)
```bash
flutter build appbundle --release
```

**Output Location:** `build/app/outputs/bundle/release/app-release.aab`

---

## Build Variants

### Split APKs by Architecture (Smaller file sizes)
```bash
flutter build apk --split-per-abi
```

This generates separate APKs for each CPU architecture:
- `app-armeabi-v7a-release.apk` (ARM 32-bit)
- `app-arm64-v8a-release.apk` (ARM 64-bit)
- `app-x86_64-release.apk` (Intel 64-bit)

### Fat APK (All architectures in one file)
```bash
flutter build apk --release
```

---

## Android Configuration

### Application ID
```
com.enugu.smartbus.enugu_smart_bus
```

### Version Information
- **Version Name:** 1.0.0
- **Version Code:** 1

Location: `pubspec.yaml`

### SDK Versions
The app uses Flutter's default SDK versions which are compatible with your installed packages:
- **compileSdk:** Determined by Flutter
- **targetSdk:** Determined by Flutter  
- **minSdk:** Determined by Flutter

Location: `android/app/build.gradle.kts`

---

## Deployment Checklist

### ✅ Before Building
- [x] All dependencies installed
- [x] Splash screen navigation fixed
- [x] No compilation errors
- [x] All screens properly connected
- [x] Android SDK packages compatible

### ✅ After Building
- [ ] Test the APK on a physical device or emulator
- [ ] Verify splash screen → welcome screen → login flow
- [ ] Test all navigation routes
- [ ] Verify app theme and UI consistency

---

## Installation on Device

### Via ADB (Android Debug Bridge)
```bash
adb install build/app/outputs/flutter-apk/app-release.apk
```

### Manual Installation
1. Transfer the APK file to your Android device
2. Enable "Install from Unknown Sources" in Settings
3. Tap the APK file to install

---

## Troubleshooting

### Issue: Gradle build fails
**Solution:** 
```bash
cd android
./gradlew clean
cd ..
flutter clean
flutter pub get
flutter build apk
```

### Issue: SDK version mismatch
**Solution:** Your local Dart SDK 3.8.1 is compatible. Ensure Flutter is up to date:
```bash
flutter upgrade
flutter doctor -v
```

### Issue: Missing dependencies
**Solution:**
```bash
flutter pub get
flutter pub upgrade
```

### Issue: APK not installing on device
**Solution:**
- Check minimum Android version compatibility
- Ensure "Install from Unknown Sources" is enabled
- Clear previous app data if reinstalling

---

## App Features

✅ **Splash Screen** - Animated splash with app branding
✅ **Welcome/Onboarding** - Multi-slide onboarding for new users
✅ **Login Screen** - Email/password authentication UI
✅ **Registration** - Multi-step registration with profile upload
✅ **Forgot Password** - Password recovery flow
✅ **Modern UI** - Dark blue fintech theme
✅ **Responsive Design** - Works on all screen sizes

---

## Production Release Notes

### For Google Play Store
1. Build App Bundle (AAB):
   ```bash
   flutter build appbundle --release
   ```

2. Sign the app bundle with your keystore

3. Upload to Google Play Console

### For Direct Distribution
1. Build signed APK with your release keystore

2. Configure signing in `android/app/build.gradle.kts`:
   ```kotlin
   signingConfigs {
       release {
           storeFile = file("path/to/your/keystore.jks")
           storePassword = "your-store-password"
           keyAlias = "your-key-alias"
           keyPassword = "your-key-password"
       }
   }
   
   buildTypes {
       release {
           signingConfig = signingConfigs.getByName("release")
       }
   }
   ```

3. Build:
   ```bash
   flutter build apk --release
   ```

---

## Support

For issues or questions:
- Check Flutter documentation: https://docs.flutter.dev
- Run `flutter doctor` to diagnose setup issues
- Review build logs in `build/app/outputs/logs/`

---

**Last Updated:** October 2025
**Flutter Version:** 3.24.0+
**Dart SDK:** 3.4.0 - 3.8.1+
