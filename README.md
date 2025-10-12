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

## 🚀 Local Setup Instructions

### Prerequisites

1. **Install Flutter SDK** (3.24.0 or higher)
   - Visit: https://docs.flutter.dev/get-started/install
   - Choose your operating system (Windows, macOS, Linux)
   - Follow the installation guide

2. **Install a Code Editor**
   - **VS Code** (Recommended): https://code.visualstudio.com/
     - Install Flutter extension: https://marketplace.visualstudio.com/items?itemName=Dart-Code.flutter
   - **Android Studio**: https://developer.android.com/studio

3. **Install Chrome** (for Flutter Web development)
   - https://www.google.com/chrome/

### Setup Steps

1. **Clone or Download this project**
   ```bash
   # If you have this as a git repository
   git clone <your-repo-url>
   cd enugu_smart_bus
   
   # Or simply download and extract the project folder
   ```

2. **Install Dependencies**
   ```bash
   flutter pub get
   ```

3. **Verify Flutter Installation**
   ```bash
   flutter doctor
   ```
   - Fix any issues shown (Android Studio, Xcode, etc.)

4. **Enable Flutter Web** (if not already enabled)
   ```bash
   flutter config --enable-web
   ```

5. **Run the App**
   
   **Option A: Web Browser (Chrome)**
   ```bash
   flutter run -d chrome
   ```
   
   **Option B: Mobile Emulator**
   ```bash
   # List available devices
   flutter devices
   
   # Run on specific device
   flutter run -d <device-id>
   ```
   
   **Option C: VS Code**
   - Open the project in VS Code
   - Press F5 or click "Run > Start Debugging"
   - Select your device (Chrome, Android, iOS)

## 📱 Screen Flow

1. **Splash Screen** → Initializes app, checks authentication
2. **Welcome/Onboarding** → First-time user introduction (if new user)
3. **Login Screen** → User authentication
4. **Registration Screen** → New user signup (multi-step)
5. **Forgot Password** → Password recovery

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
- **image_cropper**: Image editing

## 🧪 Testing

### Mock Credentials (Development Only)
- **Email**: user@enugusmart.com
- **Password**: password123

**⚠️ Important**: Remove mock credentials before production deployment!

## 📲 Building for Production

### Android APK
```bash
flutter build apk --release
# Output: build/app/outputs/flutter-apk/app-release.apk
```

### Android App Bundle (for Google Play)
```bash
flutter build appbundle --release
# Output: build/app/outputs/bundle/release/app-release.aab
```

### iOS (requires macOS with Xcode)
```bash
flutter build ios --release
# Then open ios/Runner.xcworkspace in Xcode
```

### Web
```bash
flutter build web --release
# Output: build/web/
```

## 🎯 Next Steps - Backend Integration

To connect this frontend to a backend:

1. **Set up API endpoints** for:
   - POST `/api/auth/login`
   - POST `/api/auth/register`
   - POST `/api/auth/forgot-password`
   - POST `/api/auth/reset-password`

2. **Update authentication logic** in:
   - `lib/presentation/login_screen/login_screen.dart`
   - `lib/presentation/registration_screen/registration_screen.dart`
   - `lib/presentation/forgot_password_screen/forgot_password_screen.dart`

3. **Add HTTP client** (dio is already included):
   ```dart
   import 'package:dio/dio.dart';
   
   final dio = Dio(BaseOptions(
     baseUrl: 'https://your-api-url.com',
     connectTimeout: Duration(seconds: 5),
     receiveTimeout: Duration(seconds: 3),
   ));
   ```

4. **Create API service classes** for authentication

5. **Implement secure token storage** using `flutter_secure_storage`

## 🎨 Customization

### Change Primary Color
Edit `lib/theme/app_theme.dart`:
```dart
static const Color primaryLight = Color(0xFF1a237e); // Change this
```

### Update App Name
Edit `pubspec.yaml`:
```yaml
name: enugu_smart_bus  # Change this
```

### Update Assets
- Logo: `assets/images/img_app_logo.svg`
- Icons: Place in `assets/images/`

## 📝 License

This is a template project for Enugu Smart Bus.

## 🤝 Support

For Flutter issues:
- Flutter Documentation: https://docs.flutter.dev/
- Stack Overflow: https://stackoverflow.com/questions/tagged/flutter

For project-specific questions:
- Contact your development team

---

**Made with ❤️ for Enugu Smart Bus**
