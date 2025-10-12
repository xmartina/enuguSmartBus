import 'package:flutter/material.dart';
import '../presentation/splash_screen/splash_screen.dart';
import '../presentation/forgot_password_screen/forgot_password_screen.dart';
import '../presentation/login_screen/login_screen.dart';
import '../presentation/welcome_onboarding_screen/welcome_onboarding_screen.dart';
import '../presentation/registration_screen/registration_screen.dart';

class AppRoutes {
  // TODO: Add your routes here
  static const String initial = '/';
  static const String splash = '/splash-screen';
  static const String forgotPassword = '/forgot-password-screen';
  static const String login = '/login-screen';
  static const String welcomeOnboarding = '/welcome-onboarding-screen';
  static const String registration = '/registration-screen';

  static Map<String, WidgetBuilder> routes = {
    initial: (context) => const SplashScreen(),
    splash: (context) => const SplashScreen(),
    forgotPassword: (context) => const ForgotPasswordScreen(),
    login: (context) => const LoginScreen(),
    welcomeOnboarding: (context) => const WelcomeOnboardingScreen(),
    registration: (context) => const RegistrationScreen(),
    // TODO: Add your other routes here
  };
}
