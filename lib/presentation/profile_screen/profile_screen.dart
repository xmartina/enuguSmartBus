import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';
import '../login_screen/login_screen.dart';
import '../tickets_list_screen/tickets_list_screen.dart';
import '../agent_dashboard_screen/agent_dashboard_screen.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  void _showEditProfileDialog(BuildContext context) {
    final theme = Theme.of(context);
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(
          'Edit Profile',
          style: theme.textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.bold,
          ),
        ),
        content: Text(
          'Profile editing feature coming soon.',
          style: theme.textTheme.bodyMedium,
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text(
              'OK',
              style: theme.textTheme.labelLarge?.copyWith(
                color: theme.colorScheme.primary,
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _showAboutDialog(BuildContext context) {
    final theme = Theme.of(context);
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Row(
          children: [
            Icon(
              Icons.info_outline,
              color: theme.colorScheme.primary,
              size: 24,
            ),
            SizedBox(width: 2.w),
            Text(
              'About Us',
              style: theme.textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Enugu Smart Bus',
              style: theme.textTheme.titleSmall?.copyWith(
                fontWeight: FontWeight.bold,
                color: theme.colorScheme.primary,
              ),
            ),
            SizedBox(height: 1.h),
            Text(
              'Version 1.0.0',
              style: theme.textTheme.bodySmall,
            ),
            SizedBox(height: 1.5.h),
            Text(
              'Your trusted partner for bus ticketing and travel management.',
              style: theme.textTheme.bodyMedium,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text(
              'CLOSE',
              style: theme.textTheme.labelLarge?.copyWith(
                color: theme.colorScheme.primary,
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _handleLogout(BuildContext context) {
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(builder: (context) => const LoginScreen()),
      (route) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: theme.colorScheme.primary,
        elevation: 2,
        title: Text(
          'My Profile',
          style: theme.textTheme.titleLarge?.copyWith(
            color: theme.colorScheme.onPrimary,
            fontWeight: FontWeight.w600,
          ),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            _buildIdentityCard(context, theme),
            SizedBox(height: 2.h),
            _buildAccountManagementSection(context, theme),
            SizedBox(height: 1.h),
            _buildGeneralSupportSection(context, theme),
            SizedBox(height: 2.h),
            _buildLogoutSection(context, theme),
            SizedBox(height: 3.h),
          ],
        ),
      ),
    );
  }

  Widget _buildIdentityCard(BuildContext context, ThemeData theme) {
    return Container(
      margin: EdgeInsets.all(3.w),
      padding: EdgeInsets.all(4.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.08),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        children: [
          Container(
            width: 25.w,
            height: 25.w,
            decoration: BoxDecoration(
              color: theme.colorScheme.primaryContainer.withOpacity(0.3),
              shape: BoxShape.circle,
            ),
            child: Icon(
              Icons.person,
              size: 60,
              color: theme.colorScheme.primary,
            ),
          ),
          SizedBox(height: 2.h),
          Text(
            'Aisha Okoro',
            style: theme.textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.bold,
              fontSize: 18.sp,
            ),
          ),
          SizedBox(height: 0.5.h),
          Text(
            'aisha.okoro@example.com',
            style: theme.textTheme.bodyMedium?.copyWith(
              color: theme.colorScheme.onSurfaceVariant,
              fontSize: 13.sp,
            ),
          ),
          SizedBox(height: 2.h),
          OutlinedButton.icon(
            onPressed: () => _showEditProfileDialog(context),
            icon: Icon(
              Icons.edit_outlined,
              size: 18,
              color: theme.colorScheme.primary,
            ),
            label: Text(
              'Edit Info',
              style: theme.textTheme.labelMedium?.copyWith(
                color: theme.colorScheme.primary,
                fontWeight: FontWeight.w600,
                fontSize: 12.sp,
              ),
            ),
            style: OutlinedButton.styleFrom(
              side: BorderSide(
                color: theme.colorScheme.primary,
                width: 1.5,
              ),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
              padding: EdgeInsets.symmetric(horizontal: 4.w, vertical: 1.h),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAccountManagementSection(BuildContext context, ThemeData theme) {
    return Container(
      margin: EdgeInsets.symmetric(horizontal: 3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: EdgeInsets.fromLTRB(4.w, 2.h, 4.w, 1.h),
            child: Text(
              'Account Management',
              style: theme.textTheme.labelLarge?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
                fontWeight: FontWeight.w600,
                fontSize: 11.sp,
              ),
            ),
          ),
          _buildProfileListTile(
            context,
            theme,
            icon: Icons.person_outline,
            title: 'Edit Profile',
            onTap: () => _showEditProfileDialog(context),
          ),
          Divider(
            height: 1,
            indent: 4.w,
            endIndent: 4.w,
            color: theme.colorScheme.outline.withOpacity(0.2),
          ),
          _buildProfileListTile(
            context,
            theme,
            icon: Icons.lock_outline,
            title: 'Change Password',
            onTap: () {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: const Text('Password change feature accessed.'),
                  backgroundColor: theme.colorScheme.primary,
                ),
              );
            },
          ),
          Divider(
            height: 1,
            indent: 4.w,
            endIndent: 4.w,
            color: theme.colorScheme.outline.withOpacity(0.2),
          ),
          _buildProfileListTile(
            context,
            theme,
            icon: Icons.cancel_outlined,
            title: 'My Cancellations',
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const TicketsListScreen(
                    initialFilter: TicketFilter.cancelled,
                  ),
                ),
              );
            },
          ),
          Divider(
            height: 1,
            indent: 4.w,
            endIndent: 4.w,
            color: theme.colorScheme.outline.withOpacity(0.2),
          ),
          _buildProfileListTile(
            context,
            theme,
            icon: Icons.people_alt_outlined,
            title: 'Agent Dashboard',
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const AgentDashboardScreen(),
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildGeneralSupportSection(BuildContext context, ThemeData theme) {
    return Container(
      margin: EdgeInsets.symmetric(horizontal: 3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: EdgeInsets.fromLTRB(4.w, 2.h, 4.w, 1.h),
            child: Text(
              'General & Support',
              style: theme.textTheme.labelLarge?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
                fontWeight: FontWeight.w600,
                fontSize: 11.sp,
              ),
            ),
          ),
          _buildProfileListTile(
            context,
            theme,
            icon: Icons.info_outline,
            title: 'About Us',
            onTap: () => _showAboutDialog(context),
          ),
          Divider(
            height: 1,
            indent: 4.w,
            endIndent: 4.w,
            color: theme.colorScheme.outline.withOpacity(0.2),
          ),
          _buildProfileListTile(
            context,
            theme,
            icon: Icons.policy_outlined,
            title: 'Terms & Privacy',
            onTap: () {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: const Text('Terms opened in browser.'),
                  backgroundColor: theme.colorScheme.primary,
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildLogoutSection(BuildContext context, ThemeData theme) {
    return Container(
      margin: EdgeInsets.symmetric(horizontal: 3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
      ),
      child: ListTile(
        contentPadding: EdgeInsets.symmetric(horizontal: 4.w, vertical: 0.5.h),
        leading: Container(
          padding: EdgeInsets.all(2.w),
          decoration: BoxDecoration(
            color: theme.colorScheme.error.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(
            Icons.exit_to_app_outlined,
            color: theme.colorScheme.error,
            size: 24,
          ),
        ),
        title: Text(
          'Logout',
          style: theme.textTheme.titleSmall?.copyWith(
            color: theme.colorScheme.error,
            fontWeight: FontWeight.w600,
            fontSize: 14.sp,
          ),
        ),
        trailing: Icon(
          Icons.chevron_right,
          color: theme.colorScheme.error,
          size: 24,
        ),
        onTap: () => _handleLogout(context),
      ),
    );
  }

  Widget _buildProfileListTile(
    BuildContext context,
    ThemeData theme, {
    required IconData icon,
    required String title,
    required VoidCallback onTap,
  }) {
    return ListTile(
      contentPadding: EdgeInsets.symmetric(horizontal: 4.w, vertical: 0.5.h),
      leading: Container(
        padding: EdgeInsets.all(2.w),
        decoration: BoxDecoration(
          color: theme.colorScheme.primaryContainer.withOpacity(0.2),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Icon(
          icon,
          color: theme.colorScheme.primary,
          size: 22,
        ),
      ),
      title: Text(
        title,
        style: theme.textTheme.titleSmall?.copyWith(
          fontWeight: FontWeight.w500,
          fontSize: 14.sp,
        ),
      ),
      trailing: Icon(
        Icons.chevron_right,
        color: theme.colorScheme.onSurfaceVariant,
        size: 24,
      ),
      onTap: onTap,
    );
  }
}
