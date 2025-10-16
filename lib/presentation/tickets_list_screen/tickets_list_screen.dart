import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';
import 'ticket_details_screen.dart';

enum TicketFilter { upcoming, past, cancelled }

enum TicketStatus { upcoming, completed, cancelled }

class TicketsListScreen extends StatefulWidget {
  final TicketFilter? initialFilter;
  
  const TicketsListScreen({
    Key? key,
    this.initialFilter,
  }) : super(key: key);

  @override
  State<TicketsListScreen> createState() => _TicketsListScreenState();
}

class _TicketsListScreenState extends State<TicketsListScreen> {
  late TicketFilter _selectedFilter;
  
  @override
  void initState() {
    super.initState();
    _selectedFilter = widget.initialFilter ?? TicketFilter.upcoming;
  }

  // Mock ticket data
  final List<Map<String, dynamic>> _allTickets = [
    {
      'ticketToken': '#AXB-9321',
      'status': TicketStatus.upcoming,
      'route': 'Lagos → Enugu',
      'dateTime': 'Wed, Dec 18 @ 10:00 AM',
      'seats': ['A1', 'A2', 'B1'],
      'totalFare': 30000.0,
    },
    {
      'ticketToken': '#BYC-4567',
      'status': TicketStatus.upcoming,
      'route': 'Abuja → Port Harcourt',
      'dateTime': 'Fri, Dec 20 @ 08:00 AM',
      'seats': ['C2'],
      'totalFare': 18500.0,
    },
    {
      'ticketToken': '#CZD-7890',
      'status': TicketStatus.completed,
      'route': 'Enugu → Lagos',
      'dateTime': 'Mon, Nov 25 @ 07:30 AM',
      'seats': ['B3', 'B4'],
      'totalFare': 25000.0,
    },
    {
      'ticketToken': '#DEF-2345',
      'status': TicketStatus.completed,
      'route': 'Port Harcourt → Abuja',
      'dateTime': 'Sat, Nov 16 @ 06:00 PM',
      'seats': ['A1'],
      'totalFare': 19500.0,
    },
    {
      'ticketToken': '#EFG-6789',
      'status': TicketStatus.cancelled,
      'route': 'Lagos → Abuja',
      'dateTime': 'Thu, Dec 12 @ 09:00 AM',
      'seats': ['A3', 'B2'],
      'totalFare': 22000.0,
    },
    {
      'ticketToken': '#FGH-1234',
      'status': TicketStatus.cancelled,
      'route': 'Enugu → Port Harcourt',
      'dateTime': 'Tue, Dec 10 @ 11:30 AM',
      'seats': ['C1', 'C2', 'C3'],
      'totalFare': 33500.0,
    },
  ];

  List<Map<String, dynamic>> get _filteredTickets {
    switch (_selectedFilter) {
      case TicketFilter.upcoming:
        return _allTickets.where((ticket) => ticket['status'] == TicketStatus.upcoming).toList();
      case TicketFilter.past:
        return _allTickets.where((ticket) => ticket['status'] == TicketStatus.completed).toList();
      case TicketFilter.cancelled:
        return _allTickets.where((ticket) => ticket['status'] == TicketStatus.cancelled).toList();
    }
  }

  Color _getStatusColor(ThemeData theme, TicketStatus status) {
    switch (status) {
      case TicketStatus.upcoming:
        return theme.colorScheme.secondary;
      case TicketStatus.completed:
        return theme.colorScheme.onSurfaceVariant;
      case TicketStatus.cancelled:
        return theme.colorScheme.error;
    }
  }

