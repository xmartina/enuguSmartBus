# Home Screen Implementation Guide

## Overview

The Home Screen is the main hub of the Enugu Smart Bus application. It provides a complete trip booking interface with enhanced search capabilities, popular routes discovery, and agent network promotion.

**File:** `lib/presentation/home_screen/home_screen.dart`

---

## 🎯 Key Features

### 1. Enhanced Trip Search Card

The primary feature is a comprehensive trip search interface positioned at the top of the screen.

#### Components:

**A. Location Inputs**
- **Departure Location**: Text input for starting city/stop
- **Destination Location**: Text input for arrival city/stop
- **Swap Button**: Icon button (swap_vert) to quickly swap departure and destination

**B. Date Picker**
- Displays currently selected date
- Opens native date picker on tap
- Constraint: Current day to +90 days from today
- Format: DD/MM/YYYY

**C. Passenger Counter**
- Compact widget with +/- buttons
- Minimum: 1 passenger
- No maximum limit
- Visual feedback when minimum is reached (disabled state)

**D. Search Button**
- Full-width prominent button labeled "SEARCH TRIPS"
- Validates inputs before navigation
- Shows SnackBar error if validation fails
- Navigates to TripListScreen on success

#### Validation Logic:
```dart
- Checks if departure location is not empty
- Checks if destination location is not empty
- Shows error message: "Please select all locations and dates."
- Only navigates if validation passes
```

---

### 2. Popular Routes Section

Horizontal scrollable section showcasing frequently traveled routes.

#### Structure:
- **Title**: "Popular Routes"
- **Layout**: Horizontal ListView with 5 route cards
- **Card Content**:
  - Route name with arrow (e.g., "Enugu ↔ Abuja")
  - Starting price (e.g., "From NGN 18,500")
  - Bus icon decoration
  - "VIEW TRIPS" button

#### Pre-configured Routes:
1. Enugu ↔ Abuja - From NGN 18,500
2. Enugu ↔ Lagos - From NGN 22,000
3. Enugu ↔ Port Harcourt - From NGN 12,500
4. Enugu ↔ Onitsha - From NGN 5,500
5. Enugu ↔ Aba - From NGN 8,000

#### Navigation:
Each "VIEW TRIPS" button navigates to TripListScreen with the route name passed as a parameter.

---

### 3. Agent/Loyalty Promotion Card

Call-to-action card encouraging users to join the agent network.

#### Content:
- **Icon**: Groups icon (representing community)
- **Title**: "Join Our Agent Network"
- **Description**: Brief text about benefits and exclusive offers
- **Button**: "Explore Benefits"

#### Action:
Clicking "Explore Benefits" displays a Dialog (not a new screen) with:
- Title: "Agent Benefits Coming Soon"
- Message: Information about upcoming program
- Single "Got it" button to dismiss

---

## 🎨 Design Principles

