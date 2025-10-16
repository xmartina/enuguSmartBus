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

class _BookingSummaryScreenState extends State<BookingSummaryScreen> {
  final _formKey = GlobalKey<FormState>();
  
  // Mock data - 3 passengers for 3 selected seats
  List<Map<String, TextEditingController>> passengerControllers = [];
  
  // Optional fields
  final TextEditingController _luggageController = TextEditingController();
  final TextEditingController _nextOfKinController = TextEditingController();
  
  // Mock luggage settings
  final double freeLuggageAllowance = 10.0; // 10KG free allowance
  
  @override
  void initState() {
    super.initState();
    _initializePassengerForms();
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
    super.dispose();
  }
  
  bool _validateForm() {
    if (!_formKey.currentState!.validate()) {
      return false;
    }
    
    // Check if all required fields are filled
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
      body: Column(
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
      bottomNavigationBar: _buildPaymentButton(theme, totalAmount),
    );
  }

  Widget _buildBookingSummaryCard(ThemeData theme, List<String> seats, double totalAmount) {
    return Container(
      margin: EdgeInsets.all(3.w),
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.1),
            blurRadius: 8,
            offset: const Offset(0, 2),
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
                  fontSize: 15.sp,
                ),
              ),
              IconButton(
                onPressed: _navigateBackToSeatSelection,
                icon: CustomIconWidget(
                  iconName: 'edit',
                  color: theme.colorScheme.primary,
                  size: 20,
                ),
                tooltip: 'Modify Seats',
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
            ],
          ),
          SizedBox(height: 1.5.h),
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
                  widget.route ?? 'Abuja to Lagos',
                  style: theme.textTheme.bodyMedium?.copyWith(
                    fontWeight: FontWeight.w600,
                    fontSize: 13.sp,
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
            ],
          ),
          SizedBox(height: 1.h),
          Row(
            children: [
              CustomIconWidget(
                iconName: 'event_seat',
                color: theme.colorScheme.onSurfaceVariant,
                size: 16,
              ),
              SizedBox(width: 2.w),
              Expanded(
                child: Text(
                  'Selected Seats: ${seats.join(", ")}',
                  style: theme.textTheme.bodySmall?.copyWith(
                    fontSize: 12.sp,
                    color: theme.colorScheme.onSurfaceVariant,
                  ),
                ),
              ),
            ],
          ),
          SizedBox(height: 1.5.h),
          Divider(color: theme.colorScheme.outline.withOpacity(0.3)),
          SizedBox(height: 1.h),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Grand Total:',
                style: theme.textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.bold,
                  fontSize: 15.sp,
                ),
              ),
              Text(
                '₦${totalAmount.toStringAsFixed(0)}',
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

  Widget _buildPassengerDetailsSection(ThemeData theme, List<String> seats) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.h),
          child: Text(
            'Passenger Details',
            style: theme.textTheme.titleMedium?.copyWith(
              fontWeight: FontWeight.bold,
              fontSize: 15.sp,
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
      margin: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.h),
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
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
                padding: EdgeInsets.symmetric(horizontal: 2.w, vertical: 0.5.h),
                decoration: BoxDecoration(
                  color: theme.colorScheme.primaryContainer.withOpacity(0.3),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  'Seat $seat',
                  style: theme.textTheme.labelMedium?.copyWith(
                    color: theme.colorScheme.primary,
                    fontWeight: FontWeight.w600,
                    fontSize: 11.sp,
                  ),
                ),
              ),
              SizedBox(width: 2.w),
              Text(
                'Passenger ${index + 1}',
                style: theme.textTheme.titleSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                  fontSize: 13.sp,
                ),
              ),
            ],
          ),
          SizedBox(height: 2.h),
          TextFormField(
            controller: passengerControllers[index]['fullName'],
            decoration: InputDecoration(
              labelText: 'Full Name *',
              prefixIcon: Icon(
                Icons.person_outline,
                size: 20,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.5.h),
            ),
            validator: (value) {
              if (value == null || value.trim().isEmpty) {
                return 'Full name is required';
              }
              return null;
            },
          ),
          SizedBox(height: 1.5.h),
          TextFormField(
            controller: passengerControllers[index]['phoneNumber'],
            decoration: InputDecoration(
              labelText: 'Phone Number *',
              prefixIcon: Icon(
                Icons.phone_outlined,
                size: 20,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.5.h),
            ),
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
          SizedBox(height: 1.5.h),
          TextFormField(
            controller: passengerControllers[index]['email'],
            decoration: InputDecoration(
              labelText: 'Email Address *',
              prefixIcon: Icon(
                Icons.email_outlined,
                size: 20,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.5.h),
            ),
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
      margin: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.h),
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
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
              fontSize: 13.sp,
            ),
          ),
          SizedBox(height: 2.h),
          TextFormField(
            controller: _luggageController,
            decoration: InputDecoration(
              labelText: 'Luggage Weight (KG)',
              hintText: 'Enter total luggage weight',
              prefixIcon: Icon(
                Icons.luggage_outlined,
                size: 20,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              helperText: '${freeLuggageAllowance}KG free allowance per ticket',
              helperStyle: theme.textTheme.bodySmall?.copyWith(
                fontSize: 10.sp,
                color: theme.colorScheme.secondary,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.5.h),
            ),
            keyboardType: TextInputType.number,
            inputFormatters: [
              FilteringTextInputFormatter.digitsOnly,
            ],
          ),
          SizedBox(height: 1.5.h),
          TextFormField(
            controller: _nextOfKinController,
            decoration: InputDecoration(
              labelText: 'Emergency Contact / Next of Kin',
              hintText: 'Name and phone number',
              prefixIcon: Icon(
                Icons.contact_emergency_outlined,
                size: 20,
                color: theme.colorScheme.onSurfaceVariant,
              ),
              contentPadding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.5.h),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentButton(ThemeData theme, double totalAmount) {
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
            onPressed: _proceedToPayment,
            style: ElevatedButton.styleFrom(
              backgroundColor: theme.colorScheme.primary,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              elevation: 2,
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  'PAY NOW',
                  style: theme.textTheme.titleMedium?.copyWith(
                    color: theme.colorScheme.onPrimary,
                    fontWeight: FontWeight.bold,
                    fontSize: 14.sp,
                  ),
                ),
                SizedBox(width: 2.w),
                Text(
                  '(₦${totalAmount.toStringAsFixed(0)})',
                  style: theme.textTheme.titleMedium?.copyWith(
                    color: theme.colorScheme.onPrimary,
                    fontWeight: FontWeight.bold,
                    fontSize: 14.sp,
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