  String _getStatusLabel(TicketStatus status) {
    switch (status) {
      case TicketStatus.upcoming:
        return 'Upcoming';
      case TicketStatus.completed:
        return 'Completed';
      case TicketStatus.cancelled:
        return 'Cancelled';
    }
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
          'My Tickets',
          style: theme.textTheme.titleLarge?.copyWith(
            color: theme.colorScheme.onPrimary,
            fontWeight: FontWeight.w600,
          ),
        ),
        centerTitle: true,
      ),
      body: Column(
        children: [
          _buildSegmentedControl(theme),
          Expanded(
            child: _filteredTickets.isEmpty
                ? _buildEmptyState(theme)
                : ListView.builder(
                    padding: EdgeInsets.symmetric(horizontal: 3.w, vertical: 2.h),
                    itemCount: _filteredTickets.length,
                    itemBuilder: (context, index) {
                      return _buildTicketCard(theme, _filteredTickets[index]);
                    },
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildSegmentedControl(ThemeData theme) {
    return Container(
      margin: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: theme.colorScheme.outline.withOpacity(0.3),
          width: 1,
        ),
      ),
      child: Row(
        children: [
          Expanded(
            child: _buildSegmentButton(
              theme,
              'Upcoming',
              TicketFilter.upcoming,
              _selectedFilter == TicketFilter.upcoming,
            ),
          ),
          Expanded(
            child: _buildSegmentButton(
              theme,
              'Past Trips',
              TicketFilter.past,
              _selectedFilter == TicketFilter.past,
            ),
          ),
          Expanded(
            child: _buildSegmentButton(
              theme,
              'Cancelled',
              TicketFilter.cancelled,
              _selectedFilter == TicketFilter.cancelled,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSegmentButton(
    ThemeData theme,
    String label,
    TicketFilter filter,
    bool isSelected,
  ) {
    return GestureDetector(
      onTap: () {
        setState(() {
          _selectedFilter = filter;
        });
      },
      child: Container(
        padding: EdgeInsets.symmetric(vertical: 1.5.h),
        decoration: BoxDecoration(
          color: isSelected
              ? theme.colorScheme.primary
              : Colors.transparent,
          borderRadius: BorderRadius.circular(10),
        ),
        child: Text(
          label,
          style: theme.textTheme.labelMedium?.copyWith(
            color: isSelected
                ? theme.colorScheme.onPrimary
                : theme.colorScheme.onSurfaceVariant,
            fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
            fontSize: 11.sp,
          ),
          textAlign: TextAlign.center,
        ),
      ),
    );
  }

  Widget _buildTicketCard(ThemeData theme, Map<String, dynamic> ticket) {
    final status = ticket['status'] as TicketStatus;
    final seats = ticket['seats'] as List<String>;
    final ticketToken = ticket['ticketToken'] as String;

    return Container(
      margin: EdgeInsets.only(bottom: 2.h),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: theme.colorScheme.outline.withOpacity(0.2),
          width: 1,
        ),
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.05),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: EdgeInsets.all(3.w),
            decoration: BoxDecoration(
              color: _getStatusColor(theme, status).withOpacity(0.1),
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(12),
                topRight: Radius.circular(12),
              ),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  ticketToken,
                  style: theme.textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                    fontSize: 15.sp,
                  ),
                ),
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 2.w, vertical: 0.5.h),
                  decoration: BoxDecoration(
                    color: _getStatusColor(theme, status),
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text(
                    _getStatusLabel(status).toUpperCase(),
                    style: theme.textTheme.labelSmall?.copyWith(
                      color: status == TicketStatus.completed
                          ? theme.colorScheme.onSecondary
                          : Colors.white,
                      fontWeight: FontWeight.w600,
                      fontSize: 9.sp,
                    ),
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: EdgeInsets.all(3.w),
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
                        ticket['route'] as String,
                        style: theme.textTheme.titleSmall?.copyWith(
                          fontWeight: FontWeight.w600,
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
                      ticket['dateTime'] as String,
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
                    Text(
                      'Seats: ${seats.length} (${seats.join(", ")})',
                      style: theme.textTheme.bodySmall?.copyWith(
                        fontSize: 12.sp,
                        color: theme.colorScheme.onSurfaceVariant,
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 1.5.h),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Total Fare:',
                      style: theme.textTheme.labelMedium?.copyWith(
                        fontWeight: FontWeight.w600,
                        fontSize: 12.sp,
                      ),
                    ),
                    Text(
                      '₦${(ticket['totalFare'] as double).toStringAsFixed(0)}',
                      style: theme.textTheme.titleMedium?.copyWith(
                        color: theme.colorScheme.primary,
                        fontWeight: FontWeight.bold,
                        fontSize: 16.sp,
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 2.h),
                SizedBox(
                  width: double.infinity,
                  height: 5.h,
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => TicketDetailsScreen(
                            ticketToken: ticketToken,
                          ),
                        ),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: theme.colorScheme.primary,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                      ),
                      elevation: 1,
                    ),
                    child: Text(
                      'VIEW TICKET',
                      style: theme.textTheme.labelLarge?.copyWith(
                        color: theme.colorScheme.onPrimary,
                        fontWeight: FontWeight.bold,
                        fontSize: 13.sp,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState(ThemeData theme) {
    String message;
    switch (_selectedFilter) {
      case TicketFilter.upcoming:
        message = 'No upcoming trips';
        break;
      case TicketFilter.past:
        message = 'No past trips';
        break;
      case TicketFilter.cancelled:
        message = 'No cancelled trips';
        break;
    }

    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          CustomIconWidget(
            iconName: 'confirmation_number',
            color: theme.colorScheme.onSurfaceVariant.withOpacity(0.3),
            size: 64,
          ),
          SizedBox(height: 2.h),
          Text(
            message,
            style: theme.textTheme.titleMedium?.copyWith(
              color: theme.colorScheme.onSurfaceVariant,
              fontSize: 15.sp,
            ),
          ),
        ],
      ),
    );
  }
}
