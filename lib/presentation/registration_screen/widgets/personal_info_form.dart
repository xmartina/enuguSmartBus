import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:sizer/sizer.dart';

import '../../../core/app_export.dart';

class PersonalInfoForm extends StatefulWidget {
  final TextEditingController fullNameController;
  final TextEditingController emailController;
  final TextEditingController phoneController;
  final GlobalKey<FormState> formKey;
  final Function(String) onEmailChanged;
  final bool isEmailChecking;
  final String? emailError;

  const PersonalInfoForm({
    Key? key,
    required this.fullNameController,
    required this.emailController,
    required this.phoneController,
    required this.formKey,
    required this.onEmailChanged,
    required this.isEmailChecking,
    this.emailError,
  }) : super(key: key);

  @override
  State<PersonalInfoForm> createState() => _PersonalInfoFormState();
}

class _PersonalInfoFormState extends State<PersonalInfoForm> {
  bool _isFullNameValid = false;
  bool _isEmailValid = false;
  bool _isPhoneValid = false;

  @override
  void initState() {
    super.initState();
    widget.fullNameController.addListener(_validateFullName);
    widget.emailController.addListener(_validateEmail);
    widget.phoneController.addListener(_validatePhone);
  }

  void _validateFullName() {
    setState(() {
      _isFullNameValid = widget.fullNameController.text.trim().length >= 2 &&
          widget.fullNameController.text.trim().contains(' ');
    });
  }

  void _validateEmail() {
    final email = widget.emailController.text.trim();
    setState(() {
      _isEmailValid =
          RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}\$').hasMatch(email);
    });

    if (_isEmailValid) {
      widget.onEmailChanged(email);
    }
  }

  void _validatePhone() {
    final phone = widget.phoneController.text.replaceAll(RegExp(r'[^\d]'), '');
    setState(() {
      _isPhoneValid = phone.length >= 10 && phone.startsWith('234');
    });
  }

  @override
  Widget build(BuildContext context) {
    return Form(
      key: widget.formKey,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Personal Information',
            style: AppTheme.lightTheme.textTheme.headlineSmall?.copyWith(
              fontWeight: FontWeight.w600,
              color: AppTheme.lightTheme.colorScheme.onSurface,
            ),
          ),
          SizedBox(height: 1.h),
          Text(
            'Please provide your personal details to create your account',
            style: AppTheme.lightTheme.textTheme.bodyMedium?.copyWith(
              color: AppTheme.lightTheme.colorScheme.onSurfaceVariant,
            ),
          ),
          SizedBox(height: 3.h),

          // Full Name Field
          TextFormField(
            controller: widget.fullNameController,
            textInputAction: TextInputAction.next,
            keyboardType: TextInputType.name,
            textCapitalization: TextCapitalization.words,
            decoration: InputDecoration(
              labelText: 'Full Name',
              hintText: 'Enter your full name',
              prefixIcon: Padding(
                padding: EdgeInsets.all(3.w),
                child: CustomIconWidget(
                  iconName: 'person',
                  color: _isFullNameValid
                      ? AppTheme.lightTheme.colorScheme.primary
                      : AppTheme.lightTheme.colorScheme.onSurfaceVariant,
                  size: 20,
                ),
              ),
              suffixIcon: _isFullNameValid
                  ? Padding(
                      padding: EdgeInsets.all(3.w),
                      child: CustomIconWidget(
                        iconName: 'check_circle',
                        color: AppTheme.lightTheme.colorScheme.secondary,
                        size: 20,
                      ),
                    )
                  : null,
            ),
            validator: (value) {
              if (value == null || value.trim().isEmpty) {
                return 'Full name is required';
              }
              if (value.trim().length < 2) {
                return 'Name must be at least 2 characters';
              }
              if (!value.trim().contains(' ')) {
                return 'Please enter your full name (first and last name)';
              }
              return null;
            },
          ),
          SizedBox(height: 2.h),

          // Email Field
          TextFormField(
            controller: widget.emailController,
            textInputAction: TextInputAction.next,
            keyboardType: TextInputType.emailAddress,
            decoration: InputDecoration(
              labelText: 'Email Address',
              hintText: 'Enter your email address',
              prefixIcon: Padding(
                padding: EdgeInsets.all(3.w),
                child: CustomIconWidget(
                  iconName: 'email',
                  color: _isEmailValid
                      ? AppTheme.lightTheme.colorScheme.primary
                      : AppTheme.lightTheme.colorScheme.onSurfaceVariant,
                  size: 20,
                ),
              ),
              suffixIcon: widget.isEmailChecking
                  ? Padding(
                      padding: EdgeInsets.all(3.w),
                      child: SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: AppTheme.lightTheme.colorScheme.primary,
                        ),
                      ),
                    )
                  : _isEmailValid && widget.emailError == null
                      ? Padding(
                          padding: EdgeInsets.all(3.w),
                          child: CustomIconWidget(
                            iconName: 'check_circle',
                            color: AppTheme.lightTheme.colorScheme.secondary,
                            size: 20,
                          ),
                        )
                      : null,
              errorText: widget.emailError,
            ),
            validator: (value) {
              if (value == null || value.trim().isEmpty) {
                return 'Email address is required';
              }
              if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}\$')
                  .hasMatch(value.trim())) {
                return 'Please enter a valid email address';
              }
              return null;
            },
          ),
          SizedBox(height: 2.h),

          // Phone Number Field
          TextFormField(
            controller: widget.phoneController,
            textInputAction: TextInputAction.done,
            keyboardType: TextInputType.phone,
            inputFormatters: [
              FilteringTextInputFormatter.digitsOnly,
              LengthLimitingTextInputFormatter(13),
            ],
            decoration: InputDecoration(
              labelText: 'Phone Number',
              hintText: '2348012345678',
              prefixIcon: Padding(
                padding: EdgeInsets.all(3.w),
                child: CustomIconWidget(
                  iconName: 'phone',
                  color: _isPhoneValid
                      ? AppTheme.lightTheme.colorScheme.primary
                      : AppTheme.lightTheme.colorScheme.onSurfaceVariant,
                  size: 20,
                ),
              ),
              suffixIcon: _isPhoneValid
                  ? Padding(
                      padding: EdgeInsets.all(3.w),
                      child: CustomIconWidget(
                        iconName: 'check_circle',
                        color: AppTheme.lightTheme.colorScheme.secondary,
                        size: 20,
                      ),
                    )
                  : null,
              helperText: 'Format: 2348012345678 (with country code)',
            ),
            validator: (value) {
              if (value == null || value.trim().isEmpty) {
                return 'Phone number is required';
              }
              final phone = value.replaceAll(RegExp(r'[^\d]'), '');
              if (phone.length < 10) {
                return 'Phone number must be at least 10 digits';
              }
              if (!phone.startsWith('234')) {
                return 'Please include Nigerian country code (234)';
              }
              return null;
            },
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    widget.fullNameController.removeListener(_validateFullName);
    widget.emailController.removeListener(_validateEmail);
    widget.phoneController.removeListener(_validatePhone);
    super.dispose();
  }
}
