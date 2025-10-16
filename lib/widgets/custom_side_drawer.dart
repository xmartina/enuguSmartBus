import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../core/app_export.dart';
import 'custom_icon_widget.dart';

class CustomSideDrawer extends StatelessWidget {
  const CustomSideDrawer({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Drawer(
      child: Container(
        color: theme.scaffoldBackgroundColor,
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildDrawerHeader(context, theme),
              Expanded(
                child: ListView(
                  padding: EdgeInsets.zero,
                  children: [
                    _buildDrawerItem(
                      context: context,
                      theme: theme,
                      icon: 'home',
                      title: 'Home',
                      onTap: () {
                        Navigator.pop(context);
                      },
                    ),
                    _buildDrawerItem(
                      context: context,
                      theme: theme,
                      icon: 'confirmation_number',
                      title: 'My Tickets',
                      onTap: () {
                        Navigator.pop(context);
                        Navigator.pushNamed(context, '/tickets-list-screen');
                      },
                    ),
                    _buildDrawerItem(
                      context: context,
                      theme: theme,
                      icon: 'settings',
                      title: 'Settings',
                      onTap: () {
                        Navigator.pop(context);
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            content: const Text('Settings feature coming soon.'),
                            backgroundColor: theme.colorScheme.primary,
                          ),
                        );
                      },
                    ),
                    _buildDrawerItem(
                      context: context,
                      theme: theme,
                      icon: 'help_outline',
                      title: 'Help & Support',
                      onTap: () {
                        Navigator.pop(context);
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            content: const Text('Help & Support feature coming soon.'),
                            backgroundColor: theme.colorScheme.primary,
                          ),
                        );
                      },
                    ),
                    Divider(
                      height: 1,
                      thickness: 1,
                      color: theme.colorScheme.outlineVariant,
                      indent: 4.w,
                      endIndent: 4.w,
                    ),
                    SizedBox(height: 1.h),
                    _buildDrawerItem(
                      context: context,
                      theme: theme,
                      icon: 'exit_to_app',
                      title: 'Logout',
                      onTap: () {
                        Navigator.pop(context);
                        _showLogoutDialog(context, theme);
                      },
                      isDestructive: true,
                    ),
                  ],
                ),
              ),
              _buildDrawerFooter(context, theme),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDrawerHeader(BuildContext context, ThemeData theme) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 4.w, vertical: 3.h),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            theme.colorScheme.primary,
            theme.colorScheme.primaryContainer,
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 20.w,
            height: 20.w,
            decoration: BoxDecoration(
              color: theme.colorScheme.surface,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Padding(
              padding: EdgeInsets.all(2.w),
              child: Image.asset(
                'assets/images/logo.png',
                fit: BoxFit.contain,
              ),
            ),
          ),
          SizedBox(height: 2.h),
          Text(
            'Enugu Smart Bus',
            style: theme.textTheme.titleMedium?.copyWith(
              color: theme.colorScheme.onPrimary,
              fontWeight: FontWeight.w600,
              fontSize: 16.sp,
            ),
          ),
          SizedBox(height: 0.5.h),
          Text(
            'user@enugusmartbus.com',
            style: theme.textTheme.bodySmall?.copyWith(
              color: theme.colorScheme.onPrimary.withOpacity(0.8),
              fontSize: 11.sp,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDrawerItem({
    required BuildContext context,
    required ThemeData theme,
    required String icon,
    required String title,
    required VoidCallback onTap,
    bool isDestructive = false,
  }) {
    return ListTile(
      contentPadding: EdgeInsets.symmetric(horizontal: 4.w, vertical: 0.5.h),
      leading: CustomIconWidget(
        iconName: icon,
        color: isDestructive
            ? theme.colorScheme.error
            : theme.colorScheme.onSurfaceVariant,
        size: 20,
      ),
      title: Text(
        title,
        style: theme.textTheme.bodyMedium?.copyWith(
          color: isDestructive
              ? theme.colorScheme.error
              : theme.colorScheme.onSurface,
          fontWeight: FontWeight.w500,
          fontSize: 14.sp,
        ),
      ),
      trailing: CustomIconWidget(
        iconName: 'chevron_right',
        color: theme.colorScheme.onSurfaceVariant.withOpacity(0.5),
        size: 18,
      ),
      onTap: onTap,
    );
  }

  Widget _buildDrawerFooter(BuildContext context, ThemeData theme) {
    return Container(
      padding: EdgeInsets.all(4.w),
      decoration: BoxDecoration(
        border: Border(
          top: BorderSide(
            color: theme.colorScheme.outlineVariant,
            width: 1,
          ),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Version 1.0.0',
            style: theme.textTheme.bodySmall?.copyWith(
              color: theme.colorScheme.onSurfaceVariant,
              fontSize: 10.sp,
            ),
          ),
          SizedBox(height: 0.5.h),
          Text(
            '\u00A9 2025 Enugu Smart Bus',
            style: theme.textTheme.bodySmall?.copyWith(
              color: theme.colorScheme.onSurfaceVariant,
              fontSize: 10.sp,
            ),
          ),
        ],
      ),
    );
  }

  void _showLogoutDialog(BuildContext context, ThemeData theme) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(
            'Logout',
            style: theme.textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.w600,
            ),
          ),
          content: Text(
            'Are you sure you want to logout?',
            style: theme.textTheme.bodyMedium,
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text(
                'Cancel',
                style: theme.textTheme.labelLarge?.copyWith(
                  color: theme.colorScheme.onSurfaceVariant,
                ),
              ),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
                Navigator.pushReplacementNamed(context, '/login-screen');
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: theme.colorScheme.error,
              ),
              child: Text(
                'Logout',
                style: theme.textTheme.labelLarge?.copyWith(
                  color: theme.colorScheme.onError,
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}
