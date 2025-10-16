import 'package:flutter/material.dart';
import '../presentation/splash_screen/splash_screen.dart';
import '../presentation/forgot_password_screen/forgot_password_screen.dart';
import '../presentation/login_screen/login_screen.dart';
import '../presentation/welcome_onboarding_screen/welcome_onboarding_screen.dart';
import '../presentation/registration_screen/registration_screen.dart';
import '../presentation/home_screen/home_screen.dart';
import '../presentation/trip_list_screen/trip_list_screen.dart';
import '../presentation/seat_selection_screen/seat_selection_screen.dart';
import '../presentation/booking_summary_screen/booking_summary_screen.dart';

class AppRoutes {
  static const String initial = '/';
  static const String splash = '/splash-screen';
  static const String forgotPassword = '/forgot-password-screen';
  static const String login = '/login-screen';
  static const String welcomeOnboarding = '/welcome-onboarding-screen';
  static const String registration = '/registration-screen';
  static const String home = '/home-screen';
  static const String tripList = '/trip-list-screen';
  static const String seatSelection = '/seat-selection-screen';
  static const String bookingSummary = '/booking-summary-screen';

  static Map<String, WidgetBuilder> routes = {
    initial: (context) => const SplashScreen(),
    splash: (context) => const SplashScreen(),
    forgotPassword: (context) => const ForgotPasswordScreen(),
    login: (context) => const LoginScreen(),
    welcomeOnboarding: (context) => const WelcomeOnboardingScreen(),
    registration: (context) => const RegistrationScreen(),
    home: (context) => const HomeScreen(),
    tripList: (context) => const TripListScreen(),
    seatSelection: (context) => const SeatSelectionScreen(),
    bookingSummary: (context) => const BookingSummaryScreen(),
  };
}
