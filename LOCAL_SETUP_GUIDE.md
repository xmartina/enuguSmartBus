# 🚀 Quick Start Guide - Enugu Smart Bus Flutter App

## ⚡ Fast Local Setup (5 Minutes)

### Step 1: Install Flutter (if not already installed)

#### Windows:
```powershell
# Download Flutter SDK
# Visit: https://docs.flutter.dev/get-started/install/windows

# Or use Chocolatey
choco install flutter
```

#### macOS:
```bash
# Using Homebrew
brew install --cask flutter

# Or download manually from:
# https://docs.flutter.dev/get-started/install/macos
```

#### Linux (Ubuntu/Debian):
```bash
# Download Flutter
cd ~
git clone https://github.com/flutter/flutter.git -b stable
export PATH="$PATH:`pwd`/flutter/bin"

# Add to bashrc/zshrc for permanent PATH
echo 'export PATH="$PATH:$HOME/flutter/bin"' >> ~/.bashrc
```

### Step 2: Verify Flutter Installation
```bash
flutter doctor
```

### Step 3: Navigate to Project & Install Dependencies
```bash
cd /path/to/enugu_smart_bus
flutter pub get
```

### Step 4: Run the App

#### For Web (Chrome):
```bash
flutter run -d chrome
```

#### For Android:
```bash
# First, start an Android emulator or connect a device
flutter run
```

#### For iOS (macOS only):
```bash
flutter run
```

## 📱 What's Included

✅ **Splash Screen** - Animated startup screen with Enugu Smart Bus branding  
✅ **Onboarding** - Welcome slides for new users  
✅ **Login Screen** - Email/password authentication with biometric option  
✅ **Registration** - Multi-step signup with profile image upload  
✅ **Forgot Password** - Password recovery flow  
✅ **Modern UI** - Dark blue theme, smooth animations  
✅ **Responsive Design** - Works on all screen sizes  

## 🔑 Test Credentials (Development)

```
Email: user@enugusmart.com
Password: password123
```

## 🎨 Primary Color

The app uses a **dark blue** (#1a237e) as the primary color for a professional, trustworthy look.

## 📸 Screenshots

Once you run the app, you'll see:
1. Animated splash screen with bus icon
2. Onboarding slides (for new users)
3. Modern login form
4. Multi-step registration
5. Password recovery screen

## 🔧 Quick Customization

### Change App Name
Edit `pubspec.yaml` line 1:
```yaml
name: your_app_name
```

### Change Primary Color
Edit `lib/theme/app_theme.dart` line 10-11:
```dart
static const Color primaryLight = Color(0xFF1a237e); // Your color
```

### Update Logo
Replace `assets/images/img_app_logo.svg` with your logo

## 🚨 Troubleshooting

**"Flutter command not found"**
- Make sure Flutter is in your PATH
- Restart terminal after installation

**"No devices found"**
- For Web: Install Chrome browser
- For Android: Start an emulator or connect a device
- For iOS: Requires macOS with Xcode

**"Pub get failed"**
- Check internet connection
- Run: `flutter pub cache repair`

**"Build errors"**
- Run: `flutter clean`
- Then: `flutter pub get`
- Finally: `flutter run`

## 📦 Build for Production

### Android APK:
```bash
flutter build apk --release
```
Output: `build/app/outputs/flutter-apk/app-release.apk`

### iOS (macOS only):
```bash
flutter build ios --release
```

### Web:
```bash
flutter build web --release
```
Output: `build/web/` (deploy to any web host)

## 🎯 Next Steps - Connect to Backend

1. Create API endpoints for authentication
2. Update authentication logic in screen files
3. Add API service layer
4. Implement secure token storage

See `README.md` for detailed backend integration guide.

## 📞 Need Help?

- Flutter Docs: https://docs.flutter.dev/
- Flutter Community: https://flutter.dev/community
- Stack Overflow: [flutter] tag

---

**Happy Coding! 🎉**
