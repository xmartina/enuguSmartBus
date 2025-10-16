
import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../../core/app_export.dart';
import '../../widgets/custom_bottom_nav_bar.dart';
import '../../widgets/custom_icon_widget.dart';
import '../../widgets/custom_side_drawer.dart';
import '../trip_list_screen/trip_list_screen.dart';
import '../tickets_list_screen/tickets_list_screen.dart';
import '../profile_screen/profile_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({Key? key}) : super(key: key);

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> with SingleTickerProviderStateMixin {
  final ScrollController _scrollController = ScrollController();
  int _currentNavIndex = 0;
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;

  // Trip search form controllers
  final TextEditingController _departureController = TextEditingController();
  final TextEditingController _destinationController = TextEditingController();
  DateTime _selectedDate = DateTime.now();
  int _passengerCount = 1;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    );
    _fadeAnimation = CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    );
    _animationController.forward();
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _departureController.dispose();
    _destinationController.dispose();
    _animationController.dispose();
    super.dispose();
  }

  void _onNavBarTap(int index) {
    switch (index) {
      case 0:
        if (_scrollController.hasClients) {
          _scrollController.animateTo(
            0,
            duration: const Duration(milliseconds: 300),
            curve: Curves.easeOut,
          );
        }
        setState(() {
          _currentNavIndex = 0;
        });
        break;
      case 1:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const TicketsListScreen(),
          ),
        ).then((_) {
          setState(() {
            _currentNavIndex = 0;
          });
        });
        break;
      case 2:
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Luggage tracking feature coming soon.'),
            backgroundColor: Theme.of(context).colorScheme.primary,
          ),
        );
        setState(() {
          _currentNavIndex = 0;
        });
        break;
      case 3:
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const ProfileScreen(),
          ),
        ).then((_) {
          setState(() {
            _currentNavIndex = 0;
          });
        });
        break;
    }
  }

  void _swapLocations() {
    setState(() {
      final temp = _departureController.text;
      _departureController.text = _destinationController.text;
      _destinationController.text = temp;
    });
  }

  void _incrementPassengers() {
    setState(() {
      _passengerCount++;
    });
  }

  void _decrementPassengers() {
    if (_passengerCount > 1) {
      setState(() {
        _passengerCount--;
      });
    }
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime now = DateTime.now();
    final DateTime maxDate = now.add(const Duration(days: 90));

    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: now,
      lastDate: maxDate,
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(
              primary: AppTheme.lightTheme.colorScheme.primary,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null && picked != _selectedDate) {
      setState(() {
        _selectedDate = picked;
      });
    }
  }

  void _searchTrips() {
    if (_departureController.text.isEmpty || _destinationController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text('Please select all locations and dates.'),
          backgroundColor: AppTheme.lightTheme.colorScheme.error,
          behavior: SnackBarBehavior.floating,
        ),
      );
      return;
    }

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => TripListScreen(
          fromLocation: _departureController.text,
          toLocation: _destinationController.text,
          date: '${_selectedDate.day}/${_selectedDate.month}/${_selectedDate.year}',
          passengers: _passengerCount,
        ),
      ),
    );
  }

  void _viewPopularRoute(String routeName) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => TripListScreen(
          routeName: routeName,
        ),
      ),
    );
  }

  void _showAgentBenefitsDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(
          'Agent Benefits Coming Soon',
          style: AppTheme.lightTheme.textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.bold,
            fontSize: 13.sp,
          ),
        ),
        content: Text(
          'We are preparing an exclusive agent program with amazing benefits. Stay tuned!',
          style: AppTheme.lightTheme.textTheme.bodyMedium?.copyWith(
            fontSize: 11.sp,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text(
              'Got it',
              style: TextStyle(
                color: AppTheme.lightTheme.colorScheme.primary,
                fontWeight: FontWeight.w600,
                fontSize: 11.sp,
              ),
            ),
          ),
        ],
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
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
        elevation: 1,
        leading: Builder(
          builder: (context) => IconButton(
            onPressed: () => Scaffold.of(context).openDrawer(),
            icon: CustomIconWidget(
              iconName: 'menu',
              color: theme.colorScheme.onPrimary,
              size: 22,
            ),
          ),
        ),
        title: Text(
          'Enugu Smart Bus',
          style: theme.textTheme.titleLarge?.copyWith(
            color: theme.colorScheme.onPrimary,
            fontWeight: FontWeight.w600,
            fontSize: 14.sp,
          ),
        ),
        centerTitle: true,
        actions: [
          IconButton(
            onPressed: () {},
            icon: CustomIconWidget(
              iconName: 'notifications',
              color: theme.colorScheme.onPrimary,
              size: 22,
            ),
          ),
        ],
      ),
      drawer: const CustomSideDrawer(),
      body: _buildBody(theme),
      bottomNavigationBar: CustomBottomNavBar(
        scrollController: _scrollController,
        currentIndex: _currentNavIndex,
        onTap: _onNavBarTap,
      ),
    );
  }

  Widget _buildBody(ThemeData theme) {
    return FadeTransition(
      opacity: _fadeAnimation,
      child: ListView(
        controller: _scrollController,
        padding: EdgeInsets.symmetric(horizontal: 3.5.w, vertical: 1.5.h),
        children: [
          _buildTripSearchCard(theme),
          SizedBox(height: 2.h),
          _buildPopularRoutesSection(theme),
          SizedBox(height: 2.h),
          _buildAgentPromotionCard(theme),
          SizedBox(height: 12.h),
        ],
      ),
    );
  }

  Widget _buildTripSearchCard(ThemeData theme) {
    return Container(
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(10),
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.08),
            blurRadius: 12,
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
                iconName: 'search',
                color: theme.colorScheme.primary,
                size: 20,
              ),
              SizedBox(width: 2.w),
              Text(
                'Book Your Trip',
                style: theme.textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                  fontSize: 13.sp,
                ),
              ),
            ],
          ),
          SizedBox(height: 2.h),
          _buildLocationInput(
            theme: theme,
            controller: _departureController,
            label: 'From',
            hint: 'Select departure city',
            icon: 'location_on',
          ),
          SizedBox(height: 1.2.h),
          Center(
            child: InkWell(
              onTap: _swapLocations,
              borderRadius: BorderRadius.circular(20),
              child: Container(
                padding: EdgeInsets.all(1.w),
                decoration: BoxDecoration(
                  color: theme.colorScheme.primary.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: CustomIconWidget(
                  iconName: 'swap_vert',
                  color: theme.colorScheme.primary,
                  size: 18,
                ),
              ),
            ),
          ),
          SizedBox(height: 1.2.h),
          _buildLocationInput(
            theme: theme,
            controller: _destinationController,
            label: 'To',
            hint: 'Select destination city',
            icon: 'location_on',
          ),
          SizedBox(height: 1.5.h),
          Row(
            children: [
              Expanded(
                child: _buildDatePicker(theme),
              ),
              SizedBox(width: 2.5.w),
              Expanded(
                child: _buildPassengerCounter(theme),
              ),
            ],
          ),
          SizedBox(height: 2.h),
          SizedBox(
            width: double.infinity,
            height: 5.h,
            child: ElevatedButton(
              onPressed: _searchTrips,
              style: ElevatedButton.styleFrom(
                backgroundColor: theme.colorScheme.primary,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
                elevation: 1,
                padding: EdgeInsets.symmetric(horizontal: 2.w, vertical: 1.h),
              ),
              child: Text(
                'SEARCH TRIPS',
                style: theme.textTheme.titleMedium?.copyWith(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 11.sp,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLocationInput({
    required ThemeData theme,
    required TextEditingController controller,
    required String label,
    required String hint,
    required String icon,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: theme.textTheme.bodyMedium?.copyWith(
            fontWeight: FontWeight.w600,
            fontSize: 11.sp,
            color: theme.colorScheme.onSurfaceVariant,
          ),
        ),
        SizedBox(height: 0.6.h),
        TextField(
          controller: controller,
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: theme.textTheme.bodyMedium?.copyWith(
              color: theme.colorScheme.onSurfaceVariant.withOpacity(0.5),
              fontSize: 11.sp,
            ),
            prefixIcon: Padding(
              padding: EdgeInsets.all(2.5.w),
              child: CustomIconWidget(
                iconName: icon,
                color: theme.colorScheme.primary,
                size: 18,
              ),
            ),
            filled: true,
            fillColor: theme.colorScheme.surfaceVariant.withOpacity(0.3),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(8),
              borderSide: BorderSide.none,
            ),
            contentPadding: EdgeInsets.symmetric(
              horizontal: 3.w,
              vertical: 1.2.h,
            ),
          ),
          style: theme.textTheme.bodyMedium?.copyWith(
            fontSize: 11.sp,
          ),
        ),
      ],
    );
  }

  Widget _buildDatePicker(ThemeData theme) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Date',
          style: theme.textTheme.bodyMedium?.copyWith(
            fontWeight: FontWeight.w600,
            fontSize: 11.sp,
            color: theme.colorScheme.onSurfaceVariant,
          ),
        ),
        SizedBox(height: 0.6.h),
        InkWell(
          onTap: () => _selectDate(context),
          borderRadius: BorderRadius.circular(8),
          child: Container(
            padding: EdgeInsets.symmetric(horizontal: 2.5.w, vertical: 1.2.h),
            decoration: BoxDecoration(
              color: theme.colorScheme.surfaceVariant.withOpacity(0.3),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Row(
              children: [
                CustomIconWidget(
                  iconName: 'calendar_today',
                  color: theme.colorScheme.primary,
                  size: 16,
                ),
                SizedBox(width: 1.5.w),
                Expanded(
                  child: Text(
                    '${_selectedDate.day}/${_selectedDate.month}/${_selectedDate.year}',
                    style: theme.textTheme.bodyMedium?.copyWith(
                      fontSize: 11.sp,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPassengerCounter(ThemeData theme) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Passengers',
          style: theme.textTheme.bodyMedium?.copyWith(
            fontWeight: FontWeight.w600,
            fontSize: 11.sp,
            color: theme.colorScheme.onSurfaceVariant,
          ),
        ),
        SizedBox(height: 0.6.h),
        Container(
          padding: EdgeInsets.symmetric(horizontal: 1.5.w, vertical: 0.3.h),
          decoration: BoxDecoration(
            color: theme.colorScheme.surfaceVariant.withOpacity(0.3),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              IconButton(
                onPressed: _decrementPassengers,
                icon: CustomIconWidget(
                  iconName: 'remove_circle',
                  color: _passengerCount > 1
                      ? theme.colorScheme.primary
                      : theme.colorScheme.onSurfaceVariant.withOpacity(0.3),
                  size: 20,
                ),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
              Text(
                '$_passengerCount',
                style: theme.textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.bold,
                  fontSize: 12.sp,
                ),
              ),
              IconButton(
                onPressed: _incrementPassengers,
                icon: CustomIconWidget(
                  iconName: 'add_circle',
                  color: theme.colorScheme.primary,
                  size: 20,
                ),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildPopularRoutesSection(ThemeData theme) {
    final List<Map<String, String>> routes = [
      {'route': 'Enugu ↔ Abuja', 'price': 'From NGN 18,500'},
      {'route': 'Enugu ↔ Lagos', 'price': 'From NGN 22,000'},
      {'route': 'Enugu ↔ Port Harcourt', 'price': 'From NGN 12,500'},
      {'route': 'Enugu ↔ Onitsha', 'price': 'From NGN 5,500'},
      {'route': 'Enugu ↔ Aba', 'price': 'From NGN 8,000'},
    ];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: EdgeInsets.symmetric(horizontal: 1.w),
          child: Text(
            'Popular Routes',
            style: theme.textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.bold,
              fontSize: 13.sp,
            ),
          ),
        ),
        SizedBox(height: 1.2.h),
        SizedBox(
          height: 18.h,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: routes.length,
            itemBuilder: (context, index) {
              return Padding(
                padding: EdgeInsets.only(
                  left: index == 0 ? 0 : 0,
                  right: 2.5.w,
                ),
                child: _buildPopularRouteCard(
                  theme: theme,
                  routeName: routes[index]['route']!,
                  price: routes[index]['price']!,
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  Widget _buildPopularRouteCard({
    required ThemeData theme,
    required String routeName,
    required String price,
  }) {
    return Container(
      width: 60.w,
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            theme.colorScheme.primary,
            theme.colorScheme.primary.withOpacity(0.8),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(10),
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.primary.withOpacity(0.25),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              CustomIconWidget(
                iconName: 'directions_bus',
                color: theme.colorScheme.onPrimary,
                size: 24,
              ),
              SizedBox(width: 2.w),
              Expanded(
                child: Text(
                  routeName,
                  style: theme.textTheme.titleMedium?.copyWith(
                    color: theme.colorScheme.onPrimary,
                    fontWeight: FontWeight.bold,
                    fontSize: 12.sp,
                  ),
                ),
              ),
            ],
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                price,
                style: theme.textTheme.bodyLarge?.copyWith(
                  color: theme.colorScheme.onPrimary,
                  fontWeight: FontWeight.w600,
                  fontSize: 11.sp,
                ),
              ),
              SizedBox(height: 0.8.h),
              SizedBox(
                width: double.infinity,
                height: 4.h,
                child: ElevatedButton(
                  onPressed: () => _viewPopularRoute(routeName),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: theme.colorScheme.onPrimary,
                    foregroundColor: theme.colorScheme.primary,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    padding: EdgeInsets.symmetric(horizontal: 2.w, vertical: 0.8.h),
                  ),
                  child: Text(
                    'VIEW TRIPS',
                    style: theme.textTheme.labelLarge?.copyWith(
                      fontWeight: FontWeight.bold,
                      fontSize: 10.sp,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildAgentPromotionCard(ThemeData theme) {
    return Container(
      padding: EdgeInsets.all(3.w),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            theme.colorScheme.tertiary,
            theme.colorScheme.tertiary.withOpacity(0.7),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(10),
        boxShadow: [
          BoxShadow(
            color: theme.colorScheme.shadow.withOpacity(0.12),
            blurRadius: 12,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: EdgeInsets.all(1.5.w),
                decoration: BoxDecoration(
                  color: theme.colorScheme.onTertiary.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: CustomIconWidget(
                  iconName: 'groups',
                  color: theme.colorScheme.onTertiary,
                  size: 24,
                ),
              ),
              SizedBox(width: 2.5.w),
              Expanded(
                child: Text(
                  'Join Our Agent Network',
                  style: theme.textTheme.titleLarge?.copyWith(
                    color: theme.colorScheme.onTertiary,
                    fontWeight: FontWeight.bold,
                    fontSize: 13.sp,
                  ),
                ),
              ),
            ],
          ),
          SizedBox(height: 1.5.h),
          Text(
            'Become a partner agent and enjoy exclusive benefits, higher commissions, and priority support.',
            style: theme.textTheme.bodyMedium?.copyWith(
              color: theme.colorScheme.onTertiary.withOpacity(0.9),
              fontSize: 11.sp,
              height: 1.4,
            ),
          ),
          SizedBox(height: 2.h),
          SizedBox(
            width: double.infinity,
            height: 5.h,
            child: ElevatedButton(
              onPressed: _showAgentBenefitsDialog,
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: theme.colorScheme.tertiary,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
                elevation: 0,
                padding: EdgeInsets.symmetric(horizontal: 2.w, vertical: 1.h),
              ),
              child: Text(
                'Explore Benefits',
                style: theme.textTheme.titleMedium?.copyWith(
                  color: theme.colorScheme.tertiary,
                  fontWeight: FontWeight.bold,
                  fontSize: 11.sp,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
