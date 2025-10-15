# Universal Navigation Components - Usage Guide

## Overview

This guide explains how to use the two universal navigation components in your Enugu Smart Bus Flutter app:

1. **CustomBottomNavBar** - Floating bottom navigation with auto-hide on scroll
2. **CustomSideDrawer** - Professional side drawer menu

Both components are fully integrated with the app's theme and design system.

---

## 1. Custom Floating Bottom Navigation Bar

### File Location
`lib/widgets/custom_bottom_nav_bar.dart`

### Features
- ✅ 4 navigation tabs (Book Trip, My Tickets, Luggage, Profile)
- ✅ Floating design with rounded corners and shadow
- ✅ Auto-hides when scrolling down, shows when scrolling up
- ✅ Smooth slide animations
- ✅ Theme-consistent styling
- ✅ Responsive sizing using Sizer package

### Basic Usage

```dart
import 'package:flutter/material.dart';
import '../../widgets/custom_bottom_nav_bar.dart';

class MyScreen extends StatefulWidget {
  @override
  State<MyScreen> createState() => _MyScreenState();
}

class _MyScreenState extends State<MyScreen> {
  final ScrollController _scrollController = ScrollController();
  int _currentNavIndex = 0;

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _onNavBarTap(int index) {
    setState(() {
      _currentNavIndex = index;
    });
    
    // Handle navigation based on index
    switch (index) {
      case 0: // Book Trip (Home)
        // Navigate to home/booking screen
        break;
      case 1: // My Tickets
        // Navigate to tickets screen
        break;
      case 2: // Luggage
        // Navigate to luggage screen
        break;
      case 3: // Profile
        // Navigate to profile screen
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: ListView(
        controller: _scrollController,
        children: [
          // Your content here
        ],
      ),
      bottomNavigationBar: CustomBottomNavBar(
        scrollController: _scrollController,
        currentIndex: _currentNavIndex,
        onTap: _onNavBarTap,
      ),
    );
  }
}
```

### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `scrollController` | `ScrollController` | Yes | Controller for the scrollable content to enable auto-hide |
| `currentIndex` | `int` | No (default: 0) | Currently active tab index (0-3) |
| `onTap` | `Function(int)` | Yes | Callback when a tab is tapped |

### Tab Indices

| Index | Label | Icon | Purpose |
|-------|-------|------|---------|
| 0 | Book Trip | directions_bus | Main booking/home screen |
| 1 | My Tickets | confirmation_number | User's booked tickets |
| 2 | Luggage | luggage | Luggage management |
| 3 | Profile | person | User profile settings |

### Important Notes

1. **ScrollController is Required**: The bottom nav needs a ScrollController to detect scroll direction and auto-hide/show.

2. **Use with ListView/SingleChildScrollView**: Works best with scrollable widgets:
   ```dart
   ListView(controller: _scrollController, ...)
   SingleChildScrollView(controller: _scrollController, ...)
   ```

3. **Bottom Padding**: Add extra padding to your last content item to account for the floating nav bar:
   ```dart
   SizedBox(height: 10.h), // At the end of your list
   ```

---

## 2. Custom Side Drawer

### File Location
`lib/widgets/custom_side_drawer.dart`

### Features
- ✅ Professional header with app logo and user info
- ✅ Menu items: Agent Management, Cancellations, About Us, Logout
- ✅ Logout confirmation dialog
- ✅ Theme-consistent styling
- ✅ Footer with version info

### Basic Usage

```dart
import 'package:flutter/material.dart';
import '../../widgets/custom_side_drawer.dart';

class MyScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: Builder(
          builder: (context) => IconButton(
            onPressed: () => Scaffold.of(context).openDrawer(),
            icon: Icon(Icons.menu),
          ),
        ),
        title: Text('My Screen'),
      ),
      drawer: CustomSideDrawer(),
      body: // Your content
    );
  }
}
```

### Customization

The drawer is currently set with placeholder navigation. To customize the menu items:

1. **Open** `lib/widgets/custom_side_drawer.dart`
2. **Find** the `_buildDrawerItem` calls in the `ListView`
3. **Update** the `onTap` callbacks to navigate to your screens:

```dart
_buildDrawerItem(
  context: context,
  theme: theme,
  icon: 'people',
  title: 'Agent Management',
  onTap: () {
    Navigator.pop(context); // Close drawer
    Navigator.pushNamed(context, '/agent-management');
  },
),
```

### Header Customization

To show actual user information instead of placeholder:

```dart
// In _buildDrawerHeader method
Text(
  user.fullName ?? 'Guest User', // Use actual user data
  style: theme.textTheme.titleMedium?.copyWith(...),
),
Text(
  user.email ?? 'guest@email.com', // Use actual user email
  style: theme.textTheme.bodySmall?.copyWith(...),
),
```

---

## 3. Complete Example (HomeScreen)

