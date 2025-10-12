import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';

import '../../../core/app_export.dart';

class TermsPrivacyCheckbox extends StatefulWidget {
  final bool isAccepted;
  final Function(bool) onChanged;

  const TermsPrivacyCheckbox({
    Key? key,
    required this.isAccepted,
    required this.onChanged,
  }) : super(key: key);

  @override
  State<TermsPrivacyCheckbox> createState() => _TermsPrivacyCheckboxState();
}

class _TermsPrivacyCheckboxState extends State<TermsPrivacyCheckbox> {
  void _showTermsOfService() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppTheme.lightTheme.colorScheme.surface,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) => DraggableScrollableSheet(
        initialChildSize: 0.9,
        minChildSize: 0.5,
        maxChildSize: 0.95,
        expand: false,
        builder: (context, scrollController) => Container(
          padding: EdgeInsets.all(4.w),
          child: Column(
            children: [
              Container(
                width: 12.w,
                height: 0.5.h,
                decoration: BoxDecoration(
                  color: AppTheme.lightTheme.colorScheme.outline,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              SizedBox(height: 2.h),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Terms of Service',
                    style: AppTheme.lightTheme.textTheme.titleLarge,
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: CustomIconWidget(
                      iconName: 'close',
                      color: AppTheme.lightTheme.colorScheme.onSurface,
                      size: 24,
                    ),
                  ),
                ],
              ),
              SizedBox(height: 2.h),
              Expanded(
                child: SingleChildScrollView(
                  controller: scrollController,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildSection(
                        'Acceptance of Terms',
                        'By creating an account with Enugu Smart Bus, you agree to be bound by these Terms of Service. These terms apply to all users of our mobile application and services.',
                      ),
                      _buildSection(
                        'Service Description',
                        'Enugu Smart Bus provides a mobile platform for booking bus transportation services within Enugu and surrounding areas. Our service includes route planning, seat reservation, payment processing, and customer support.',
                      ),
                      _buildSection(
                        'User Responsibilities',
                        'Users must provide accurate information during registration, maintain the security of their account credentials, comply with all applicable laws, and use the service only for legitimate transportation needs.',
                      ),
                      _buildSection(
                        'Booking and Payment',
                        'All bookings are subject to availability. Payment must be completed at the time of booking. Cancellation and refund policies apply as outlined in our cancellation terms.',
                      ),
                      _buildSection(
                        'Privacy and Data Protection',
                        'We collect and process personal data in accordance with our Privacy Policy. User data is protected using industry-standard security measures and is used solely for service provision.',
                      ),
                      _buildSection(
                        'Limitation of Liability',
                        'Enugu Smart Bus shall not be liable for any indirect, incidental, or consequential damages arising from the use of our services. Our liability is limited to the amount paid for the specific service.',
                      ),
                      _buildSection(
                        'Modifications',
                        'We reserve the right to modify these terms at any time. Users will be notified of significant changes and continued use constitutes acceptance of modified terms.',
                      ),
                      SizedBox(height: 2.h),
                      Text(
                        'Last updated: October 12, 2025',
                        style:
                            AppTheme.lightTheme.textTheme.bodySmall?.copyWith(
                          color:
                              AppTheme.lightTheme.colorScheme.onSurfaceVariant,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showPrivacyPolicy() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppTheme.lightTheme.colorScheme.surface,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) => DraggableScrollableSheet(
        initialChildSize: 0.9,
        minChildSize: 0.5,
        maxChildSize: 0.95,
        expand: false,
        builder: (context, scrollController) => Container(
          padding: EdgeInsets.all(4.w),
          child: Column(
            children: [
              Container(
                width: 12.w,
                height: 0.5.h,
                decoration: BoxDecoration(
                  color: AppTheme.lightTheme.colorScheme.outline,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              SizedBox(height: 2.h),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Privacy Policy',
                    style: AppTheme.lightTheme.textTheme.titleLarge,
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: CustomIconWidget(
                      iconName: 'close',
                      color: AppTheme.lightTheme.colorScheme.onSurface,
                      size: 24,
                    ),
                  ),
                ],
              ),
              SizedBox(height: 2.h),
              Expanded(
                child: SingleChildScrollView(
                  controller: scrollController,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildSection(
                        'Information We Collect',
                        'We collect personal information you provide during registration including name, email, phone number, and profile photo. We also collect usage data, location information for route planning, and payment information for transaction processing.',
                      ),
                      _buildSection(
                        'How We Use Your Information',
                        'Your information is used to provide transportation services, process bookings and payments, send service notifications, improve our services, and ensure account security.',
                      ),
                      _buildSection(
                        'Information Sharing',
                        'We do not sell your personal information. We may share data with service providers, payment processors, and as required by law. All third parties are bound by confidentiality agreements.',
                      ),
                      _buildSection(
                        'Data Security',
                        'We implement industry-standard security measures including encryption, secure servers, and regular security audits. However, no system is completely secure, and we cannot guarantee absolute security.',
                      ),
                      _buildSection(
                        'Your Rights',
                        'You have the right to access, update, or delete your personal information. You can also opt-out of marketing communications and request data portability.',
                      ),
                      _buildSection(
                        'Cookies and Tracking',
                        'We use cookies and similar technologies to improve user experience, analyze usage patterns, and provide personalized services. You can control cookie settings in your browser.',
                      ),
                      _buildSection(
                        'Contact Information',
                        'For privacy-related questions or concerns, contact us at privacy@enugusmartbus.com or through our customer support channels.',
                      ),
                      SizedBox(height: 2.h),
                      Text(
                        'Last updated: October 12, 2025',
                        style:
                            AppTheme.lightTheme.textTheme.bodySmall?.copyWith(
                          color:
                              AppTheme.lightTheme.colorScheme.onSurfaceVariant,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSection(String title, String content) {
    return Padding(
      padding: EdgeInsets.only(bottom: 2.h),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: AppTheme.lightTheme.textTheme.titleMedium?.copyWith(
              fontWeight: FontWeight.w600,
            ),
          ),
          SizedBox(height: 1.h),
          Text(
            content,
            style: AppTheme.lightTheme.textTheme.bodyMedium,
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.all(4.w),
      decoration: BoxDecoration(
        color: AppTheme.lightTheme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: AppTheme.lightTheme.colorScheme.outline.withValues(alpha: 0.3),
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Checkbox(
            value: widget.isAccepted,
            onChanged: (value) => widget.onChanged(value ?? false),
            materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
          ),
          SizedBox(width: 2.w),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                RichText(
                  text: TextSpan(
                    style: AppTheme.lightTheme.textTheme.bodyMedium,
                    children: [
                      const TextSpan(text: 'I agree to the '),
                      WidgetSpan(
                        child: GestureDetector(
                          onTap: _showTermsOfService,
                          child: Text(
                            'Terms of Service',
                            style: AppTheme.lightTheme.textTheme.bodyMedium
                                ?.copyWith(
                              color: AppTheme.lightTheme.colorScheme.primary,
                              decoration: TextDecoration.underline,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ),
                      const TextSpan(text: ' and '),
                      WidgetSpan(
                        child: GestureDetector(
                          onTap: _showPrivacyPolicy,
                          child: Text(
                            'Privacy Policy',
                            style: AppTheme.lightTheme.textTheme.bodyMedium
                                ?.copyWith(
                              color: AppTheme.lightTheme.colorScheme.primary,
                              decoration: TextDecoration.underline,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: 0.5.h),
                Text(
                  'Required to create your account',
                  style: AppTheme.lightTheme.textTheme.bodySmall?.copyWith(
                    color: AppTheme.lightTheme.colorScheme.onSurfaceVariant,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
