import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';
import '../booking_summary_screen/booking_summary_screen.dart';

enum SeatStatus { available, selected, booked, aisle, driver }

class SeatSelectionScreen extends StatefulWidget {
  final String? tripId;
  final String? route;
  final String? dateTime;
  final String? busType;

  const SeatSelectionScreen({
    Key? key,
    this.tripId,
    this.route,
    this.dateTime,
    this.busType,
  }) : super(key: key);

  @override
  State<SeatSelectionScreen> createState() => _SeatSelectionScreenState();
}

class _SeatSelectionScreenState extends State<SeatSelectionScreen> {
  // Mock seat data - 2x1 layout (2 seats on left, aisle, 1 seat on right) with 10 rows
  Map<String, SeatStatus> seatStates = {};
  Set<String> selectedSeats = {};
  final double baseFare = 18500.0;

  @override
  void initState() {
    super.initState();
    _initializeSeats();
  }

  void _initializeSeats() {
    // Initialize seats for 10 rows
    for (int row = 1; row <= 10; row++) {
      // Left side seats (A, B)
      seatStates['A$row'] = row == 3 || row == 7 ? SeatStatus.booked : SeatStatus.available;
      seatStates['B$row'] = row == 3 || row == 8 ? SeatStatus.booked : SeatStatus.available;
      
      // Right side seat (C)
      seatStates['C$row'] = row == 5 ? SeatStatus.booked : SeatStatus.available;
    }
  }

  void _toggleSeat(String seatId) {
    setState(() {
      if (seatStates[seatId] == SeatStatus.available) {
        seatStates[seatId] = SeatStatus.selected;
        selectedSeats.add(seatId);
      } else if (seatStates[seatId] == SeatStatus.selected) {
        seatStates[seatId] = SeatStatus.available;
        selectedSeats.remove(seatId);
      }
    });
  }

  double _calculateTotal() {
    return baseFare * selectedSeats.length;
  }

