import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';
import '../seat_selection_screen/seat_selection_screen.dart';
import '../home_screen/home_screen.dart';

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
  Set<String> selectedFilters = {};
  String selectedSort = 'Lowest Price';

  final List<Map<String, dynamic>> mockTrips = [
    {
      'id': 'TRIP001',
      'vehicleName': 'VIP Luxury Coach',
      'regNumber': 'ABC-123',
      'seatingLayout': '2x1 Layout',
      'departureTime': '07:00 AM',
      'arrivalTime': '04:30 PM',
      'duration': '9 hrs 30 mins',
      'baseFare': '₦18,500',
      'availableSeats': 5,
    },
    {
      'id': 'TRIP002',
      'vehicleName': 'Executive Sprinter',
      'regNumber': 'DEF-456',
      'seatingLayout': '2x2 Layout',
      'departureTime': '09:30 AM',
      'arrivalTime': '07:00 PM',
      'duration': '9 hrs 30 mins',
      'baseFare': '₦15,000',
      'availableSeats': 12,
    },
    {
      'id': 'TRIP003',
      'vehicleName': 'Premium Express',
      'regNumber': 'GHI-789',
      'seatingLayout': '2x1 Layout',
      'departureTime': '11:00 AM',
      'arrivalTime': '08:45 PM',
      'duration': '9 hrs 45 mins',
      'baseFare': '₦20,000',
      'availableSeats': 3,
    },
    {
      'id': 'TRIP004',
      'vehicleName': 'Standard Coach',
      'regNumber': 'JKL-012',
      'seatingLayout': '2x2 Layout',
      'departureTime': '02:00 PM',
      'arrivalTime': '11:30 PM',
      'duration': '9 hrs 30 mins',
      'baseFare': '₦12,500',
      'availableSeats': 18,
    },
    {
      'id': 'TRIP005',
      'vehicleName': 'Deluxe Night Rider',
      'regNumber': 'MNO-345',
      'seatingLayout': '2x1 Layout',
      'departureTime': '08:00 PM',
      'arrivalTime': '05:30 AM',
      'duration': '9 hrs 30 mins',
      'baseFare': '₦22,000',
      'availableSeats': 7,
    },
  ];

  void _showSortDialog() {
    showDialog(
      context: context,
      builder: (context) {
        final theme = Theme.of(context);
        return AlertDialog(
          title: Text(
            'Sort By',
            style: theme.textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.bold,
            ),
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              _buildSortOption(theme, 'Lowest Price'),
              _buildSortOption(theme, 'Highest Price'),
              _buildSortOption(theme, 'Earliest Departure'),
              _buildSortOption(theme, 'Latest Departure'),
            ],
          ),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
        );
      },
    );
  }

  Widget _buildSortOption(ThemeData theme, String option) {
    return RadioListTile<String>(
      title: Text(
        option,
        style: theme.textTheme.bodyMedium,
      ),
      value: option,
      groupValue: selectedSort,
      onChanged: (value) {
        setState(() {
          selectedSort = value!;
        });
        Navigator.pop(context);
      },
      activeColor: theme.colorScheme.primary,
    );
  }

  void _navigateToSeatSelection(String tripId) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => SeatSelectionScreen(tripId: tripId),
      ),
    );
  }

  void _modifySearch() {
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(
        builder: (context) => const HomeScreen(),
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
          'Available Trips',
          style: theme.textTheme.titleLarge?.copyWith(
            color: theme.colorScheme.onPrimary,
            fontWeight: FontWeight.w600,
          ),
        ),
        centerTitle: true,
      ),
      body: Column(
        children: [
          _buildSearchSummaryCard(theme),
          _buildFilterSortBar(theme),
          Expanded(
            child: _buildTripsList(theme),
          ),
        ],
      ),
    );
  }

  Widget _buildSearchSummaryCard(ThemeData theme) {
    String departure = widget.fromLocation ?? 'Enugu';
    String destination = widget.toLocation ?? 'Abuja';
    String travelDate = widget.date ?? 'Sat, Nov 16';

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
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    CustomIconWidget(
                      iconName: 'trip_origin',
                      color: theme.colorScheme.primary,
                      size: 16,
                    ),
                    SizedBox(width: 2.w),
                    Expanded(
                      child: Text(
                        '$departure to $destination',
                        style: theme.textTheme.titleSmall?.copyWith(
                          fontWeight: FontWeight.bold,
                          fontSize: 14.sp,
                        ),
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 0.5.h),
                Row(
                  children: [
                    CustomIconWidget(
                      iconName: 'calendar_today',
                      color: theme.colorScheme.onSurfaceVariant,
                      size: 14,
                    ),
                    SizedBox(width: 2.w),
                    Text(
                      travelDate,
                      style: theme.textTheme.bodySmall?.copyWith(
                        color: theme.colorScheme.onSurfaceVariant,
                        fontSize: 12.sp,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          TextButton.icon(
            onPressed: _modifySearch,
            icon: CustomIconWidget(
              iconName: 'edit',
              color: theme.colorScheme.primary,
              size: 16,
            ),
            label: Text(
              'Modify',
              style: theme.textTheme.labelSmall?.copyWith(
                color: theme.colorScheme.primary,
                fontWeight: FontWeight.w600,
                fontSize: 11.sp,
              ),
            ),
            style: TextButton.styleFrom(
              padding: EdgeInsets.symmetric(horizontal: 2.w, vertical: 0.5.h),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterSortBar(ThemeData theme) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.h),
      child: Row(
        children: [
          OutlinedButton.icon(
            onPressed: _showSortDialog,
            icon: CustomIconWidget(
              iconName: 'sort',
              color: theme.colorScheme.primary,
              size: 18,
            ),
            label: Text(
              'Sort',
              style: theme.textTheme.labelMedium?.copyWith(
                color: theme.colorScheme.primary,
                fontWeight: FontWeight.w600,
                fontSize: 12.sp,
              ),
            ),
            style: OutlinedButton.styleFrom(
              padding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.h),
              side: BorderSide(color: theme.colorScheme.primary, width: 1),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
            ),
          ),
          SizedBox(width: 2.w),
          Expanded(
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  _buildFilterChip(theme, 'Bus Type', 'directions_bus'),
                  SizedBox(width: 2.w),
                  _buildFilterChip(theme, 'Departure Window', 'schedule'),
                  SizedBox(width: 2.w),
                  _buildFilterChip(theme, 'Facilities', 'wifi'),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(ThemeData theme, String label, String iconName) {
    final isSelected = selectedFilters.contains(label);

    return FilterChip(
      label: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          CustomIconWidget(
            iconName: iconName,
            color: isSelected
                ? theme.colorScheme.onPrimary
                : theme.colorScheme.primary,
            size: 16,
          ),
          SizedBox(width: 1.w),
          Text(
            label,
            style: theme.textTheme.labelSmall?.copyWith(
              color: isSelected
                  ? theme.colorScheme.onPrimary
                  : theme.colorScheme.primary,
              fontWeight: FontWeight.w600,
              fontSize: 11.sp,
            ),
          ),
        ],
      ),
      selected: isSelected,
      onSelected: (selected) {
        setState(() {
          if (selected) {
            selectedFilters.add(label);
          } else {
            selectedFilters.remove(label);
          }
        });
      },
      selectedColor: theme.colorScheme.primary,
      backgroundColor: theme.colorScheme.surface,
      side: BorderSide(
        color: isSelected
            ? theme.colorScheme.primary
            : theme.colorScheme.outline,
        width: 1,
      ),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
      padding: EdgeInsets.symmetric(horizontal: 2.w, vertical: 0.5.h),
    );
  }

  Widget _buildTripsList(ThemeData theme) {
    return ListView.builder(
      padding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 1.h),
      itemCount: mockTrips.length,
      itemBuilder: (context, index) {
        final trip = mockTrips[index];
        return _buildTripCard(theme, trip);
      },
    );
  }

  Widget _buildTripCard(ThemeData theme, Map<String, dynamic> trip) {
    return Container(
      margin: EdgeInsets.only(bottom: 2.h),
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: theme.colorScheme.outline.withOpacity(0.2),
          width: 1,
        ),
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.06),
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
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      trip['vehicleName'],
                      style: theme.textTheme.titleMedium?.copyWith(
                        fontWeight: FontWeight.bold,
                        fontSize: 14.sp,
                      ),
                    ),
                    SizedBox(height: 0.3.h),
                    Text(
                      'Reg No: ${trip['regNumber']}',
                      style: theme.textTheme.bodySmall?.copyWith(
                        color: theme.colorScheme.onSurfaceVariant,
                        fontSize: 11.sp,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding: EdgeInsets.symmetric(horizontal: 2.w, vertical: 0.5.h),
                decoration: BoxDecoration(
                  color: theme.colorScheme.primaryContainer.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  trip['seatingLayout'],
                  style: theme.textTheme.labelSmall?.copyWith(
                    color: theme.colorScheme.primary,
                    fontWeight: FontWeight.w600,
                    fontSize: 10.sp,
                  ),
                ),
              ),
            ],
          ),
          SizedBox(height: 2.h),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    trip['departureTime'],
                    style: theme.textTheme.titleSmall?.copyWith(
                      fontWeight: FontWeight.bold,
                      fontSize: 15.sp,
                    ),
                  ),
                  SizedBox(height: 0.3.h),
                  Text(
                    'Departure',
                    style: theme.textTheme.bodySmall?.copyWith(
                      color: theme.colorScheme.onSurfaceVariant,
                      fontSize: 11.sp,
                    ),
                  ),
                ],
              ),
              Column(
                children: [
                  CustomIconWidget(
                    iconName: 'arrow_forward',
                    color: theme.colorScheme.primary,
                    size: 20,
                  ),
                  SizedBox(height: 0.3.h),
                  Text(
                    trip['duration'],
                    style: theme.textTheme.labelSmall?.copyWith(
                      color: theme.colorScheme.onSurfaceVariant,
                      fontSize: 10.sp,
                    ),
                  ),
                ],
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    trip['arrivalTime'],
                    style: theme.textTheme.titleSmall?.copyWith(
                      fontWeight: FontWeight.bold,
                      fontSize: 15.sp,
                    ),
                  ),
                  SizedBox(height: 0.3.h),
                  Text(
                    'Arrival',
                    style: theme.textTheme.bodySmall?.copyWith(
                      color: theme.colorScheme.onSurfaceVariant,
                      fontSize: 11.sp,
                    ),
                  ),
                ],
              ),
            ],
          ),
          SizedBox(height: 2.h),
          Divider(
            color: theme.colorScheme.outline.withOpacity(0.2),
            height: 1,
          ),
          SizedBox(height: 1.5.h),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    trip['baseFare'],
                    style: theme.textTheme.titleLarge?.copyWith(
                      fontWeight: FontWeight.bold,
                      color: theme.colorScheme.primary,
                      fontSize: 18.sp,
                    ),
                  ),
                  SizedBox(height: 0.3.h),
                  Row(
                    children: [
                      CustomIconWidget(
                        iconName: 'event_seat',
                        color: trip['availableSeats'] <= 5
                            ? theme.colorScheme.error
                            : theme.colorScheme.secondary,
                        size: 14,
                      ),
                      SizedBox(width: 1.w),
                      Text(
                        '${trip['availableSeats']} Seats Left',
                        style: theme.textTheme.bodySmall?.copyWith(
                          color: trip['availableSeats'] <= 5
                              ? theme.colorScheme.error
                              : theme.colorScheme.secondary,
                          fontWeight: FontWeight.w600,
                          fontSize: 11.sp,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              ElevatedButton(
                onPressed: () => _navigateToSeatSelection(trip['id']),
                style: ElevatedButton.styleFrom(
                  backgroundColor: theme.colorScheme.secondary,
                  foregroundColor: theme.colorScheme.onSecondary,
                  padding: EdgeInsets.symmetric(horizontal: 5.w, vertical: 1.2.h),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  elevation: 0,
                ),
                child: Text(
                  'SELECT SEATS',
                  style: theme.textTheme.labelLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                    fontSize: 12.sp,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
