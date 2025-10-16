import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';

class TicketDetailsScreen extends StatelessWidget {
  final String ticketToken;

  const TicketDetailsScreen({
    Key? key,
    required this.ticketToken,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: theme.colorScheme.primary,
        elevation: 2,
        leading: IconButton(
          onPressed: () => Navigator.pop(context),
          icon: CustomIconWidget(
            iconName: 'arrow_back',
            color: theme.colorScheme.onPrimary,
            size: 24,
          ),
        ),
        title: Text(
          'Ticket Details',
          style: theme.textTheme.titleLarge?.copyWith(
            color: theme.colorScheme.onPrimary,
            fontWeight: FontWeight.w600,
          ),
        ),
        centerTitle: true,
      ),
      body: Center(
        child: Padding(
          padding: EdgeInsets.all(4.w),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CustomIconWidget(
                iconName: 'confirmation_number',
                color: theme.colorScheme.primary,
                size: 48,
              ),
              SizedBox(height: 2.h),
              Text(
                'Details for Token $ticketToken would go here.',
                style: theme.textTheme.titleMedium?.copyWith(
                  fontSize: 13.sp,
                ),
                textAlign: TextAlign.center,
              ),
              SizedBox(height: 0.8.h),
              Text(
                'This is a placeholder screen for the ticket details view.',
                style: theme.textTheme.bodySmall?.copyWith(
                  color: theme.colorScheme.onSurfaceVariant,
                  fontSize: 10.sp,
                ),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
