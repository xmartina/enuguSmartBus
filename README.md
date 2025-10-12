# Enugu Smart Bus - Flutter App

A modern Flutter authentication template for the Enugu Smart Bus booking application with Login, Signup, and Forgot Password screens.

## 📱 Features

- **Splash Screen** - Beautiful animated splash screen with Enugu Smart Bus branding
- **Welcome/Onboarding** - Multi-slide onboarding experience for new users
- **Login Screen** - Secure login with email/password and biometric support
- **Registration Screen** - Multi-step registration process with profile image upload
- **Forgot Password** - Password recovery flow
- **Modern UI** - Contemporary Nigerian Fintech design with dark blue primary color
- **Responsive Design** - Works on all screen sizes
- **Theme Support** - Light and dark theme ready

## 🎨 Design

- **Primary Color**: Deep Blue (#1a237e) - Professional and trustworthy
- **Typography**: Inter font family via Google Fonts
- **Animations**: Smooth transitions and micro-interactions
- **Material Design 3**: Latest Material Design guidelines

## 🚀 Quick Setup (Complete Project Structure)

### Prerequisites
1. **Install Flutter SDK** (3.24.0 or higher): https://docs.flutter.dev/get-started/install
2. **Download this project** to your local machine
3. **Open terminal** in project directory

### Automated Setup (Recommended)

#### Linux/macOS:
```bash
./setup_flutter_project.sh
```

#### Windows:
```cmd
setup_flutter_project.bat
```

This will automatically:
- ✅ Generate complete Flutter project structure (android/, ios/, web/ folders)
- ✅ Install all dependencies  
- ✅ Enable Flutter Web
- ✅ Prepare project for building APK/iOS/Web

### Manual Setup

If automated setup fails:

```bash
# 1. Generate project structure
flutter create --project-name enugu_smart_bus --org com.enugu.smartbus --platforms android,ios,web . --overwrite

# 2. Install dependencies
flutter pub get

# 3. Enable web
flutter config --enable-web
```

See **[SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)** for detailed step-by-step guide.

## 📱 Running the App

### Web (Chrome):
```bash
flutter run -d chrome
```

### Android:
```bash
flutter run  # Emulator or connected device
```

### iOS (macOS only):
```bash
flutter run
```

## 📦 Building for Production

### Android APK:
```bash
flutter build apk --release
```
**Output:** `build/app/outputs/flutter-apk/app-release.apk`

### Android App Bundle (Google Play):
```bash
flutter build appbundle --release
```
**Output:** `build/app/outputs/bundle/release/app-release.aab`

### iOS (macOS only):
```bash
flutter build ios --release
```

### Web:
```bash
flutter build web --release
```
**Output:** `build/web/`

## 🔐 Database Schema

The app is designed to work with the following user database structure:

```sql
CREATE TABLE `Users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_uuid` VARCHAR(36) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100),
    `phone_number` VARCHAR(20),
    `profile_image_url` VARCHAR(512),
    `user_type` ENUM('customer', 'expert', 'delivery_boy', 'admin') DEFAULT 'customer',
    `tagline` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 📁 Project Structure

```
lib/
├── core/
│   └── app_export.dart              # Central exports file
├── presentation/
│   ├── splash_screen/               # Splash screen with animations
│   ├── welcome_onboarding_screen/   # Onboarding slides
│   ├── login_screen/                # Login page
│   ├── registration_screen/         # Multi-step registration
│   │   └── widgets/                 # Registration form widgets
│   └── forgot_password_screen/      # Password recovery
├── routes/
│   └── app_routes.dart              # Navigation routes
├── theme/
│   └── app_theme.dart               # App theming (light/dark)
├── widgets/
│   ├── custom_icon_widget.dart      # Icon wrapper
│   ├── custom_image_widget.dart     # Image handler
│   └── custom_error_widget.dart     # Error display
└── main.dart                        # App entry point
```

## 🔧 Key Dependencies

- **sizer**: Responsive design system
- **google_fonts**: Typography (Inter font)
- **flutter_svg**: SVG icon support
- **fluttertoast**: Toast notifications
- **shared_preferences**: Local storage
- **image_picker**: Profile image upload
- **dio**: HTTP client for API integration
- **connectivity_plus**: Network monitoring

## 🧪 Test Credentials (Development)

```
Email: user@enugusmart.com
Password: password123
```

**⚠️ Important**: Remove mock credentials before production deployment!

## 🎯 Next Steps - Backend Integration

### 1. Set Up API Endpoints

Create these authentication endpoints:
- `POST /api/auth/login` - User authentication
- `POST /api/auth/register` - New user signup
- `POST /api/auth/forgot-password` - Password recovery
- `POST /api/auth/reset-password` - Password reset
- `GET /api/auth/verify-token` - Token validation

### 2. Update Authentication Logic

Replace mock authentication in:
- `lib/presentation/login_screen/login_screen.dart` (line 120-170)
- `lib/presentation/registration_screen/registration_screen.dart`
- `lib/presentation/forgot_password_screen/forgot_password_screen.dart`

### 3. Example API Integration

```dart
import 'package:dio/dio.dart';

final dio = Dio(BaseOptions(
  baseUrl: 'https://your-api-url.com',
  connectTimeout: Duration(seconds: 5),
  receiveTimeout: Duration(seconds: 3),
));

// Login example
Future<void> login(String email, String password) async {
  try {
    final response = await dio.post('/api/auth/login', data: {
      'email': email,
      'password': password,
    });
    
    if (response.statusCode == 200) {
      // Save token, navigate to home
      final token = response.data['token'];
      // Store token securely
    }
  } catch (e) {
    // Handle error
  }
}
```

### 4. Secure Token Storage

Add `flutter_secure_storage` to `pubspec.yaml`:

```yaml
dependencies:
  flutter_secure_storage: ^9.0.0
```

Store tokens securely:

```dart
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

final storage = FlutterSecureStorage();

// Save token
await storage.write(key: 'auth_token', value: token);

// Read token
String? token = await storage.read(key: 'auth_token');
```

## 🎨 Customization

### Change Primary Color
Edit `lib/theme/app_theme.dart`:
```dart
static const Color primaryLight = Color(0xFF1a237e); // Change this
```

### Update App Name
Edit `pubspec.yaml`:
```yaml
name: your_app_name
```

### Update Logo
Replace `assets/images/img_app_logo.svg` with your logo

### Modify App ID (for Android/iOS)

**Android:** Edit `android/app/build.gradle`:
```gradle
defaultConfig {
    applicationId "com.enugu.smartbus"  // Change this
}
```

**iOS:** Open `ios/Runner.xcworkspace` in Xcode and update Bundle Identifier

## 🔥 What's Included

✅ Complete authentication flow (Login, Signup, Forgot Password)  
✅ Modern Material Design 3 UI  
✅ Dark blue professional theme  
✅ Smooth animations and transitions  
✅ Form validation and error handling  
✅ Profile image upload support  
✅ Biometric authentication ready  
✅ Responsive design (all screen sizes)  
✅ Light and dark theme support  
✅ Production-ready code structure  

## 📚 Documentation Files

- **[SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)** - Complete setup guide
- **[LOCAL_SETUP_GUIDE.md](LOCAL_SETUP_GUIDE.md)** - Quick start (5 minutes)
- **[.gitignore](.gitignore)** - Comprehensive gitignore for Flutter

## 🚨 Troubleshooting

**"Flutter command not found"**
- Install Flutter SDK and add to PATH
- Restart terminal

**"No devices found"**
- For Web: Install Chrome
- For Android: Start emulator or connect device
- For iOS: Requires macOS with Xcode

**Build errors**
```bash
flutter clean
flutter pub get
flutter run
```

**Gradle build failed (Android)**
```bash
cd android
./gradlew clean
cd ..
flutter clean
flutter pub get
```

## 📝 Git Workflow

The `.gitignore` file is configured to exclude:
- Build outputs (`build/`, `*.apk`, `*.aab`, `*.ipa`)
- Generated files (`android/`, `ios/`, platform-specific generated files)
- Dependencies (`flutter/`, `.pub-cache/`)
- IDE files (`.idea/`, `.vscode/`)
- Environment files (`.env`)
- Temporary files

**What gets committed:**
- ✅ Source code (`lib/`)
- ✅ Assets (`assets/`)
- ✅ Configuration (`pubspec.yaml`)
- ✅ Documentation (`*.md`)

## 📞 Support

For Flutter issues:
- Flutter Documentation: https://docs.flutter.dev/
- Flutter Community: https://flutter.dev/community
- Stack Overflow: [flutter] tag

For project-specific questions:
- Check `SETUP_INSTRUCTIONS.md`
- Contact your development team

## 📄 License

This is a template project for Enugu Smart Bus.

---

**Made with ❤️ for Enugu Smart Bus**

**Status:** ✅ Ready for local deployment and production builds  
**Last Updated:** October 12, 2025
