
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';
import '../seat_selection_screen/seat_selection_screen.dart';
import 'payment_success_screen.dart';

class BookingSummaryScreen extends StatefulWidget {
  final List<String>? selectedSeats;
  final double? totalFare;
  final String? tripId;
  final String? route;
  final String? dateTime;

  const BookingSummaryScreen({
    Key? key,
    this.selectedSeats,
    this.totalFare,
    this.tripId,
    this.route,
    this.dateTime,
  }) : super(key: key);

  @override
  State<BookingSummaryScreen> createState() => _BookingSummaryScreenState();
}

class _BookingSummaryScreenState extends State<BookingSummaryScreen> with SingleTickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;
  
  List<Map<String, TextEditingController>> passengerControllers = [];
  final TextEditingController _luggageController = TextEditingController();
  final TextEditingController _nextOfKinController = TextEditingController();
  final double freeLuggageAllowance = 10.0;
  
  @override
  void initState() {
    super.initState();
    _initializePassengerForms();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _fadeAnimation = CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    );
    _animationController.forward();
  }
  
  void _initializePassengerForms() {
    final seats = widget.selectedSeats ?? ['A1', 'B2', 'B3'];
    for (int i = 0; i < seats.length; i++) {
      passengerControllers.add({
        'fullName': TextEditingController(),
        'phoneNumber': TextEditingController(),
        'email': TextEditingController(),
      });
    }
  }
  
  @override
  void dispose() {
    for (var controllers in passengerControllers) {
      controllers['fullName']?.dispose();
      controllers['phoneNumber']?.dispose();
      controllers['email']?.dispose();
    }
    _luggageController.dispose();
    _nextOfKinController.dispose();
    _animationController.dispose();
    super.dispose();
  }
  
  bool _validateForm() {
    if (!_formKey.currentState!.validate()) {
      return false;
    }
    
    for (int i = 0; i < passengerControllers.length; i++) {
      if (passengerControllers[i]['fullName']!.text.trim().isEmpty ||
          passengerControllers[i]['phoneNumber']!.text.trim().isEmpty ||
          passengerControllers[i]['email']!.text.trim().isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'Please fill all required fields for Passenger ${i + 1}',
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: Theme.of(context).colorScheme.onError,
                fontSize: 11.sp,
              ),
            ),
            backgroundColor: Theme.of(context).colorScheme.error,
          ),
        );
        return false;
      }
    }
    
    return true;
  }
  
  void _proceedToPayment() {
    if (_validateForm()) {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => const PaymentSuccessScreen(),
        ),
      );
    }
  }
  
  void _navigateBackToSeatSelection() {
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final seats = widget.selectedSeats ?? ['A1', 'B2', 'B3'];
    final totalAmount = widget.totalFare ?? 55500.0;

    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: theme.colorScheme.primary,
        elevation: 1,
        leading: IconButton(
          onPressed: () => Navigator.pop(context),
          icon: CustomIconWidget(
            iconName: 'arrow_back',
            color: theme.colorScheme.onPrimary,
            size: 22,
          ),
        ),
        title: Text(
          'Booking Summary',
          style: theme.textTheme.titleLarge?.copyWith(
            color: theme.colorScheme.onPrimary,
            fontWeight: FontWeight.w600,
            fontSize: 13.sp,
          ),
        ),
        centerTitle: true,
      ),
      body: FadeTransition(
        opacity: _fadeAnimation,
        child: Column(
          children: [
            _buildBookingSummaryCard(theme, seats, totalAmount),
            Expanded(
              child: SingleChildScrollView(
                child: Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      _buildPassengerDetailsSection(theme, seats),
                      _buildOptionalFieldsSection(theme),
                      SizedBox(height: 10.h),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: _buildPaymentButton(theme, totalAmount),
    );
  }

  Widget _buildBookingSummaryCard(ThemeData theme, List<String> seats, double totalAmount) {
    return Container(
      margin: EdgeInsets.all(2.5.w),
      padding: EdgeInsets.all(2.5.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(8),
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.08),
            blurRadius: 6,
            offset: const Offset(0, 1),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Booking Details',
                style: theme.textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.bold,
                  fontSize: 12.sp,
                ),
              ),
              IconButton(
                onPressed: _navigateBackToSeatSelection,
                icon: CustomIconWidget(
                  iconName: 'edit',
                  color: theme.colorScheme.primary,
                  size: 18,
                ),
                tooltip: 'Modify Seats',
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
            ],
          ),
          SizedBox(height: 1.2.h),
          Row(
            children: [
              CustomIconWidget(
                iconName: 'route',
                color: theme.colorScheme.primary,
                size: 16,
              ),
              SizedBox(width: 1.5.w),
              Expanded(
                child: Text(
                  widget.route ?? 'Abuja to Lagos',
                  style: theme.textTheme.bodyMedium?.copyWith(
                    fontWeight: FontWeight.w600,
                    fontSize: 11.sp,
                  ),
                ),
              ),
            ],
          ),
          SizedBox(height: 0.8.h),
          Row(
            children: [
              CustomIconWidget(
                iconName: 'event',
                color: theme.colorScheme.onSurfaceVariant,
                size: 14,
              ),
              SizedBox(width: 1.5.w),
              Text(
                widget.dateTime ?? 'Sat, Nov 16 @ 07:00 AM',
                style: theme.textTheme.bodySmall?.copyWith(
                  fontSize: 10.sp,
                  color: theme.colorScheme.onSurfaceVariant,
                ),
              ),
            ],
          ),
          SizedBox(height: 0.8.h),
          Row(
            children: [
              CustomIconWidget(
                iconName: 'event_seat',
                color: theme.colorScheme.onSurfaceVariant,
                size: 14,
              ),
              SizedBox(width: 1.5.w),
              Expanded(
                child: Text(
                  'Selected Seats: ${seats.join(", ")}',
                  style: theme.textTheme.bodySmall?.copyWith(
                    fontSize: 10.sp,
                    color: theme.colorScheme.onSurfaceVariant,
                  ),
                ),
              ),
            ],
          ),
          SizedBox(height: 1.2.h),
          Divider(color: theme.colorScheme.outline.withOpacity(0.3)),
          SizedBox(height: 0.8.h),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Grand Total:',
                style: theme.textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.bold,
                  fontSize: 12.sp,
                ),
              ),
              Text(
                '₦${totalAmount.toStringAsFixed(0)}',
                style: theme.textTheme.titleLarge?.copyWith(
                  color: theme.colorScheme.primary,
                  fontWeight: FontWeight.bold,
                  fontSize: 14.sp,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildPassengerDetailsSection(ThemeData theme, List<String> seats) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 0.8.h),
          child: Text(
            'Passenger Details',
            style: theme.textTheme.titleMedium?.copyWith(
              fontWeight: FontWeight.bold,
              fontSize: 12.sp,
            ),
          ),
        ),
        ...List.generate(
          seats.length,
          (index) => _buildPassengerCard(theme, seats[index], index),
        ),
      ],
    );
  }

  Widget _buildPassengerCard(ThemeData theme, String seat, int index) {
    return Container(
      margin: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 0.8.h),
      padding: EdgeInsets.all(2.5.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: theme.colorScheme.outline.withOpacity(0.3),
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: EdgeInsets.symmetric(horizontal: 1.5.w, vertical: 0.4.h),
                decoration: BoxDecoration(
                  color: theme.colorScheme.primaryContainer.withOpacity(0.3),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  'Seat $seat',
                  style: theme.textTheme.labelMedium?.copyWith(
                    color: theme.colorScheme.primary,
                    fontWeight: FontWeight.w600,
                    fontSize: 9.sp,
                  ),
                ),
              ),
              SizedBox(width: 1.5.w),
              Text(
                'Passenger ${index + 1}',
                style: theme.textTheme.titleSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                  fontSize: 11.sp,
                ),
              ),
            ],
          ),
          SizedBox(height: 1.5.h),
          TextFormField(
            controller: passengerControllers[index]['fullName'],
            decoration: InputDecoration(
              labelText: 'Full Name *',
              labelStyle: TextStyle(fontSize: 11.sp),
              prefixIcon: Icon(
                Icons.person_outline,
                size: 18,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 1.2.h),
            ),
            style: TextStyle(fontSize: 11.sp),
            validator: (value) {
              if (value == null || value.trim().isEmpty) {
                return 'Full name is required';
              }
              return null;
            },
          ),
          SizedBox(height: 1.2.h),
          TextFormField(
            controller: passengerControllers[index]['phoneNumber'],
            decoration: InputDecoration(
              labelText: 'Phone Number *',
              labelStyle: TextStyle(fontSize: 11.sp),
              prefixIcon: Icon(
                Icons.phone_outlined,
                size: 18,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 1.2.h),
            ),
            style: TextStyle(fontSize: 11.sp),
            keyboardType: TextInputType.phone,
            inputFormatters: [
              FilteringTextInputFormatter.digitsOnly,
            ],
            validator: (value) {
              if (value == null || value.trim().isEmpty) {
                return 'Phone number is required';
              }
              return null;
            },
          ),
          SizedBox(height: 1.2.h),
          TextFormField(
            controller: passengerControllers[index]['email'],
            decoration: InputDecoration(
              labelText: 'Email Address *',
              labelStyle: TextStyle(fontSize: 11.sp),
              prefixIcon: Icon(
                Icons.email_outlined,
                size: 18,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 1.2.h),
            ),
            style: TextStyle(fontSize: 11.sp),
            keyboardType: TextInputType.emailAddress,
            validator: (value) {
              if (value == null || value.trim().isEmpty) {
                return 'Email address is required';
              }
              if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value)) {
                return 'Please enter a valid email';
              }
              return null;
            },
          ),
        ],
      ),
    );
  }

  Widget _buildOptionalFieldsSection(ThemeData theme) {
    return Container(
      margin: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 0.8.h),
      padding: EdgeInsets.all(2.5.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: theme.colorScheme.outline.withOpacity(0.3),
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Additional Information (Optional)',
            style: theme.textTheme.titleSmall?.copyWith(
              fontWeight: FontWeight.bold,
              fontSize: 11.sp,
            ),
          ),
          SizedBox(height: 1.5.h),
          TextFormField(
            controller: _luggageController,
            decoration: InputDecoration(
              labelText: 'Luggage Weight (KG)',
              labelStyle: TextStyle(fontSize: 11.sp),
              hintText: 'Enter total luggage weight',
              hintStyle: TextStyle(fontSize: 10.sp),
              prefixIcon: Icon(
                Icons.luggage_outlined,
                size: 18,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              helperText: '${freeLuggageAllowance}KG free allowance per ticket',
              helperStyle: theme.textTheme.bodySmall?.copyWith(
                fontSize: 9.sp,
                color: theme.colorScheme.secondary,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 1.2.h),
            ),
            style: TextStyle(fontSize: 11.sp),
            keyboardType: TextInputType.number,
            inputFormatters: [
              FilteringTextInputFormatter.digitsOnly,
            ],
          ),
          SizedBox(height: 1.2.h),
          TextFormField(
            controller: _nextOfKinController,
            decoration: InputDecoration(
              labelText: 'Emergency Contact / Next of Kin',
              labelStyle: TextStyle(fontSize: 11.sp),
              hintText: 'Name and phone number',
              hintStyle: TextStyle(fontSize: 10.sp),
              prefixIcon: Icon(
                Icons.contact_emergency_outlined,
                size: 18,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 1.2.h),
            ),
            style: TextStyle(fontSize: 11.sp),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentButton(ThemeData theme, double totalAmount) {
    return Container(
      padding: EdgeInsets.all(2.5.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.08),
            blurRadius: 6,
            offset: const Offset(0, -1),
          ),
        ],
      ),
      child: SafeArea(
        child: SizedBox(
          width: double.infinity,
          height: 5.h,
          child: ElevatedButton(
            onPressed: _proceedToPayment,
            style: ElevatedButton.styleFrom(
              backgroundColor: theme.colorScheme.primary,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              elevation: 1,
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  'PAY NOW',
                  style: theme.textTheme.titleMedium?.copyWith(
                    color: theme.colorScheme.onPrimary,
                    fontWeight: FontWeight.bold,
                    fontSize: 11.sp,
                  ),
                ),
                SizedBox(width: 1.5.w),
                Text(
                  '(₦${totalAmount.toStringAsFixed(0)})',
                  style: theme.textTheme.titleMedium?.copyWith(
                    color: theme.colorScheme.onPrimary,
                    fontWeight: FontWeight.bold,
                    fontSize: 11.sp,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
