import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';

class BookingSummaryScreen extends StatefulWidget {
  final List<String>? selectedSeats;
  final double? totalFare;
  final String? tripId;

  const BookingSummaryScreen({
    Key? key,
    this.selectedSeats,
    this.totalFare,
    this.tripId,
  }) : super(key: key);

  @override
  State<BookingSummaryScreen> createState() => _BookingSummaryScreenState();
}

class _BookingSummaryScreenState extends State<BookingSummaryScreen> {
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
          'Booking Summary',
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
                iconName: 'receipt_long',
                color: theme.colorScheme.primary,
                size: 64,
              ),
              SizedBox(height: 2.h),
              Text(
                'Booking Summary Screen',
                style: theme.textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
              SizedBox(height: 1.h),
              Text(
                'This screen will show booking details and payment options',
                style: theme.textTheme.bodyMedium?.copyWith(
                  color: theme.colorScheme.onSurfaceVariant,
                ),
                textAlign: TextAlign.center,
              ),
              if (widget.selectedSeats != null && widget.selectedSeats!.isNotEmpty) ...[
                SizedBox(height: 2.h),
                Text(
                  'Selected Seats: ${widget.selectedSeats!.join(", ")}',
                  style: theme.textTheme.bodySmall?.copyWith(
                    color: theme.colorScheme.primary,
                  ),
                ),
              ],
              if (widget.totalFare != null) ...[
                SizedBox(height: 1.h),
                Text(
                  'Total: ₦${widget.totalFare!.toStringAsFixed(0)}',
                  style: theme.textTheme.titleMedium?.copyWith(
                    color: theme.colorScheme.primary,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