### Color Scheme
- **Primary Cards**: White surface with subtle shadows
- **Popular Routes**: Gradient using primary color (#1a237e)
- **Agent Promotion**: Gradient using tertiary color
- **Buttons**: Primary color with white text

### Typography
- **Card Headers**: 18sp, bold
- **Input Labels**: 13sp, semi-bold
- **Body Text**: 13-14sp, regular
- **Button Text**: 15sp, bold

### Spacing & Layout
- **Card Padding**: 4.w (responsive)
- **Section Gaps**: 3.h
- **Input Spacing**: 1.5.h between fields
- **Corner Radius**: 12-16px (contemporary rounded design)

### Responsive Design
All sizes use Sizer package:
- `w` for width percentages
- `h` for height percentages
- `sp` for font sizes

---

## 🔄 Navigation Flow

### From Home Screen:

```
HomeScreen
  ├─ [SEARCH TRIPS Button] ──────► TripListScreen (with search params)
  ├─ [Popular Route Card] ────────► TripListScreen (with route name)
  └─ [Explore Benefits] ──────────► Dialog (stays on Home)
```

### Parameters Passed to TripListScreen:

**From Search:**
- `fromLocation`: Departure city (String)
- `toLocation`: Destination city (String)
- `date`: Selected date formatted as DD/MM/YYYY (String)
- `passengers`: Number of passengers (int)

**From Popular Routes:**
- `routeName`: Pre-configured route name (String)

---

## 📱 User Interaction Flow

### Trip Search Flow:
1. User taps on "Departure" field and enters city name
2. User taps on "Destination" field and enters city name
3. (Optional) User taps swap button to reverse locations
4. User taps date picker and selects travel date
5. User adjusts passenger count using +/- buttons
6. User taps "SEARCH TRIPS" button
7. System validates inputs
8. If valid: Navigate to TripListScreen with parameters
9. If invalid: Show error SnackBar

### Popular Route Flow:
1. User scrolls horizontally through route cards
2. User taps "VIEW TRIPS" on desired route
3. System navigates to TripListScreen with route name

### Agent Promotion Flow:
1. User scrolls to bottom of screen
2. User reads promotion card
3. User taps "Explore Benefits"
4. Dialog appears with information
5. User taps "Got it" to dismiss

---

## 🧩 Widget Hierarchy

```
HomeScreen (StatefulWidget)
└── Scaffold
    ├── AppBar
    │   ├── Menu Button (opens drawer)
    │   ├── Title: "Enugu Smart Bus"
    │   └── Notifications Button
    ├── Drawer: CustomSideDrawer
    ├── Body: ListView
    │   ├── Trip Search Card
    │   │   ├── Header (icon + title)
    │   │   ├── Departure Input
    │   │   ├── Swap Button
    │   │   ├── Destination Input
    │   │   ├── Row
    │   │   │   ├── Date Picker
    │   │   │   └── Passenger Counter
    │   │   └── Search Button
    │   ├── Popular Routes Section
    │   │   ├── Title
    │   │   └── Horizontal ListView
    │   │       └── Route Cards (5)
    │   └── Agent Promotion Card
    │       ├── Header (icon + title)
    │       ├── Description
    │       └── Benefits Button
    └── BottomNavigationBar: CustomBottomNavBar
```

---

## 🔧 State Management

### State Variables:
```dart
ScrollController _scrollController;      // For bottom nav scroll detection
int _currentNavIndex;                   // Bottom nav active tab
TextEditingController _departureController;   // Departure input
TextEditingController _destinationController; // Destination input
DateTime _selectedDate;                 // Selected travel date
int _passengerCount;                   // Number of passengers (min: 1)
```

### Methods:
- `_swapLocations()` - Swaps departure and destination values
- `_incrementPassengers()` - Increases passenger count
- `_decrementPassengers()` - Decreases passenger count (min: 1)
- `_selectDate()` - Opens date picker dialog
- `_searchTrips()` - Validates and navigates to trip list
- `_viewPopularRoute()` - Navigates to trip list with route
- `_showAgentBenefitsDialog()` - Shows benefits dialog
- `_onNavBarTap()` - Handles bottom nav taps

---

## 🎯 Mock Data

### Location Placeholders:
The text inputs accept any string. Suggested test values:
- Enugu, Abuja, Lagos, Port Harcourt, Onitsha, Aba, Awka

### Date Range:
- Start: Today (current date)
- End: +90 days from today
- Default: Current date

### Passenger Count:
- Minimum: 1
- Default: 1
- Maximum: None (can increment indefinitely)

---

## 🚀 Testing Checklist

When testing on Android Studio:

- [ ] **Trip Search Card**
  - [ ] Departure field accepts input
  - [ ] Destination field accepts input
  - [ ] Swap button swaps the two values
  - [ ] Date picker opens and allows selection
  - [ ] Date picker restricts to current day through +90 days
  - [ ] Passenger counter increments correctly
  - [ ] Passenger counter decrements correctly
  - [ ] Passenger counter stops at minimum 1
  - [ ] Search button shows error when fields are empty
  - [ ] Search button navigates with valid inputs

- [ ] **Popular Routes Section**
  - [ ] Horizontal scroll works smoothly
  - [ ] All 5 route cards are visible
  - [ ] Each "VIEW TRIPS" button navigates correctly
  - [ ] Route name is passed to TripListScreen

- [ ] **Agent Promotion**
  - [ ] Card is visible at bottom of scroll
  - [ ] "Explore Benefits" button opens dialog
  - [ ] Dialog displays correct message
  - [ ] "Got it" button dismisses dialog

- [ ] **Navigation Components**
  - [ ] Bottom nav bar appears
  - [ ] Bottom nav hides on scroll down
  - [ ] Bottom nav shows on scroll up
  - [ ] Side drawer opens from menu button
  - [ ] All drawer items are functional

---

## 📝 Customization Guide

### Adding New Routes:

Edit the `routes` list in `_buildPopularRoutesSection()`:

```dart
final List<Map<String, String>> routes = [
  {'route': 'City A ↔ City B', 'price': 'From NGN XX,XXX'},
  // Add more routes here
];
```

### Changing Search Validation:

Modify `_searchTrips()` method to add custom validation:

```dart
void _searchTrips() {
  // Add your custom validation here
  if (_departureController.text.isEmpty || 
      _destinationController.text.isEmpty) {
    // Show error
    return;
  }
  
  // Additional validations...
  
  // Navigate
  Navigator.push(...);
}
```

### Updating Agent Promotion:

Edit `_buildAgentPromotionCard()` to change:
- Title text
- Description text
- Button label
- Dialog content

---

## 🔗 Related Files

- **Navigation Target**: `lib/presentation/trip_list_screen/trip_list_screen.dart`
- **Bottom Navigation**: `lib/widgets/custom_bottom_nav_bar.dart`
- **Side Drawer**: `lib/widgets/custom_side_drawer.dart`
- **Theme Configuration**: `lib/theme/app_theme.dart`
- **Custom Icons**: `lib/widgets/custom_icon_widget.dart`

---

## 📚 Next Steps

After testing the Home Screen:

1. **Implement TripListScreen**
   - Display available trips based on search criteria
   - Add filters (time, price, operator)
   - Add booking functionality

2. **Add Real Data**
   - Replace mock routes with API calls
   - Connect date picker to availability system
   - Implement real-time pricing

3. **Enhance Features**
   - Add recent searches
   - Save favorite routes
   - Enable location auto-complete
   - Add route suggestions

4. **Complete Bottom Nav Screens**
   - My Tickets screen
   - Luggage screen
   - Profile screen

---

**Last Updated:** October 15, 2025  
**Version:** 1.0.0