See `lib/presentation/home_screen/home_screen.dart` for a complete implementation showing:

- ✅ Both navigation components working together
- ✅ Scroll-responsive bottom nav
- ✅ Drawer integration
- ✅ Professional UI with cards and content
- ✅ Proper spacing and padding

### Key Code Snippet

```dart
class _HomeScreenState extends State<HomeScreen> {
  final ScrollController _scrollController = ScrollController();
  int _currentNavIndex = 0;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: Builder(
          builder: (context) => IconButton(
            onPressed: () => Scaffold.of(context).openDrawer(),
            icon: Icon(Icons.menu),
          ),
        ),
        title: Text('Enugu Smart Bus'),
      ),
      drawer: CustomSideDrawer(),
      body: ListView(
        controller: _scrollController,
        children: [
          // Your content
          SizedBox(height: 10.h), // Bottom padding for nav bar
        ],
      ),
      bottomNavigationBar: CustomBottomNavBar(
        scrollController: _scrollController,
        currentIndex: _currentNavIndex,
        onTap: _onNavBarTap,
      ),
    );
  }
}
```

---

## 4. Integration Checklist

When adding navigation to a new screen:

- [ ] Create a `ScrollController` in your state
- [ ] Dispose the `ScrollController` in dispose method
- [ ] Attach controller to your scrollable widget (ListView, etc.)
- [ ] Add `CustomBottomNavBar` to bottomNavigationBar
- [ ] Add `CustomSideDrawer` to drawer (optional)
- [ ] Implement `onTap` callback for bottom nav
- [ ] Add bottom padding to last content item (10.h recommended)
- [ ] Update drawer menu items with actual navigation logic

---

## 5. Theming

Both components automatically use the app's theme from `lib/theme/app_theme.dart`:

- **Primary Color**: Deep blue (#1a237e)
- **Surface Color**: White (light mode)
- **Text**: Inter font family
- **Spacing**: Responsive using Sizer package

No additional styling is needed unless you want to customize further.

---

## 6. Testing the Components

To test the navigation components:

1. **Run the app** in Android Studio or your emulator
2. **Navigate to Home Screen**: After login, go to `/home-screen`
3. **Test Bottom Nav**:
   - Tap different tabs to see selection changes
   - Scroll down to see the nav bar hide
   - Scroll up to see it appear again
4. **Test Side Drawer**:
   - Tap the menu icon in the app bar
   - Try different menu items
   - Test the logout button and confirmation dialog

---

## 7. Common Issues & Solutions

### Bottom Nav Not Hiding/Showing

**Problem**: The nav bar doesn't respond to scrolling.

**Solution**: Make sure you passed a `ScrollController` to both the scrollable widget AND the `CustomBottomNavBar`.

```dart
// Correct ✅
final _scrollController = ScrollController();

ListView(
  controller: _scrollController, // Attached here
  ...
)

CustomBottomNavBar(
  scrollController: _scrollController, // And passed here
  ...
)
```

### Drawer Menu Items Not Working

**Problem**: Clicking menu items doesn't navigate.

**Solution**: Update the `onTap` callbacks in `custom_side_drawer.dart` with your actual routes:

```dart
_buildDrawerItem(
  ...
  onTap: () {
    Navigator.pop(context);
    Navigator.pushNamed(context, '/your-route');
  },
),
```

### Content Hidden Behind Bottom Nav

**Problem**: Last items in your list are hidden behind the floating nav bar.

**Solution**: Add bottom padding at the end of your scrollable content:

```dart
ListView(
  children: [
    // Your content
    SizedBox(height: 10.h), // Add this at the end
  ],
)
```

---

## 8. Advanced Customization

### Changing Tab Icons

Edit `lib/widgets/custom_bottom_nav_bar.dart` and update the icon names:

```dart
BottomNavigationBarItem(
  icon: CustomIconWidget(
    iconName: 'your_icon_name', // Change this
    size: 20,
  ),
  label: 'Your Label',
),
```

Available icons are in `lib/widgets/custom_icon_widget.dart`.

### Adding More Drawer Items

In `lib/widgets/custom_side_drawer.dart`, add more `_buildDrawerItem` calls:

```dart
_buildDrawerItem(
  context: context,
  theme: theme,
  icon: 'settings',
  title: 'Settings',
  onTap: () {
    Navigator.pop(context);
    Navigator.pushNamed(context, '/settings');
  },
),
```

---

## 9. Next Steps

1. Create the missing screens (Tickets, Luggage, Profile)
2. Update drawer menu navigation to actual routes
3. Integrate with user authentication to show real user data
4. Add backend API integration for data fetching

---

## Support

For questions or issues:
- Check Flutter documentation: https://docs.flutter.dev/
- Review the implementation in `lib/presentation/home_screen/home_screen.dart`
- Test in Android Studio with hot reload for quick iteration

---

**Last Updated**: October 15, 2025  
**Version**: 1.0.0
