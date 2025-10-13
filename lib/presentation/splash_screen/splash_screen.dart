import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:sizer/sizer.dart';

import '../../core/app_export.dart';
import '../welcome_onboarding_screen/welcome_onboarding_screen.dart';
import '../login_screen/login_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with TickerProviderStateMixin {
  late AnimationController _logoAnimationController;
  late AnimationController _loadingAnimationController;
  late Animation<double> _logoScaleAnimation;
  late Animation<double> _logoFadeAnimation;
  late Animation<double> _loadingFadeAnimation;

  bool _isInitialized = false;
  bool _showRetryOption = false;

  @override
  void initState() {
    super.initState();
    _setupAnimations();
    _initializeApp();
  }

  void _setupAnimations() {
    // Logo animation controller
    _logoAnimationController = AnimationController(
      duration: const Duration(milliseconds: 1500),
      vsync: this,
    );

    // Loading animation controller
    _loadingAnimationController = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );

    // Logo scale animation
    _logoScaleAnimation = Tween<double>(
      begin: 0.8,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _logoAnimationController,
      curve: Curves.elasticOut,
    ));

    // Logo fade animation
    _logoFadeAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _logoAnimationController,
      curve: const Interval(0.0, 0.6, curve: Curves.easeIn),
    ));

    // Loading fade animation
    _loadingFadeAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _loadingAnimationController,
      curve: Curves.easeIn,
    ));

    // Start logo animation
    _logoAnimationController.forward();
  }

  Future<void> _initializeApp() async {
    try {
      // Set system UI overlay style
      SystemChrome.setSystemUIOverlayStyle(
        SystemUiOverlayStyle(
          statusBarColor: AppTheme.lightTheme.primaryColor,
          statusBarIconBrightness: Brightness.light,
          systemNavigationBarColor: AppTheme.lightTheme.primaryColor,
          systemNavigationBarIconBrightness: Brightness.light,
        ),
      );

      // Wait for logo animation to complete
      await _logoAnimationController.forward();

      // Start loading animation
      _loadingAnimationController.forward();

      // Simulate initialization tasks
      await Future.wait([
        _checkAuthenticationToken(),
        _loadUserPreferences(),
        _fetchEssentialConfiguration(),
        _prepareCachedData(),
      ]);

      setState(() {
        _isInitialized = true;
      });

      // Wait minimum splash time
      await Future.delayed(const Duration(milliseconds: 500));

      // Navigate based on authentication status
      _navigateToNextScreen();
    } catch (e) {
      // Handle initialization error
      _handleInitializationError();
    }
  }

  Future<void> _checkAuthenticationToken() async {
    // Simulate checking authentication token validity
    await Future.delayed(const Duration(milliseconds: 800));
    // In real implementation, check stored token validity
  }

  Future<void> _loadUserPreferences() async {
    // Simulate loading user preferences
    await Future.delayed(const Duration(milliseconds: 600));
    // In real implementation, load from SharedPreferences
  }

  Future<void> _fetchEssentialConfiguration() async {
    // Simulate fetching essential app configuration
    await Future.delayed(const Duration(milliseconds: 700));
    // In real implementation, fetch from API with timeout
  }

  Future<void> _prepareCachedData() async {
    // Simulate preparing cached data
    await Future.delayed(const Duration(milliseconds: 500));
    // In real implementation, prepare offline data
  }

  void _handleInitializationError() {
    setState(() {
      _showRetryOption = true;
    });

    // Show retry option after 5 seconds
    Future.delayed(const Duration(seconds: 5), () {
      if (mounted && !_isInitialized) {
        setState(() {
          _showRetryOption = true;
        });
      }
    });
  }

  void _navigateToNextScreen() {
    if (!mounted) return;

    // Determine navigation based on user state
    final bool isAuthenticated = false; // TODO: Check actual authentication status
    final bool isFirstTime = true; // TODO: Check if first time user from SharedPreferences

    // Navigate to appropriate screen with fade transition
    Navigator.of(context).pushReplacement(
      PageRouteBuilder(
        pageBuilder: (context, animation, secondaryAnimation) {
          if (isAuthenticated) {
            // TODO: Navigate to dashboard/home screen when implemented
            return const LoginScreen();
          } else if (isFirstTime) {
            return const WelcomeOnboardingScreen();
          } else {
            return const LoginScreen();
          }
        },
        transitionsBuilder: (context, animation, secondaryAnimation, child) {
          return FadeTransition(
            opacity: animation,
            child: child,
          );
        },
        transitionDuration: const Duration(milliseconds: 500),
      ),
    );
  }

  void _retryInitialization() {
    setState(() {
      _showRetryOption = false;
      _isInitialized = false;
    });

    // Reset animations
    _logoAnimationController.reset();
    _loadingAnimationController.reset();

    // Restart initialization
    _initializeApp();
  }

  @override
  void dispose() {
    _logoAnimationController.dispose();
    _loadingAnimationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        height: double.infinity,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              AppTheme.lightTheme.primaryColor,
              AppTheme.lightTheme.primaryColor.withOpacity(0.8),
              AppTheme.lightTheme.colorScheme.primaryContainer,
            ],
          ),
        ),
        child: SafeArea(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Logo section
              Expanded(
                flex: 3,
                child: Center(
                  child: AnimatedBuilder(
                    animation: _logoAnimationController,
                    builder: (context, child) {
                      return FadeTransition(
                        opacity: _logoFadeAnimation,
                        child: ScaleTransition(
                          scale: _logoScaleAnimation,
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              // App logo
                              Container(
                                width: 25.w,
                                height: 25.w,
                                decoration: BoxDecoration(
                                  color:
                                      AppTheme.lightTheme.colorScheme.surface,
                                  borderRadius: BorderRadius.circular(4.w),
                                  boxShadow: [
                                    BoxShadow(
                                      color:
                                          Colors.black.withOpacity(0.2),
                                      blurRadius: 20,
                                      offset: const Offset(0, 10),
                                    ),
                                  ],
                                ),
                                child: Center(
                                  child: CustomIconWidget(
                                    iconName: 'directions_bus',
                                    color: AppTheme.lightTheme.primaryColor,
                                    size: 12.w,
                                  ),
                                ),
                              ),
                              SizedBox(height: 3.h),
                              // App name
                              Text(
                                'Enugu Smart Bus',
                                style: AppTheme
                                    .lightTheme.textTheme.headlineMedium
                                    ?.copyWith(
                                  color:
                                      AppTheme.lightTheme.colorScheme.onPrimary,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: 1.2,
                                ),
                                textAlign: TextAlign.center,
                              ),
                              SizedBox(height: 1.h),
                              // Tagline
                              Text(
                                'Smart Transportation Solutions',
                                style: AppTheme.lightTheme.textTheme.bodyLarge
                                    ?.copyWith(
                                  color: AppTheme
                                      .lightTheme.colorScheme.onPrimary
                                      .withOpacity(0.8),
                                  letterSpacing: 0.5,
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
              ),
              // Loading section
              Expanded(
                flex: 1,
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    AnimatedBuilder(
                      animation: _loadingAnimationController,
                      builder: (context, child) {
                        return FadeTransition(
                          opacity: _loadingFadeAnimation,
                          child: Column(
                            children: [
                              // Loading indicator
                              _showRetryOption
                                  ? _buildRetrySection()
                                  : _buildLoadingIndicator(),
                              SizedBox(height: 2.h),
                              // Loading text
                              Text(
                                _showRetryOption
                                    ? 'Connection timeout. Please try again.'
                                    : _isInitialized
                                        ? 'Ready to go!'
                                        : 'Initializing...',
                                style: AppTheme.lightTheme.textTheme.bodyMedium
                                    ?.copyWith(
                                  color: AppTheme
                                      .lightTheme.colorScheme.onPrimary
                                      .withOpacity(0.7),
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        );
                      },
                    ),
                  ],
                ),
              ),
              // Version info
              Padding(
                padding: EdgeInsets.only(bottom: 2.h),
                child: Text(
                  'Version 1.0.0',
                  style: AppTheme.lightTheme.textTheme.bodySmall?.copyWith(
                    color: AppTheme.lightTheme.colorScheme.onPrimary
                        .withOpacity(0.5),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLoadingIndicator() {
    return SizedBox(
      width: 6.w,
      height: 6.w,
      child: CircularProgressIndicator(
        strokeWidth: 3,
        valueColor: AlwaysStoppedAnimation<Color>(
          AppTheme.lightTheme.colorScheme.onPrimary,
        ),
      ),
    );
  }

  Widget _buildRetrySection() {
    return Column(
      children: [
        CustomIconWidget(
          iconName: 'refresh',
          color: AppTheme.lightTheme.colorScheme.onPrimary,
          size: 8.w,
        ),
        SizedBox(height: 2.h),
        ElevatedButton(
          onPressed: _retryInitialization,
          style: ElevatedButton.styleFrom(
            backgroundColor: AppTheme.lightTheme.colorScheme.surface,
            foregroundColor: AppTheme.lightTheme.primaryColor,
            padding: EdgeInsets.symmetric(horizontal: 8.w, vertical: 1.5.h),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(2.w),
            ),
          ),
          child: Text(
            'Retry',
            style: AppTheme.lightTheme.textTheme.labelLarge?.copyWith(
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
}
