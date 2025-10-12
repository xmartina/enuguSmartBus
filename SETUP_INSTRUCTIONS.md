# 🚀 Complete Flutter Project Setup

## Quick Setup (Automated)

### For Linux/macOS:
```bash
chmod +x setup_flutter_project.sh
./setup_flutter_project.sh
```

### For Windows:
```cmd
setup_flutter_project.bat
```

This will automatically:
1. ✅ Generate complete Flutter project structure (android/, ios/, web/ folders)
2. ✅ Install all dependencies
3. ✅ Enable Flutter Web
4. ✅ Prepare for building APK/iOS/Web

---

## Manual Setup (If Automated Fails)

### Step 1: Generate Flutter Project Structure

Run this command in your project directory:

```bash
flutter create --project-name enugu_smart_bus --org com.enugu.smartbus --platforms android,ios,web . --overwrite
```

**⚠️ Important:** This will overwrite some files, but DON'T WORRY - your custom code in `lib/` folder is safe!

### Step 2: Restore Custom Files (if overwritten)

If `pubspec.yaml` gets overwritten, restore it from backup:

```bash
# It should already be correct, but if needed:
# Copy the pubspec.yaml from backup
```

### Step 3: Install Dependencies

```bash
flutter pub get
```

### Step 4: Enable Flutter Web

```bash
flutter config --enable-web
```

---

## ✅ Verify Setup

After setup, your project should have these folders:

```
enugu_smart_bus/
├── android/          ✅ (Android build files)
├── ios/             ✅ (iOS build files)  
├── web/             ✅ (Web build files)
├── lib/             ✅ (Your Flutter code)
├── assets/          ✅ (Images, icons)
├── pubspec.yaml     ✅ (Dependencies)
└── README.md        ✅ (Documentation)
```

Check if everything is ready:

```bash
flutter doctor
```

---

## 📱 Running the App

### Web (Chrome):
```bash
flutter run -d chrome
```

### Android:
```bash
# Start an emulator or connect a device first
flutter run
```

### iOS (macOS only):
```bash
flutter run
```

---

## 📦 Building for Production

### Android APK:
```bash
flutter build apk --release
```
**Output:** `build/app/outputs/flutter-apk/app-release.apk`

### Android App Bundle (for Google Play):
```bash
flutter build appbundle --release
```
**Output:** `build/app/outputs/bundle/release/app-release.aab`

### iOS (macOS only):
```bash
flutter build ios --release
```
Then open `ios/Runner.xcworkspace` in Xcode to archive and distribute.

### Web:
```bash
flutter build web --release
```
**Output:** `build/web/` (deploy to any static hosting)

---

## 🎯 What You Can Do Now

✅ **Run on emulator/device:** `flutter run`  
✅ **Build Android APK:** `flutter build apk --release`  
✅ **Build iOS app:** `flutter build ios --release` (macOS only)  
✅ **Deploy to web:** `flutter build web --release`  
✅ **Test on Chrome:** `flutter run -d chrome`  

---

## 🔧 Troubleshooting

### "Flutter command not found"
- Install Flutter SDK: https://docs.flutter.dev/get-started/install
- Add Flutter to PATH

### "No Android SDK found"
- Install Android Studio: https://developer.android.com/studio
- Run: `flutter doctor --android-licenses`

### "iOS toolchain missing" (macOS only)
- Install Xcode from Mac App Store
- Run: `sudo xcode-select --switch /Applications/Xcode.app/Contents/Developer`
- Run: `sudo xcodebuild -runFirstLaunch`

### "Gradle build failed"
- Run: `cd android && ./gradlew clean`
- Then: `flutter clean && flutter pub get`

### Dependencies error
- Run: `flutter clean`
- Run: `flutter pub get`
- Run: `flutter run`

---

## 📝 After Setup

Your project is now a **complete, production-ready Flutter app** with:

- ✅ Full Android support (build APK/AAB)
- ✅ Full iOS support (build IPA) 
- ✅ Full Web support (build static site)
- ✅ All authentication screens (Login, Signup, Forgot Password)
- ✅ Modern UI with dark blue theme
- ✅ Ready for backend integration

---

## 🎨 Next Steps - Backend Integration

1. **Create authentication API endpoints**
2. **Update API calls** in:
   - `lib/presentation/login_screen/login_screen.dart`
   - `lib/presentation/registration_screen/registration_screen.dart`
   - `lib/presentation/forgot_password_screen/forgot_password_screen.dart`
3. **Add token storage** using `flutter_secure_storage`
4. **Deploy backend** and update base URLs

See `README.md` for detailed backend integration guide.

---

## 🔑 Test Credentials

```
Email: user@enugusmart.com
Password: password123
```

⚠️ **Remove mock credentials before production deployment!**

---

## 📚 Documentation

- **README.md** - Complete project documentation
- **LOCAL_SETUP_GUIDE.md** - Quick start guide
- **pubspec.yaml** - Dependencies configuration

---

**Happy Building! 🎉**
