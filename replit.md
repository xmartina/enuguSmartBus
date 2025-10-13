# Overview

This is a bus booking and ticketing system called "Enugu Smart Bus" (also referred to as BUST or Bus365). The application is a full-stack web solution built with CodeIgniter 4 (PHP backend) and React (frontend) that enables users to search for bus routes, book tickets, select seats, and make payments. The system supports both customer-facing features (booking, payments) and administrative functions (fleet management, scheduling, reporting).

The platform provides:
- Online bus ticket booking with seat selection
- Multi-route and schedule management
- Payment processing via Stripe and Flutterwave
- Fleet and vehicle management
- Employee and agent management
- Reporting and analytics
- Multi-language support

# User Preferences

Preferred communication style: Simple, everyday language.

# System Architecture

## Backend Architecture

**Framework:** CodeIgniter 4 (PHP)
- **Pattern:** MVC (Model-View-Controller) architecture
- **Directory Structure:** Modular organization with separate modules for each business domain (Account, Agent, Fleet, Schedule, Ticket, Trip, etc.)
- **Rationale:** CodeIgniter 4 provides a lightweight, fast PHP framework with built-in security features and clear separation of concerns. The modular structure allows for independent development and maintenance of different system components.

**Authentication & Authorization:**
- JWT (JSON Web Tokens) via `firebase/php-jwt` library
- Role-based access control through the Role module
- Session management through CodeIgniter's built-in session handlers
- **Rationale:** JWT provides stateless authentication suitable for API endpoints, while CodeIgniter sessions handle traditional web authentication.

**API Layer:**
- RESTful API endpoints for frontend consumption
- CORS support via `agungsugiarto/codeigniter4-cors`
- JSON response format for data exchange
- **Rationale:** REST APIs provide a clean interface between frontend and backend, enabling potential mobile app development in the future.

## Frontend Architecture

**Framework:** React with Create React App build system
- **Bundler:** Webpack (via CRA)
- **Styling:** CSS with custom styles and utility classes
- **Routing:** Client-side routing (inferred from SPA structure)
- **Rationale:** React provides component-based architecture for building interactive UIs, with strong ecosystem support and performance optimization through virtual DOM.

**Key Frontend Features:**
- Seat selection visualization with SVG icons
- Interactive booking flow with multi-step forms
- Real-time seat availability checking
- Responsive design with mobile support
- **Rationale:** Component reusability and state management in React simplifies complex UI interactions like seat selection and booking workflows.

**UI/UX Patterns:**
- Progressive enhancement for mobile devices
- Floating modal popups for low-resolution displays
- Custom date/time pickers for scheduling
- Image upload with preview functionality
- **Rationale:** Ensures consistent user experience across devices while optimizing for mobile-first usage patterns.

## Data Storage

**Database:** MySQL (MySQLi driver)
- Primary database for relational data storage
- Tables for locations, schedules, trips, tickets, fleet, users, etc.
- **Configuration:** Database credentials stored in environment configuration
- **Rationale:** MySQL provides ACID compliance for transactional data like bookings and payments, with strong support for complex queries needed for schedule and route management.

**Session Storage:**
- File-based session handler (CodeIgniter default)
- Configurable session expiration (7200 seconds default)
- **Rationale:** File-based sessions are simple to implement and sufficient for moderate traffic levels.

## Third-Party Integrations

**Payment Gateways:**
1. **Stripe** (`stripe/stripe-php`)
   - Credit/debit card processing
   - Secure payment handling
   - **Rationale:** Industry-standard payment processor with comprehensive API and built-in fraud protection

2. **Flutterwave**
   - Alternative payment gateway (likely for regional support)
   - Visible in frontend assets
   - **Rationale:** Provides localized payment options for African markets

**Rich Text Editing:**
- CKEditor 4 for content management
- Used for blog posts and page content
- **Rationale:** Mature WYSIWYG editor with extensive plugin ecosystem

**Data Visualization:**
- Chart.js for analytics dashboards
- ApexCharts for advanced charting
- **Rationale:** Provides interactive visualizations for income/expense tracking and reporting

**UI Components:**
- SumoSelect for enhanced multi-select dropdowns
- Bootstrap DatePicker for date selection
- ClockPicker for time selection
- DataTables for tabular data with export functionality
- **Rationale:** Pre-built components accelerate development and ensure consistent UX patterns

**Image Management:**
- Spartan Multi Image Picker for upload interfaces
- Support for vehicle images and document uploads
- **Rationale:** Simplifies complex file upload workflows with preview and validation

## Additional Architectural Decisions

**Localization:**
- Multi-language support through Localize module
- Configurable language settings
- **Rationale:** Enables serving diverse user bases with different language preferences

**Security Measures:**
- CSRF protection (CodeIgniter built-in)
- Input validation and sanitization
- Prepared statements for SQL queries
- XSS protection in HTML rendering
- **Rationale:** Defense-in-depth approach to protect against common web vulnerabilities

**Performance Optimization:**
- Asset minification (visible in production builds)
- Code splitting in React bundles
- PerfectScrollbar for smooth scrolling
- **Rationale:** Reduces load times and improves perceived performance

**Responsive Design Strategy:**
- Bootstrap 5 grid system
- Mobile-first CSS approach
- Native select rendering on mobile devices
- Float width threshold (400px) for popup rendering
- **Rationale:** Ensures usability across device types while leveraging native controls where appropriate