  void _showFareBreakdown() {
    final theme = Theme.of(context);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          'Base Fare: ₦${baseFare.toStringAsFixed(0)} × ${selectedSeats.length} seats\n+ Insurance & Taxes: ₦0',
          style: theme.textTheme.bodySmall?.copyWith(
            color: theme.colorScheme.onPrimary,
          ),
        ),
        backgroundColor: theme.colorScheme.primary,
        behavior: SnackBarBehavior.floating,
        duration: const Duration(seconds: 3),
      ),
    );
  }

  void _proceedToBooking() {
    if (selectedSeats.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text('Please select at least one seat'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
      return;
    }

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => BookingSummaryScreen(
          selectedSeats: selectedSeats.toList(),
          totalFare: _calculateTotal(),
          tripId: widget.tripId,
          route: widget.route,
          dateTime: widget.dateTime,
        ),
      ),
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
        leading: IconButton(
          onPressed: () => Navigator.pop(context),
          icon: CustomIconWidget(
            iconName: 'arrow_back',
            color: theme.colorScheme.onPrimary,
            size: 24,
          ),
        ),
        title: Text(
          'Select Your Seats',
          style: theme.textTheme.titleLarge?.copyWith(
            color: theme.colorScheme.onPrimary,
            fontWeight: FontWeight.w600,
          ),
        ),
        centerTitle: true,
      ),
      body: Column(
        children: [
          _buildTripSummaryCard(theme),
          _buildColorLegend(theme),
          Expanded(
            child: SingleChildScrollView(
              child: _buildSeatLayout(theme),
            ),
          ),
          _buildBookingSummary(theme),
        ],
      ),
      bottomNavigationBar: _buildProceedButton(theme),
    );
  }

  Widget _buildTripSummaryCard(ThemeData theme) {
    return Container(
      margin: EdgeInsets.all(3.w),
      padding: EdgeInsets.all(3.w),
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
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              CustomIconWidget(
                iconName: 'route',
                color: theme.colorScheme.primary,
                size: 18,
              ),
              SizedBox(width: 2.w),
              Expanded(
                child: Text(
                  widget.route ?? 'Enugu to Abuja',
                  style: theme.textTheme.titleSmall?.copyWith(
                    fontWeight: FontWeight.bold,
                    fontSize: 14.sp,
                  ),
                ),
              ),
            ],
          ),
          SizedBox(height: 1.h),
          Row(
            children: [
              CustomIconWidget(
                iconName: 'event',
                color: theme.colorScheme.onSurfaceVariant,
                size: 16,
              ),
              SizedBox(width: 2.w),
              Text(
                widget.dateTime ?? 'Sat, Nov 16 @ 07:00 AM',
                style: theme.textTheme.bodySmall?.copyWith(
                  fontSize: 12.sp,
                  color: theme.colorScheme.onSurfaceVariant,
                ),
              ),
              SizedBox(width: 3.w),
              CustomIconWidget(
                iconName: 'directions_bus',
                color: theme.colorScheme.onSurfaceVariant,
                size: 16,
              ),
              SizedBox(width: 1.w),
              Expanded(
                child: Text(
                  widget.busType ?? 'VIP Luxury Coach, 2x1 Layout',
                  style: theme.textTheme.bodySmall?.copyWith(
                    fontSize: 12.sp,
                    color: theme.colorScheme.onSurfaceVariant,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildColorLegend(ThemeData theme) {
    return Container(
      margin: EdgeInsets.symmetric(horizontal: 3.w),
      padding: EdgeInsets.all(2.w),
      child: Wrap(
        spacing: 3.w,
        runSpacing: 1.h,
        alignment: WrapAlignment.center,
        children: [
          _buildLegendItem(theme, 'Available', theme.colorScheme.surface, theme.colorScheme.outline),
          _buildLegendItem(theme, 'Selected', theme.colorScheme.secondary, null),
          _buildLegendItem(theme, 'Booked', theme.colorScheme.error.withOpacity(0.7), null),
          _buildLegendItem(theme, 'Aisle', Colors.transparent, theme.colorScheme.outline.withOpacity(0.3), isAisle: true),
        ],
      ),
    );
  }

  Widget _buildLegendItem(ThemeData theme, String label, Color color, Color? borderColor, {bool isAisle = false}) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 20,
          height: 20,
          decoration: BoxDecoration(
            color: color,
            borderRadius: BorderRadius.circular(4),
            border: borderColor != null ? Border.all(color: borderColor, width: 1) : null,
          ),
          child: isAisle
              ? Center(
                  child: Container(
                    width: 2,
                    height: 20,
                    color: theme.colorScheme.outline.withOpacity(0.5),
                  ),
                )
              : null,
        ),
        SizedBox(width: 1.w),
        Text(
          label,
          style: theme.textTheme.bodySmall?.copyWith(
            fontSize: 11.sp,
            color: theme.colorScheme.onSurfaceVariant,
          ),
        ),
      ],
    );
  }

  Widget _buildSeatLayout(ThemeData theme) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 2.h),
      child: Column(
        children: [
          // Driver position
          _buildDriverSection(theme),
          SizedBox(height: 2.h),
          
          // Seat rows
          ...List.generate(10, (index) {
            final row = index + 1;
            return Padding(
              padding: EdgeInsets.only(bottom: 1.5.h),
              child: _buildSeatRow(theme, row),
            );
          }),
        ],
      ),
    );
  }

  Widget _buildDriverSection(ThemeData theme) {
    return Container(
      padding: EdgeInsets.all(2.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.primaryContainer.withOpacity(0.2),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          CustomIconWidget(
            iconName: 'airline_seat_recline_normal',
            color: theme.colorScheme.primary,
            size: 24,
          ),
          SizedBox(width: 2.w),
          Text(
            'Driver',
            style: theme.textTheme.labelMedium?.copyWith(
              color: theme.colorScheme.primary,
              fontWeight: FontWeight.w600,
              fontSize: 12.sp,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSeatRow(ThemeData theme, int row) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        // Row number
        SizedBox(
          width: 8.w,
          child: Text(
            '$row',
            style: theme.textTheme.bodySmall?.copyWith(
              fontWeight: FontWeight.w600,
              fontSize: 11.sp,
            ),
            textAlign: TextAlign.center,
          ),
        ),
        
        // Left side seats (A, B)
        _buildSeat(theme, 'A$row'),
        SizedBox(width: 1.w),
        _buildSeat(theme, 'B$row'),
        
        // Aisle
        SizedBox(width: 6.w),
        
        // Right side seat (C)
        _buildSeat(theme, 'C$row'),
        
        SizedBox(width: 8.w), // Balance the layout
      ],
    );
  }

  Widget _buildSeat(ThemeData theme, String seatId) {
    final status = seatStates[seatId] ?? SeatStatus.available;
    
    Color backgroundColor;
    Color? borderColor;
    bool isInteractive = false;

    switch (status) {
      case SeatStatus.available:
        backgroundColor = theme.colorScheme.surface;
        borderColor = theme.colorScheme.outline;
        isInteractive = true;
        break;
      case SeatStatus.selected:
        backgroundColor = theme.colorScheme.secondary;
        borderColor = null;
        isInteractive = true;
        break;
      case SeatStatus.booked:
        backgroundColor = theme.colorScheme.error.withOpacity(0.7);
        borderColor = null;
        isInteractive = false;
        break;
      default:
        backgroundColor = Colors.transparent;
        borderColor = null;
        isInteractive = false;
    }

    return GestureDetector(
      onTap: isInteractive ? () => _toggleSeat(seatId) : null,
      child: Container(
        width: 18.w,
        height: 18.w,
        decoration: BoxDecoration(
          color: backgroundColor,
          borderRadius: BorderRadius.circular(8),
          border: borderColor != null ? Border.all(color: borderColor, width: 1) : null,
        ),
        child: Center(
          child: Text(
            seatId,
            style: theme.textTheme.labelSmall?.copyWith(
              fontSize: 11.sp,
              fontWeight: FontWeight.w600,
              color: status == SeatStatus.selected
                  ? theme.colorScheme.onSecondary
                  : status == SeatStatus.booked
                      ? theme.colorScheme.onError
                      : theme.colorScheme.onSurface,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildBookingSummary(ThemeData theme) {
    if (selectedSeats.isEmpty) {
      return const SizedBox.shrink();
    }

    return Container(
      margin: EdgeInsets.all(3.w),
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.primaryContainer.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: theme.colorScheme.primary.withOpacity(0.3),
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Selected Seats:',
                style: theme.textTheme.labelMedium?.copyWith(
                  fontWeight: FontWeight.w600,
                  fontSize: 12.sp,
                ),
              ),
              GestureDetector(
                onTap: _showFareBreakdown,
                child: Row(
                  children: [
                    Text(
                      'Fare Breakdown',
                      style: theme.textTheme.labelSmall?.copyWith(
                        color: theme.colorScheme.primary,
                        fontSize: 11.sp,
                      ),
                    ),
                    SizedBox(width: 1.w),
                    CustomIconWidget(
                      iconName: 'info_outline',
                      color: theme.colorScheme.primary,
                      size: 16,
                    ),
                  ],
                ),
              ),
            ],
          ),
          SizedBox(height: 1.h),
          Text(
            selectedSeats.toList().join(', '),
            style: theme.textTheme.bodyMedium?.copyWith(
              fontSize: 13.sp,
              fontWeight: FontWeight.w500,
            ),
          ),
          SizedBox(height: 1.5.h),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Total Fare:',
                style: theme.textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.bold,
                  fontSize: 15.sp,
                ),
              ),
              Text(
                '₦${_calculateTotal().toStringAsFixed(0)}',
                style: theme.textTheme.titleLarge?.copyWith(
                  color: theme.colorScheme.primary,
                  fontWeight: FontWeight.bold,
                  fontSize: 18.sp,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildProceedButton(ThemeData theme) {
    return Container(
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.1),
            blurRadius: 8,
            offset: const Offset(0, -2),
          ),
        ],
      ),
      child: SafeArea(
        child: SizedBox(
          width: double.infinity,
          height: 6.h,
          child: ElevatedButton(
            onPressed: _proceedToBooking,
            style: ElevatedButton.styleFrom(
              backgroundColor: theme.colorScheme.primary,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              elevation: 2,
            ),
            child: Text(
              selectedSeats.isEmpty
                  ? 'SELECT SEATS TO PROCEED'
                  : 'PROCEED TO BOOKING (${selectedSeats.length} ${selectedSeats.length == 1 ? 'Seat' : 'Seats'})',
              style: theme.textTheme.titleMedium?.copyWith(
                color: theme.colorScheme.onPrimary,
                fontWeight: FontWeight.bold,
                fontSize: 14.sp,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
