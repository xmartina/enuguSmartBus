import 'package:flutter/material.dart';
import 'package:sizer/sizer.dart';

import '../../../core/app_export.dart';
import '../../../theme/app_theme.dart';

class PageIndicatorWidget extends StatelessWidget {
  final int currentIndex;
  final int totalPages;

  const PageIndicatorWidget({
    Key? key,
    required this.currentIndex,
    required this.totalPages,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(
        totalPages,
        (index) => AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          margin: EdgeInsets.symmetric(horizontal: 1.w),
          height: 1.h,
          width: currentIndex == index ? 6.w : 2.w,
          decoration: BoxDecoration(
            color: currentIndex == index
                ? AppTheme.lightTheme.colorScheme.primary
                : AppTheme.lightTheme.colorScheme.primary
                    .withOpacity(0.3),
            borderRadius: BorderRadius.circular(0.5.h),
          ),
        ),
      ),
    );
  }
}
