import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:sizer/sizer.dart';

import '../../core/app_export.dart';
import '../../theme/app_theme.dart';
import './widgets/onboarding_navigation_widget.dart';
import './widgets/onboarding_slide_widget.dart';
import './widgets/page_indicator_widget.dart';

class WelcomeOnboardingScreen extends StatefulWidget {
  const WelcomeOnboardingScreen({Key? key}) : super(key: key);

  @override
  State<WelcomeOnboardingScreen> createState() =>
      _WelcomeOnboardingScreenState();
}

class _WelcomeOnboardingScreenState extends State<WelcomeOnboardingScreen>
    with TickerProviderStateMixin {
  late PageController _pageController;
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;
  int _currentIndex = 0;

  // Mock onboarding data
  final List<Map<String, dynamic>> _onboardingData = [
    {
      "title": "Easy Bus Booking",
      "description":
          "Book your bus tickets in seconds with our simple and intuitive interface. Choose your route, select your seat, and you're ready to go!",
      "image":
          "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YnVzfGVufDB8fDB8fHww",
    },
    {
      "title": "Secure Payments",
      "description":
          "Pay safely with multiple payment options including bank transfers, cards, and mobile money. All transactions are secured with bank-level encryption.",
      "image":
          "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8cGF5bWVudHxlbnwwfHwwfHx8MA%3D%3D",
    },
    {
      "title": "Real-Time Tracking",
      "description":
          "Track your bus location in real-time and get live updates on arrival times. Never miss your bus again with our smart notification system.",
      "image":
          "https://images.unsplash.com/photo-1551288049-bebda4e38f71?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8dHJhY2tpbmd8ZW58MHx8MHx8fDA%3D",
    },
    {
      "title": "Trusted Local Service",
      "description":
          "Proudly serving Enugu and surrounding regions with reliable transportation. Join thousands of satisfied customers who trust Enugu Smart Bus.",
      "image":
          "https://images.unsplash.com/photo-1449824913935-59a10b8d2000?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8bmlnZXJpYXxlbnwwfHwwfHx8MA%3D%3D",
    },
  ];

  @override
  void initState() {
    super.initState();
    _pageController = PageController();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );
    _fadeAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    ));

    _animationController.forward();
  }

  @override
  void dispose() {
    _pageController.dispose();
    _animationController.dispose();
    super.dispose();
  }

  void _onPageChanged(int index) {
    setState(() {
      _currentIndex = index;
    });

    // Haptic feedback for iOS
    if (Theme.of(context).platform == TargetPlatform.iOS) {
      HapticFeedback.lightImpact();
    }
  }

  void _nextPage() {
    if (_currentIndex < _onboardingData.length - 1) {
      _pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    }
  }

  void _skipToEnd() {
    _pageController.animateToPage(
      _onboardingData.length - 1,
      duration: const Duration(milliseconds: 500),
      curve: Curves.easeInOut,
    );
  }

  Future<void> _completeOnboarding() async {
    try {
      // Store onboarding completion flag
      final prefs = await SharedPreferences.getInstance();
      await prefs.setBool('hasCompletedOnboarding', true);

      // Navigate to registration screen
      if (mounted) {
        Navigator.pushReplacementNamed(context, '/registration-screen');
      }
    } catch (e) {
      // Handle error gracefully
      if (mounted) {
        Navigator.pushReplacementNamed(context, '/registration-screen');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.lightTheme.scaffoldBackgroundColor,
      body: SafeArea(
        child: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [
                AppTheme.lightTheme.colorScheme.primary.withOpacity(0.05),
                AppTheme.lightTheme.scaffoldBackgroundColor,
                AppTheme.lightTheme.scaffoldBackgroundColor,
              ],
            ),
          ),
          child: FadeTransition(
            opacity: _fadeAnimation,
            child: Column(
              children: [
                // Skip button in top right
                if (_currentIndex < _onboardingData.length - 1)
                  Container(
                    width: double.infinity,
                    padding:
                        EdgeInsets.symmetric(horizontal: 6.w, vertical: 2.h),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        TextButton(
                          onPressed: _skipToEnd,
                          style: TextButton.styleFrom(
                            foregroundColor: AppTheme
                                .lightTheme.colorScheme.primary
                                .withOpacity(0.7),
                            padding: EdgeInsets.symmetric(
                                horizontal: 3.w, vertical: 1.h),
                          ),
                          child: Text(
                            'Skip',
                            style: AppTheme.lightTheme.textTheme.titleSmall
                                ?.copyWith(
                              color: AppTheme.lightTheme.colorScheme.primary
                                  .withOpacity(0.7),
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ],
                    ),
                  )
                else
                  SizedBox(height: 6.h),

                // PageView with slides
                Expanded(
                  child: PageView.builder(
                    controller: _pageController,
                    onPageChanged: _onPageChanged,
                    itemCount: _onboardingData.length,
                    itemBuilder: (context, index) {
                      final slideData =
                          _onboardingData[index];
                      return OnboardingSlideWidget(
                        imageUrl: slideData["image"] as String,
                        title: slideData["title"] as String,
                        description: slideData["description"] as String,
                      );
                    },
                  ),
                ),

                // Page indicator
                Container(
                  padding: EdgeInsets.symmetric(vertical: 2.h),
                  child: PageIndicatorWidget(
                    currentIndex: _currentIndex,
                    totalPages: _onboardingData.length,
                  ),
                ),

                // Navigation buttons
                OnboardingNavigationWidget(
                  currentIndex: _currentIndex,
                  totalPages: _onboardingData.length,
                  onNext: _nextPage,
                  onGetStarted: _completeOnboarding,
                  onSkip: _skipToEnd,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
