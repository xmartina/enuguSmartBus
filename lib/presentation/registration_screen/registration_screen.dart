import 'dart:io';

import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';

import '../../core/app_export.dart';
import './widgets/password_form.dart';
import './widgets/personal_info_form.dart';
import './widgets/profile_image_upload.dart';
import './widgets/step_progress_indicator.dart';
import './widgets/terms_privacy_checkbox.dart';

class RegistrationScreen extends StatefulWidget {
  const RegistrationScreen({Key? key}) : super(key: key);

  @override
  State<RegistrationScreen> createState() => _RegistrationScreenState();
}

class _RegistrationScreenState extends State<RegistrationScreen> {
  final PageController _pageController = PageController();
  int _currentStep = 0;
  final int _totalSteps = 3;
  bool _isLoading = false;

  // Form keys
  final GlobalKey<FormState> _personalInfoFormKey = GlobalKey<FormState>();
  final GlobalKey<FormState> _passwordFormKey = GlobalKey<FormState>();

  // Controllers
  final TextEditingController _fullNameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController =
      TextEditingController();

  // State variables
  bool _isEmailChecking = false;
  String? _emailError;
  File? _selectedImage;
  bool _termsAccepted = false;

  // Mock user data for email checking
  final List<String> _existingEmails = [
    'john.doe@example.com',
    'jane.smith@gmail.com',
    'admin@enugusmartbus.com',
    'test@test.com',
  ];

  @override
  void initState() {
    super.initState();
    _phoneController.text = '234'; // Pre-fill with country code
  }

  Future<void> _checkEmailUniqueness(String email) async {
    setState(() {
      _isEmailChecking = true;
      _emailError = null;
    });

    // Simulate API call delay
    await Future.delayed(const Duration(seconds: 1));

    setState(() {
      _isEmailChecking = false;
      if (_existingEmails.contains(email.toLowerCase())) {
        _emailError = 'This email is already registered';
      }
    });
  }

  void _nextStep() {
    if (_currentStep < _totalSteps - 1) {
      if (_validateCurrentStep()) {
        setState(() {
          _currentStep++;
        });
        _pageController.nextPage(
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeInOut,
        );
      }
    } else {
      _createAccount();
    }
  }

  void _previousStep() {
    if (_currentStep > 0) {
      setState(() {
        _currentStep--;
      });
      _pageController.previousPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    }
  }

  bool _validateCurrentStep() {
    switch (_currentStep) {
      case 0:
        return _personalInfoFormKey.currentState?.validate() ?? false;
      case 1:
        return _passwordFormKey.currentState?.validate() ?? false;
      case 2:
        if (!_termsAccepted) {
          _showErrorSnackBar(
              'Please accept the Terms of Service and Privacy Policy');
          return false;
        }
        return true;
      default:
        return false;
    }
  }

  Future<void> _createAccount() async {
    if (!_validateCurrentStep()) return;

    setState(() {
      _isLoading = true;
    });

    try {
      // Simulate account creation API call
      await Future.delayed(const Duration(seconds: 2));

      // Show success message
      _showSuccessSnackBar('Account created successfully!');

      // Navigate to login screen after a short delay
      await Future.delayed(const Duration(seconds: 1));

      if (mounted) {
        Navigator.pushReplacementNamed(context, '/login-screen');
      }
    } catch (e) {
      _showErrorSnackBar('Failed to create account. Please try again.');
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  void _showErrorSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppTheme.lightTheme.colorScheme.error,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _showSuccessSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppTheme.lightTheme.colorScheme.secondary,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.lightTheme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: _currentStep > 0
            ? IconButton(
                onPressed: _previousStep,
                icon: CustomIconWidget(
                  iconName: 'arrow_back',
                  color: AppTheme.lightTheme.colorScheme.onSurface,
                  size: 24,
                ),
              )
            : IconButton(
                onPressed: () => Navigator.pop(context),
                icon: CustomIconWidget(
                  iconName: 'close',
                  color: AppTheme.lightTheme.colorScheme.onSurface,
                  size: 24,
                ),
              ),
        title: Text(
          'Create Account',
          style: AppTheme.lightTheme.textTheme.titleLarge?.copyWith(
            color: AppTheme.lightTheme.colorScheme.onSurface,
          ),
        ),
        centerTitle: true,
      ),
      body: Column(
        children: [
          // Progress Indicator
          StepProgressIndicator(
            currentStep: _currentStep,
            totalSteps: _totalSteps,
          ),

          // Form Content
          Expanded(
            child: PageView(
              controller: _pageController,
              physics: const NeverScrollableScrollPhysics(),
              children: [
                // Step 1: Personal Information
                _buildStepContent(
                  child: PersonalInfoForm(
                    fullNameController: _fullNameController,
                    emailController: _emailController,
                    phoneController: _phoneController,
                    formKey: _personalInfoFormKey,
                    onEmailChanged: _checkEmailUniqueness,
                    isEmailChecking: _isEmailChecking,
                    emailError: _emailError,
                  ),
                ),

                // Step 2: Password Creation
                _buildStepContent(
                  child: PasswordForm(
                    passwordController: _passwordController,
                    confirmPasswordController: _confirmPasswordController,
                    formKey: _passwordFormKey,
                  ),
                ),

                // Step 3: Profile Photo & Terms
                _buildStepContent(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      ProfileImageUpload(
                        onImageSelected: (image) {
                          setState(() {
                            _selectedImage = image;
                          });
                        },
                        selectedImage: _selectedImage,
                      ),
                      SizedBox(height: 4.h),
                      TermsPrivacyCheckbox(
                        isAccepted: _termsAccepted,
                        onChanged: (value) {
                          setState(() {
                            _termsAccepted = value;
                          });
                        },
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // Bottom Action Button
          Container(
            padding: EdgeInsets.all(4.w),
            child: Column(
              children: [
                SizedBox(
                  width: double.infinity,
                  height: 6.h,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _nextStep,
                    child: _isLoading
                        ? SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              color: AppTheme.lightTheme.colorScheme.onPrimary,
                            ),
                          )
                        : Text(
                            _currentStep == _totalSteps - 1
                                ? 'Create Account'
                                : 'Continue',
                            style: AppTheme.lightTheme.textTheme.titleMedium
                                ?.copyWith(
                              color: AppTheme.lightTheme.colorScheme.onPrimary,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                  ),
                ),
                SizedBox(height: 2.h),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      'Already have an account? ',
                      style: AppTheme.lightTheme.textTheme.bodyMedium?.copyWith(
                        color: AppTheme.lightTheme.colorScheme.onSurfaceVariant,
                      ),
                    ),
                    GestureDetector(
                      onTap: () => Navigator.pushReplacementNamed(
                          context, '/login-screen'),
                      child: Text(
                        'Sign In',
                        style:
                            AppTheme.lightTheme.textTheme.bodyMedium?.copyWith(
                          color: AppTheme.lightTheme.colorScheme.primary,
                          fontWeight: FontWeight.w600,
                          decoration: TextDecoration.underline,
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStepContent({required Widget child}) {
    return SingleChildScrollView(
      padding: EdgeInsets.symmetric(horizontal: 4.w, vertical: 2.h),
      child: child,
    );
  }

  @override
  void dispose() {
    _pageController.dispose();
    _fullNameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }
}
