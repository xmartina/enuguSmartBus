import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';
import '../core/app_export.dart';
import 'custom_icon_widget.dart';

class CustomBottomNavBar extends StatefulWidget {
  final ScrollController scrollController;
  final int currentIndex;
  final Function(int) onTap;

  const CustomBottomNavBar({
    Key? key,
    required this.scrollController,
    this.currentIndex = 0,
    required this.onTap,
  }) : super(key: key);

  @override
  State<CustomBottomNavBar> createState() => _CustomBottomNavBarState();
}

class _CustomBottomNavBarState extends State<CustomBottomNavBar>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<Offset> _slideAnimation;
  bool _isVisible = true;
  double _previousScrollOffset = 0.0;

  @override
  void initState() {
    super.initState();
    
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 200),
      vsync: this,
    );

    _slideAnimation = Tween<Offset>(
      begin: Offset.zero,
      end: const Offset(0, 1),
    ).animate(CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    ));

    widget.scrollController.addListener(_handleScroll);
  }

  void _handleScroll() {
    final currentScrollOffset = widget.scrollController.offset;
    final scrollDelta = currentScrollOffset - _previousScrollOffset;

    if (scrollDelta > 5 && _isVisible) {
      setState(() {
        _isVisible = false;
      });
      _animationController.forward();
    } else if (scrollDelta < -5 && !_isVisible) {
      setState(() {
        _isVisible = true;
      });
      _animationController.reverse();
    }

    _previousScrollOffset = currentScrollOffset;
  }

  @override
  void dispose() {
    widget.scrollController.removeListener(_handleScroll);
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    
    return SlideTransition(
      position: _slideAnimation,
      child: Container(
        margin: EdgeInsets.only(
          left: 4.w,
          right: 4.w,
          bottom: 2.h,
        ),
        decoration: BoxDecoration(
          color: theme.colorScheme.surface,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: theme.colorScheme.shadow.withOpacity(0.12),
              blurRadius: 12,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(12),
          child: BottomNavigationBar(
            currentIndex: widget.currentIndex,
            onTap: widget.onTap,
            backgroundColor: theme.colorScheme.surface,
            selectedItemColor: theme.colorScheme.primary,
            unselectedItemColor: theme.colorScheme.onSurfaceVariant,
            type: BottomNavigationBarType.fixed,
            elevation: 0,
            selectedFontSize: 10.sp,
            unselectedFontSize: 9.sp,
            iconSize: 18,
            items: [
              BottomNavigationBarItem(
                icon: Padding(
                  padding: EdgeInsets.only(bottom: 0.5.h),
                  child: const CustomIconWidget(
                    iconName: 'directions_bus',
                    size: 20,
                  ),
                ),
                activeIcon: Padding(
                  padding: EdgeInsets.only(bottom: 0.5.h),
                  child: CustomIconWidget(
                    iconName: 'directions_bus',
                    color: theme.colorScheme.primary,
                    size: 22,
                  ),
                ),
                label: 'Book Trip',
              ),
              BottomNavigationBarItem(
                icon: Padding(
                  padding: EdgeInsets.only(bottom: 0.5.h),
                  child: const CustomIconWidget(
                    iconName: 'confirmation_number',
                    size: 20,
                  ),
                ),
                activeIcon: Padding(
                  padding: EdgeInsets.only(bottom: 0.5.h),
                  child: CustomIconWidget(
                    iconName: 'confirmation_number',
                    color: theme.colorScheme.primary,
                    size: 22,
                  ),
                ),
                label: 'My Tickets',
              ),
              BottomNavigationBarItem(
                icon: Padding(
                  padding: EdgeInsets.only(bottom: 0.5.h),
                  child: const CustomIconWidget(
                    iconName: 'luggage',
                    size: 20,
                  ),
                ),
                activeIcon: Padding(
                  padding: EdgeInsets.only(bottom: 0.5.h),
                  child: CustomIconWidget(
                    iconName: 'luggage',
                    color: theme.colorScheme.primary,
                    size: 22,
                  ),
                ),
                label: 'Luggage',
              ),
              BottomNavigationBarItem(
                icon: Padding(
                  padding: EdgeInsets.only(bottom: 0.5.h),
                  child: const CustomIconWidget(
                    iconName: 'person',
                    size: 20,
                  ),
                ),
                activeIcon: Padding(
                  padding: EdgeInsets.only(bottom: 0.5.h),
                  child: CustomIconWidget(
                    iconName: 'person',
                    color: theme.colorScheme.primary,
                    size: 22,
                  ),
                ),
                label: 'Profile',
              ),
            ],
          ),
        ),
      ),
    );
  }
}
