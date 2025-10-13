# Local APK Build Instructions

## Prerequisites
You mentioned having:
- Dart SDK 3.8.1
- Android SDK 34.0.0 and 36.0.0
- NDK versions 25-29
- Build Tools 34.0.0 and 36.0.0
- Platform tools 36.0.0

✅ All these are compatible with this Flutter project!

## Important: Image Cropper Version

### For Local Build (Recommended)
With your Dart SDK 3.8.1, you can upgrade to the latest image_cropper version for better compatibility:

1. **Update pubspec.yaml** (only for local builds):
```yaml
dependencies:
  image_cropper: ^8.0.2  # or latest version
```

2. **Run:**
```bash
flutter pub get
```

### Current Replit Configuration
The project currently uses `image_cropper: ^6.0.0` for compatibility with Replit's Flutter 3.22.0 environment.

## Build APK Steps

### Step 1: Clean Previous Builds
```bash
flutter clean
flutter pub get
```

### Step 2: Build Release APK
```bash
flutter build apk --release
```

Or for split APKs (smaller file sizes):
```bash
flutter build apk --split-per-abi
```

### Step 3: Locate Your APK
The APK will be generated at:
- **Release APK:** `build/app/outputs/flutter-apk/app-release.apk`
- **Split APKs:** 
  - `build/app/outputs/flutter-apk/app-armeabi-v7a-release.apk`
  - `build/app/outputs/flutter-apk/app-arm64-v8a-release.apk`
  - `build/app/outputs/flutter-apk/app-x86_64-release.apk`

## Troubleshooting

### If you get image_cropper build errors:

**Option 1: Use Latest Version (Recommended for Dart 3.8.1)**
```yaml
# In pubspec.yaml
dependencies:
  image_cropper: ^8.0.2
```

**Option 2: Keep Compatible Version**
```yaml
# In pubspec.yaml
dependencies:
  image_cropper: ^6.0.0
```

### If you get "Could not resolve" errors:
1. Check internet connection
2. Run:
```bash
flutter clean
flutter pub cache repair
flutter pub get
```

### If you get Gradle errors:
1. Make sure your JAVA_HOME is set correctly
2. Android Studio should have Java 11 or 17
3. Run:
```bash
cd android
./gradlew clean
cd ..
flutter clean
```

## What's Been Fixed

✅ **Fixed Dart Code Issues:**
- Added missing `SharedPreferences` import in splash_screen.dart
- All theme configurations are error-free
- All routing configurations are correct

✅ **Fixed Android Configuration:**
- Updated image_cropper to compatible version (6.0.0 for Replit, can upgrade locally)
- Added UCropActivity to AndroidManifest.xml
- Added all required permissions (Camera, Storage, Internet)
- Gradle configuration is modern (Gradle 8.12, AGP 8.7.3, Kotlin 2.1.0)

✅ **Build System:**
- Compatible with Android SDK 34/36
- Compatible with your installed NDK versions
- Using modern Kotlin DSL for Gradle

## App Navigation Flow

The app follows this flow:
1. **Splash Screen** → Shows app logo and initializes
2. **First Time:** → Welcome Onboarding Screen
3. **Returning Users:** → Login Screen
4. **Authenticated Users:** → Dashboard (when implemented)

## Notes for Production Build

1. **Update Application ID** in `android/app/build.gradle.kts`:
   - Currently: `com.enugu.smartbus.enugu_smart_bus`
   - Change if needed for Play Store

2. **App Signing:** Before publishing to Play Store, configure proper signing:
   ```bash
   flutter build apk --release
   ```
   Follow: https://docs.flutter.dev/deployment/android#signing-the-app

3. **Test on Physical Device:**
   ```bash
   flutter install
   ```

## Quick Build Command

For a quick release build, simply run:
```bash
flutter build apk
```

The APK will be ready at: `build/app/outputs/flutter-apk/app-release.apk`

---

**Last Updated:** October 13, 2025
**Flutter Version Tested:** 3.22.0 (Replit), Should work with 3.24.0+ (Your local)
**Dart Version:** 3.4.0 (Replit), 3.8.1 (Your local)
