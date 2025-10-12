# Enugu Smart Bus - Flutter App Template

## Project Overview
Complete Flutter authentication template for the Enugu Smart Bus booking application. Includes Login, Signup, Forgot Password, and Onboarding screens with modern UI design.

## Current Status
✅ **READY FOR LOCAL DEPLOYMENT**

The Flutter app template is fully implemented and ready to be deployed on your local machine. Due to Replit resource constraints with Flutter SDK, this project is optimized for local development.

## What's Included

### Screens
1. **Splash Screen** (`lib/presentation/splash_screen/`)
   - Animated startup screen
   - Enugu Smart Bus branding
   - Initialization logic with loading states

2. **Welcome/Onboarding** (`lib/presentation/welcome_onboarding_screen/`)
   - Multi-slide introduction
   - Page indicators
   - Navigation controls

3. **Login Screen** (`lib/presentation/login_screen/`)
   - Email/password authentication
   - Form validation
   - Remember me option
   - Biometric login support (ready)
   - Mock credentials for testing

4. **Registration Screen** (`lib/presentation/registration_screen/`)
   - Multi-step signup process
   - Personal information form
   - Password setup
   - Profile image upload
   - Terms & privacy checkbox

5. **Forgot Password** (`lib/presentation/forgot_password_screen/`)
   - Password recovery flow
   - Email validation

### Design System
- **Primary Color**: Dark Blue (#1a237e) - Professional and trustworthy
- **Typography**: Inter font family via Google Fonts
- **Theme**: Light and dark mode support
- **Animations**: Smooth transitions and micro-interactions
- **Responsive**: Works on all screen sizes (using Sizer package)

### Project Structure
```
lib/
├── core/              # Central exports and utilities
├── presentation/      # All UI screens
├── routes/           # Navigation configuration
├── theme/            # App theming (light/dark)
├── widgets/          # Reusable custom widgets
└── main.dart         # App entry point
```

## Database Schema
The app is designed to integrate with this User table structure:

```sql
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_uuid VARCHAR(36) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone_number VARCHAR(20),
    profile_image_url VARCHAR(512),
    user_type ENUM('customer', 'expert', 'delivery_boy', 'admin') DEFAULT 'customer',
    tagline VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Local Setup Instructions

### Quick Start (5 Minutes)
1. **Install Flutter SDK**: https://docs.flutter.dev/get-started/install
2. **Download this project** to your local machine
3. **Install dependencies**: `flutter pub get`
4. **Enable web**: `flutter config --enable-web`
5. **Run the app**: `flutter run -d chrome`

### Detailed Instructions
See `LOCAL_SETUP_GUIDE.md` for comprehensive setup instructions

## Development Credentials (Testing Only)
```
Email: user@enugusmart.com
Password: password123
```
⚠️ **Remove before production!**

## Key Dependencies
- `sizer`: Responsive design system
- `google_fonts`: Typography (Inter font)
- `flutter_svg`: SVG icon support
- `fluttertoast`: Toast notifications
- `shared_preferences`: Local storage
- `image_picker`: Profile image upload
- `dio`: HTTP client for API calls
- `connectivity_plus`: Network monitoring

## Next Steps - Backend Integration

### API Endpoints Needed
1. `POST /api/auth/login` - User authentication
2. `POST /api/auth/register` - New user signup
3. `POST /api/auth/forgot-password` - Password recovery
4. `POST /api/auth/reset-password` - Password reset
5. `GET /api/auth/verify-token` - Token validation

### Files to Update for API Integration
- `lib/presentation/login_screen/login_screen.dart` (line 120-170)
- `lib/presentation/registration_screen/registration_screen.dart`
- `lib/presentation/forgot_password_screen/forgot_password_screen.dart`

Replace mock authentication with real API calls using Dio.

## Building for Production

### Android APK
```bash
flutter build apk --release
```

### iOS (macOS only)
```bash
flutter build ios --release
```

### Web
```bash
flutter build web --release
```

## Customization Guide

### Change App Name
Edit `pubspec.yaml`:
```yaml
name: your_app_name
```

### Change Primary Color
Edit `lib/theme/app_theme.dart`:
```dart
static const Color primaryLight = Color(0xFF1a237e); // Change this
```

### Update Logo
Replace `assets/images/img_app_logo.svg` with your logo

## Important Notes
- The app uses **portrait orientation only** (locked in main.dart)
- **Material Design 3** theming system
- **Google Fonts** (no local fonts needed)
- All assets in `assets/` and `assets/images/` directories
- Custom error handling implemented

## Documentation Files
- **README.md** - Complete project documentation
- **LOCAL_SETUP_GUIDE.md** - Quick start guide
- **pubspec.yaml** - Dependencies and configuration

## Recent Changes
- **2025-10-12**: Complete Flutter app template created
  - All authentication screens implemented
  - Theme system with dark blue primary color
  - Responsive design using Sizer
  - Documentation and setup guides added

## User Preferences
- **Framework**: Flutter/Dart
- **Design Style**: Modern, professional, Nigerian fintech-inspired
- **Color Scheme**: Dark blue primary (#1a237e)
- **Target Platforms**: Mobile (Android/iOS) and Web

## Support Resources
- Flutter Documentation: https://docs.flutter.dev/
- Stack Overflow: [flutter] tag
- Flutter Community: https://flutter.dev/community

---

**Status**: ✅ Template Complete - Ready for Local Deployment
**Last Updated**: October 12, 2025
