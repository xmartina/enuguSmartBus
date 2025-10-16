# Enugu Smart Bus - Flutter App Template

## Overview

The Enugu Smart Bus project is a complete Flutter application template designed for bus ticketing and travel management. It aims to provide a production-ready foundation with full authentication flows, intuitive navigation, and key features for searching, selecting, and booking bus trips. The project emphasizes a modern, responsive UI/UX following Material Design 3 principles and is optimized for local deployment and build processes on Android and iOS platforms. The business vision is to provide a robust, scalable, and user-friendly mobile solution for public transportation, enhancing the travel experience for commuters and offering potential for market expansion within the transportation sector.

## User Preferences

I want iterative development.
I prefer detailed explanations.
Ask before making major changes.
Do not make changes to the folder `Z`.
Do not make changes to the file `Y`.

## System Architecture

### UI/UX Decisions
The application adheres to Material Design 3 guidelines, featuring a dark blue primary color (`#1a237e`) for a professional and trustworthy aesthetic. Typography is handled by the Inter font family via Google Fonts. The design is fully responsive across various screen sizes using the `sizer` package, and includes smooth animations and micro-interactions for an enhanced user experience.

### Technical Implementations
The core application is built with Flutter. It includes comprehensive authentication screens (Splash, Welcome/Onboarding, Login, Registration, Forgot Password) and universal navigation components like a custom floating bottom navigation bar and a custom side drawer. Key application screens such as Home, Trip List, and Seat Selection are implemented, with the Seat Selection screen featuring an interactive 2x1 seat layout and real-time state management for seat selection. The application structure separates concerns into `core/`, `presentation/`, `routes/`, `theme/`, and `widgets/` directories.

### Feature Specifications
- **Authentication:** Complete user authentication flow with email/password and biometric readiness. Multi-step registration with profile image upload.
- **Navigation:** Custom scroll-responsive floating bottom navigation bar with 4 tabs (Book Trip, My Tickets, Luggage, Profile) and a professional side drawer with configurable menu items.
- **Home Screen:** Enhanced trip search card with departure/destination inputs, date picker, passenger counter, popular routes section, and agent/loyalty promotion card.
- **Trip List Screen:** Displays search summaries, filter/sort options (price, departure time, bus type, facilities), and detailed trip result cards.
- **Seat Selection Screen:** Interactive seat layout with real-time selection, dynamic booking summary, and fare breakdown.
- **Theming:** Light/dark mode support with easily customizable primary color.

### System Design Choices
The project provides automated setup scripts for Linux/macOS (`setup_flutter_project.sh`) and Windows (`setup_flutter_project.bat`) to generate platform folders and install dependencies, ensuring a quick local development environment setup. A robust `.gitignore` is configured to track only essential source code and configurations, ignoring build artifacts and generated platform files. The architecture is designed to be production-ready, focusing on clean code and maintainability.

## External Dependencies

- `sizer`: For responsive UI design.
- `google_fonts`: To integrate custom typography (Inter font).
- `flutter_svg`: For displaying SVG assets.
- `fluttertoast`: For toast notifications.
- `shared_preferences`: For local data storage.
- `image_picker`: For profile image selection and upload.
- `dio`: HTTP client for API integration.
- `connectivity_plus`: For monitoring network connectivity.