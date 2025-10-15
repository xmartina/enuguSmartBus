import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';

class TripListScreen extends StatefulWidget {
  final String? fromLocation;
  final String? toLocation;
  final String? date;
  final int? passengers;
  final String? routeName;

  const TripListScreen({
    Key? key,
    this.fromLocation,
    this.toLocation,
    this.date,
    this.passengers,
    this.routeName,
  }) : super(key: key);

  @override
  State<TripListScreen> createState() => _TripListScreenState();
}

class _TripListScreenState extends State<TripListScreen> {
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
          widget.routeName ?? 'Available Trips',
          style: theme.textTheme.titleLarge?.copyWith(
            color: theme.colorScheme.onPrimary,
            fontWeight: FontWeight.w500,
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
                iconName: 'directions_bus',
                color: theme.colorScheme.primary,
                size: 64,
              ),
              SizedBox(height: 2.h),
              Text(
                'Trip List Screen',
                style: theme.textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.w500,
                  fontSize: 18.sp,
                ),
              ),
              SizedBox(height: 1.h),
              Text(
                'This screen will show available trips',
                style: theme.textTheme.bodyMedium?.copyWith(
                  color: theme.colorScheme.onSurfaceVariant,
                ),
                textAlign: TextAlign.center,
              ),
              if (widget.fromLocation != null) ...[
                SizedBox(height: 2.h),
                Text(
                  'From: ${widget.fromLocation}',
                  style: theme.textTheme.bodySmall,
                ),
              ],
              if (widget.toLocation != null)
                Text(
                  'To: ${widget.toLocation}',
                  style: theme.textTheme.bodySmall,
                ),
              if (widget.date != null)
                Text(
                  'Date: ${widget.date}',
                  style: theme.textTheme.bodySmall,
                ),
              if (widget.passengers != null)
                Text(
                  'Passengers: ${widget.passengers}',
                  style: theme.textTheme.bodySmall,
                ),
            ],
          ),
        ),
      ),
    );
  }
}
