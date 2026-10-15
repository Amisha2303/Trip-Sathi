# MASTER PROMPT — TRIP SATHI

You are a senior Laravel 11 architect, full-stack developer, UI/UX designer, database architect, security engineer, and QA engineer.

Build a **complete, production-ready travel planning and booking platform named "Trip Sathi"** from scratch.

The application must be fully functional, responsive, secure, scalable, visually attractive, and ready to run locally and deploy to a production server.

Do NOT create a prototype, static demo, incomplete CRUD, placeholder-only project, or pseudo-code.

Implement the complete working application with database, authentication, admin panel, user panel, booking system, email notifications, validation, security, search, filtering, dashboards, and all required backend/frontend functionality.

---

# 1. PROJECT INFORMATION

Project Name:
Trip Sathi

Project Type:
Travel Planner & Booking Platform

Technology:

* Laravel 11
* PHP 8.2+
* MySQL 8+
* Blade
* HTML5
* CSS3
* JavaScript
* Bootstrap 5
* AJAX / Fetch API
* Laravel Eloquent ORM
* Laravel Authentication
* Laravel Middleware
* Laravel Policies & Gates
* Laravel Notifications
* Laravel Mail
* Laravel Queues
* Laravel Scheduler
* Laravel Storage
* REST API architecture
* Git/GitHub ready

Architecture:
Laravel MVC + Service Layer + Repository-style organization where appropriate.

---

# 2. MAIN CONCEPT

Trip Sathi is an all-in-one travel planning and booking platform.

Users should be able to:

* Discover destinations
* Search destinations
* View hotels
* Book hotels
* Discover restaurants/food experiences
* Book food experiences
* Discover events
* Book events
* Discover adventure activities
* Book adventure activities
* Book VIP Darshan
* Book Pooja Vidhi
* Create/manage trips
* Add activities to trips
* View booking history
* Manage profile
* Receive email notifications
* Receive booking confirmations
* Download booking receipts/invoices
* Cancel eligible bookings
* View booking status
* Contact support

Administrators should be able to manage the entire platform from a powerful dashboard.

---

# 3. DESIGN REQUIREMENTS

Create a modern, premium, attractive travel website.

The design should feel similar to a combination of:

* Modern travel marketplace
* Booking platform
* Premium tourism website
* SaaS dashboard

Do NOT create a generic template-looking website.

Design principles:

* Responsive
* Mobile-first
* Modern cards
* Large destination imagery
* Smooth animations
* Elegant typography
* Clean spacing
* Professional navigation
* Sticky header
* Attractive hero section
* Search interface
* Beautiful booking cards
* Modern dashboard
* Professional tables
* Status badges
* Empty states
* Loading states
* Toast notifications
* Confirmation dialogs

Use a consistent Trip Sathi visual identity.

Primary visual direction:

* Travel-inspired
* Fresh
* Premium
* Friendly
* Trustworthy
* Modern

Avoid excessive gradients, excessive animations, excessive shadows, and clutter.

---

# 4. USER WEBSITE

Create the following pages.

## Home

Sections:

1. Header
2. Hero banner
3. Main travel search
4. Popular destinations
5. Featured hotels
6. Adventure activities
7. Upcoming events
8. VIP Darshan
9. Pooja Vidhi
10. Food experiences
11. Popular trips
12. Why Trip Sathi
13. Customer reviews
14. Newsletter subscription
15. Footer

Hero section should contain:

"Plan Your Journey. Create Beautiful Memories."

Search options:

* Destination
* Travel date
* Number of travelers
* Category

Search button:

"Explore Now"

---

# 5. DESTINATION SYSTEM

Create destination management.

Each destination should contain:

* Name
* Slug
* Country
* State
* City
* Description
* Short description
* Main image
* Gallery
* Latitude
* Longitude
* Popularity
* Featured status
* Active status
* SEO title
* SEO description

Destination page should display:

* Hero image
* Description
* Hotels
* Restaurants
* Events
* Adventures
* Darshan
* Pooja
* Recommended places
* Map
* Reviews

---

# 6. HOTEL BOOKING

Admin can create/manage:

* Hotels
* Rooms
* Room types
* Amenities
* Images
* Pricing
* Availability
* Discounts
* Policies

Hotel fields:

* Hotel name
* Description
* Address
* Destination
* Star rating
* Images
* Amenities
* Check-in time
* Check-out time
* Cancellation policy
* Status

Room fields:

* Room name
* Room type
* Capacity
* Price
* Discount
* Number of rooms
* Amenities
* Images
* Status

Users can:

* Search hotels
* Filter by price
* Filter by rating
* Filter by amenities
* View details
* Select room
* Select dates
* Enter guest information
* Confirm booking

Prevent double booking.

---

# 7. FOOD / RESTAURANT BOOKING

Admin can manage:

* Restaurants
* Food experiences
* Menus
* Images
* Pricing
* Available slots
* Tables
* Offers

Users can:

* Browse restaurants
* Search
* Filter
* View menu
* Select date
* Select time
* Select guests
* Book table/experience

---

# 8. EVENTS

Admin can create:

* Events
* Event categories
* Venue
* Date
* Start time
* End time
* Ticket types
* Ticket prices
* Capacity
* Images
* Description

Users can:

* Browse events
* View event details
* Purchase tickets
* Receive confirmation
* View ticket

Implement capacity management.

Prevent overbooking.

---

# 9. ADVENTURE ACTIVITIES

Examples:

* Trekking
* River rafting
* Paragliding
* Scuba diving
* Camping
* Safari
* Zipline
* Water sports

Admin can manage:

* Activity
* Destination
* Description
* Images
* Price
* Duration
* Difficulty
* Age limit
* Maximum participants
* Available dates
* Time slots
* Safety instructions

Users can book activities.

---

# 10. VIP DARSHAN

Create a dedicated VIP Darshan booking system.

Admin can manage:

* Temple
* Destination
* Darshan type
* Date
* Time slot
* Price
* Maximum visitors
* Instructions
* Documents required
* Active status

User booking:

* Temple
* Date
* Time
* Number of visitors
* Visitor information
* ID/document information if required
* Confirmation

---

# 11. POOJA VIDHI

Admin can manage:

* Temple
* Pooja name
* Description
* Benefits
* Duration
* Price
* Available dates
* Available slots
* Maximum participants
* Requirements
* Images

Users can book Pooja Vidhi.

Send booking confirmation by email.

---

# 12. TRIP PLANNER

This is one of the most important features.

Users can create their own trip.

Example:

Trip:
"Ahmedabad Weekend Trip"

Fields:

* Trip name
* Destination
* Start date
* End date
* Number of travelers
* Budget
* Notes

Users can add:

* Hotels
* Restaurants
* Events
* Adventures
* Darshan
* Pooja
* Custom places

Create itinerary:

Day 1

* Hotel
* Breakfast
* Adventure
* Dinner

Day 2

* Temple
* VIP Darshan
* Event

Provide drag-and-drop itinerary functionality where practical.

---

# 13. USER AUTHENTICATION

Implement secure authentication.

Features:

* Registration
* Login
* Logout
* Forgot password
* Reset password
* Email verification
* Remember me
* Password confirmation
* Profile management

User profile:

* Name
* Email
* Phone
* Profile image
* Date of birth
* Gender
* Address
* City
* State
* Country

Use Laravel's secure authentication mechanisms.

Never store plain-text passwords.

---

# 14. USER DASHBOARD

Create a professional dashboard.

Dashboard cards:

* Total bookings
* Upcoming bookings
* Completed bookings
* Cancelled bookings
* Saved trips
* Wishlist

Dashboard sections:

* Upcoming trips
* Recent bookings
* Booking status
* Recommended destinations
* Notifications

Pages:

/dashboard
/profile
/bookings
/bookings/{id}
/trips
/trips/{id}
/wishlist
/notifications
/settings

---

# 15. WISHLIST

Users can save:

* Hotels
* Destinations
* Events
* Adventures
* Restaurants
* Darshan
* Pooja

Use AJAX for add/remove where appropriate.

---

# 16. REVIEWS AND RATINGS

Users can review completed bookings.

Features:

* 1–5 star rating
* Review text
* Images optional
* Admin moderation
* Report review
* Approve/reject review

Only verified booking users should be able to review.

Prevent duplicate reviews for the same booking.

---

# 17. BOOKING ENGINE

Create a reusable booking architecture.

Booking fields:

* Booking ID
* User
* Booking type
* Item
* Destination
* Booking date
* Start time
* End time
* Quantity
* Guests
* Amount
* Discount
* Tax
* Final amount
* Payment status
* Booking status
* Cancellation status
* Notes

Generate unique booking numbers.

Example:

TS-2026-000001

Booking statuses:

* Pending
* Confirmed
* Processing
* Completed
* Cancelled
* Refunded
* Failed

Payment statuses:

* Pending
* Paid
* Failed
* Refunded
* Partially refunded

---

# 18. PAYMENT ARCHITECTURE

Build the application payment-ready.

Create a payment service abstraction.

Example:

PaymentGatewayInterface

Implement architecture for:

* Razorpay
* Stripe

The payment gateway should be configurable through .env.

Never hardcode API keys.

Use:

PAYMENT_GATEWAY=razorpay

or

PAYMENT_GATEWAY=stripe

Create:

* PaymentService
* PaymentTransaction
* PaymentCallback
* Payment verification
* Signature verification
* Refund architecture

Never mark a booking as paid based only on frontend response.

Always verify payment server-side.

---

# 19. EMAIL SYSTEM

Implement Laravel Mail.

Create professional email templates.

Emails:

1. Welcome email
2. Email verification
3. Password reset
4. Booking created
5. Booking confirmed
6. Booking cancelled
7. Payment successful
8. Payment failed
9. Refund processed
10. Trip reminder
11. Event reminder
12. Admin notification
13. Contact form notification

Use Markdown or Blade mail templates.

Create:

app/Mail/

Use queues for email sending.

Do not block the booking request while sending email.

---

# 20. NOTIFICATIONS

Implement Laravel Notifications.

Channels:

* Database
* Email

Notification examples:

* Booking confirmation
* Booking cancellation
* Payment confirmation
* Upcoming trip reminder
* Event reminder
* Admin announcements

Create notification center.

Unread/read status.

Mark all as read.

---

# 21. ADMIN PANEL

Create a complete professional admin dashboard.

Admin URL:

/admin

Admin dashboard must NOT use the normal user dashboard design.

Create a dedicated admin layout.

Sidebar:

Dashboard

Management:

* Users
* Destinations
* Hotels
* Rooms
* Restaurants
* Food Experiences
* Events
* Adventure Activities
* VIP Darshan
* Pooja Vidhi
* Categories
* Amenities
* Coupons
* Reviews
* Testimonials

Bookings:

* All Bookings
* Hotel Bookings
* Food Bookings
* Event Bookings
* Adventure Bookings
* Darshan Bookings
* Pooja Bookings

Finance:

* Payments
* Transactions
* Refunds
* Coupons
* Revenue

System:

* Notifications
* Email Logs
* Contact Messages
* Newsletter Subscribers
* Settings
* Admin Users
* Roles
* Permissions
* Activity Logs

---

# 22. ADMIN DASHBOARD

Dashboard statistics:

* Total users
* Total bookings
* Today's bookings
* Upcoming bookings
* Revenue
* Monthly revenue
* Pending bookings
* Cancelled bookings
* New users
* Popular destinations

Charts:

* Revenue chart
* Booking chart
* User registration chart
* Booking category chart

Recent bookings table.

Recent users table.

Top destinations.

---

# 23. ADMIN CRUD

Every major entity must have:

* List
* Search
* Filter
* Sort
* Create
* View
* Edit
* Delete
* Bulk actions where appropriate
* Pagination

Use Laravel Form Requests for validation.

Use Policies for authorization.

Never rely only on frontend permission checks.

---

# 24. ADMIN ROLES & PERMISSIONS

Implement RBAC.

Roles:

* Super Admin
* Admin
* Manager
* Booking Manager
* Content Manager
* Support Staff

Permissions:

Examples:

users.view
users.create
users.edit
users.delete

bookings.view
bookings.edit
bookings.cancel

hotels.view
hotels.create
hotels.edit
hotels.delete

etc.

Super Admin has all permissions.

Use Laravel Gates/Policies.

---

# 25. SECURITY

Implement strong Laravel security.

Requirements:

* CSRF protection
* XSS protection
* SQL injection prevention
* Mass assignment protection
* Authentication
* Authorization
* Policies
* Gates
* Rate limiting
* Secure password hashing
* Email verification
* Secure session configuration
* HTTP-only cookies
* Secure cookies in production
* SameSite cookies
* Input validation
* Form Requests
* File upload validation
* MIME validation
* File size validation
* Secure filenames
* Prevent executable uploads
* Storage outside public when appropriate
* Signed URLs where needed
* Login throttling
* API throttling
* Admin route protection
* Security headers
* Content Security Policy where practical
* Clickjacking protection
* Secure error handling
* Production APP_DEBUG=false
* Secrets only in .env
* No API keys in frontend
* No sensitive information in logs

Never expose:

* Passwords
* Tokens
* API keys
* Payment secrets
* Session data

---

# 26. FILE UPLOAD SECURITY

Images and documents must be securely handled.

Allowed image types:

* jpg
* jpeg
* png
* webp

Validate:

* MIME
* Extension
* File size
* Image dimensions

Generate safe filenames.

Do not trust user-provided filenames.

Store uploads using Laravel Storage.

Do not allow executable files.

---

# 27. DATABASE DESIGN

Create proper migrations.

Main tables should include:

users
roles
permissions
role_user
permission_role

destinations
destination_images

hotels
hotel_images
hotel_amenities
rooms
room_images

restaurants
restaurant_images
food_experiences

events
event_images
event_ticket_types

adventures
adventure_images
adventure_slots

darshan
darshan_images
darshan_slots

poojas
pooja_images
pooja_slots

trips
trip_items
trip_days

bookings
booking_items
payments
payment_transactions
refunds

wishlists
reviews
review_images

coupons
coupon_usages

notifications

contact_messages
newsletter_subscribers

activity_logs

settings

email_logs

Create proper foreign keys.

Use indexes where required.

Use unique constraints where appropriate.

Use soft deletes for appropriate entities.

---

# 28. ELOQUENT RELATIONSHIPS

Properly implement relationships.

Examples:

User hasMany Bookings.

User hasMany Trips.

Destination hasMany Hotels.

Destination hasMany Events.

Hotel hasMany Rooms.

Hotel hasMany Bookings.

Trip belongsTo User.

Trip hasMany TripItems.

Booking belongsTo User.

Booking morphs to bookable entity where appropriate.

Use polymorphic relationships where they make architectural sense.

Avoid unnecessary database queries.

Prevent N+1 queries using eager loading.

---

# 29. SEARCH

Implement global search.

Search:

* Destinations
* Hotels
* Restaurants
* Events
* Adventures
* Darshan
* Pooja

Filters:

* Price
* Rating
* Date
* Category
* Destination
* Availability

Use optimized queries.

Add database indexes.

---

# 30. SEO

Implement SEO-ready pages.

Every major page should support:

* SEO title
* Meta description
* Canonical URL
* Open Graph metadata
* Twitter card metadata
* SEO-friendly slug
* Sitemap
* robots.txt
* Structured data where appropriate

Generate:

/sitemap.xml

Use schema markup for:

* Hotel
* Event
* Tourist destination
* Local business where appropriate

---

# 31. CONTACT SYSTEM

Create contact page.

Fields:

* Name
* Email
* Phone
* Subject
* Message

Save contact requests in database.

Send notification email to admin.

Admin can:

* View
* Mark read
* Reply status
* Delete

Protect form with validation and rate limiting.

---

# 32. NEWSLETTER

Create newsletter subscription.

Fields:

* Email
* Status
* Subscription date

Prevent duplicate email subscriptions.

Admin can export subscribers.

---

# 33. COUPONS

Admin can create coupons.

Coupon types:

* Percentage
* Fixed amount

Conditions:

* Minimum order
* Maximum discount
* Start date
* Expiry date
* Usage limit
* Per-user limit
* Applicable category
* Applicable destination

Validate coupons server-side.

---

# 34. ADMIN SETTINGS

Create settings management.

Settings:

* Website name
* Logo
* Favicon
* Email
* Phone
* Address
* Currency
* Tax
* Booking settings
* Cancellation settings
* SMTP settings
* Payment gateway
* Social media
* SEO defaults

Never expose sensitive settings unnecessarily.

Sensitive configuration should remain in .env.

---

# 35. ACTIVITY LOGGING

Create admin activity logs.

Track:

* Login
* Logout
* Create
* Update
* Delete
* Booking status change
* Payment changes
* User changes
* Settings changes

Store:

* User
* Action
* Module
* Record ID
* IP address
* User agent
* Timestamp

Do not log passwords, tokens, payment secrets, or sensitive credentials.

---

# 36. API

Create REST API structure.

API version:

/api/v1/

Endpoints for:

* Authentication
* Destinations
* Hotels
* Restaurants
* Events
* Adventures
* Darshan
* Pooja
* Trips
* Bookings
* User profile

Use Laravel Sanctum for API authentication where required.

Implement:

* API Resources
* API validation
* Rate limiting
* Proper HTTP status codes
* Consistent JSON responses

Example response:

{
"success": true,
"message": "Booking created successfully",
"data": {}
}

---

# 37. QUEUES

Implement Laravel queues.

Queue:

* Emails
* Notifications
* Heavy processing
* Image processing where appropriate

Support:

php artisan queue:work

Configure database queue initially.

Keep the architecture ready for Redis.

---

# 38. SCHEDULER

Implement Laravel Scheduler.

Scheduled jobs:

* Upcoming booking reminders
* Event reminders
* Trip reminders
* Expired coupon cleanup
* Temporary data cleanup
* Pending booking cleanup
* Email retry handling

Use Laravel's scheduling system.

---

# 39. CACHING

Implement caching where useful.

Cache:

* Popular destinations
* Categories
* Settings
* Featured hotels
* Featured events

Do not cache user-specific sensitive data incorrectly.

Architecture should support Redis.

---

# 40. ERROR HANDLING

Create professional error pages:

404
403
419
429
500
503

Do not expose stack traces in production.

Create proper exception handling.

Log server-side errors securely.

---

# 41. UX REQUIREMENTS

Every important action should provide feedback.

Examples:

* Loading spinner
* Success toast
* Error toast
* Confirmation modal
* Empty state
* Validation errors
* Disabled submit button while processing
* Booking progress
* Payment status

Prevent double submission.

Use AJAX/Fetch where it improves UX.

---

# 42. RESPONSIVE DESIGN

Must work properly on:

* Desktop
* Laptop
* Tablet
* Mobile

Admin panel must also be responsive.

Do not simply shrink desktop layouts.

Create proper mobile navigation.

---

# 43. PERFORMANCE

Optimize application performance.

Requirements:

* Lazy-load images
* Use responsive images
* Optimize database queries
* Eager loading
* Pagination
* Cache frequently accessed data
* Queue emails
* Minimize unnecessary JavaScript
* Avoid unnecessary API calls
* Use indexes
* Avoid N+1 queries
* Optimize Blade rendering

---

# 44. ACCESSIBILITY

Follow good accessibility practices.

Implement:

* Semantic HTML
* Proper labels
* Keyboard navigation
* Focus states
* Alt text
* Accessible buttons
* ARIA only where required
* Sufficient contrast

---

# 45. PROJECT STRUCTURE

Use clean Laravel structure.

Example:

app/
Actions/
Console/
Enums/
Exceptions/
Http/
Controllers/
Admin/
Api/
Auth/
Requests/
Resources/
Mail/
Models/
Notifications/
Policies/
Services/
Jobs/
Events/
Listeners/

resources/
views/
layouts/
components/
user/
admin/
auth/
emails/

routes/
web.php
admin.php
api.php
console.php

database/
migrations/
seeders/
factories/

public/
assets/

storage/

---

# 46. ENUMS

Use PHP Enums where appropriate.

Examples:

BookingStatus
PaymentStatus
UserRole
BookingType
PaymentGateway
CouponType

Avoid magic strings throughout the application.

---

# 47. FORM REQUESTS

Create separate Form Request classes.

Examples:

StoreHotelRequest
UpdateHotelRequest
StoreRoomRequest
StoreBookingRequest
StoreTripRequest
UpdateProfileRequest
StoreDestinationRequest

Never put large validation logic directly inside controllers.

---

# 48. SERVICES

Create service classes.

Examples:

BookingService
PaymentService
HotelBookingService
TripService
NotificationService
EmailService
CouponService
FileUploadService
DestinationService

Controllers should remain thin.

Business logic belongs in services/actions.

---

# 49. TRANSACTIONS

Use database transactions for critical operations.

Especially:

* Booking creation
* Payment processing
* Cancellation
* Refund
* Inventory/availability updates

Example:

DB::transaction(...)

Prevent partial booking records.

---

# 50. DOUBLE BOOKING PROTECTION

This is critical.

Use:

* Database transactions
* Row locking where required
* Availability checks
* Unique constraints where applicable
* Server-side validation

Never trust client-side availability.

---

# 51. ADMIN UX

Admin tables should include:

* Search
* Filters
* Pagination
* Sorting
* Status badges
* Actions dropdown
* View
* Edit
* Delete
* Bulk actions

Use confirmation dialogs for destructive operations.

---

# 52. SEED DATA

Create realistic seed data.

Include:

* Admin user
* Demo user
* Destinations
* Hotels
* Rooms
* Restaurants
* Events
* Adventures
* Darshan
* Pooja
* Reviews
* Bookings

Create factories.

Use India-focused demo destinations such as:

* Ahmedabad
* Dwarka
* Somnath
* Bhavnagar
* Vadodara
* Surat
* Udaipur
* Jaipur
* Mumbai
* Goa
* Delhi
* Varanasi

Use placeholder/demo images from safe configurable sources or local assets.

Do not depend on external image URLs for core functionality.

---

# 53. DEFAULT ADMIN

Create a seeded admin account.

Credentials must come from environment variables or a secure seeder configuration.

DO NOT hardcode production credentials.

For local development, provide a clearly documented demo account.

Force password change for default admin if appropriate.

---

# 54. ENVIRONMENT CONFIGURATION

Create a complete .env.example.

Include:

APP_NAME
APP_ENV
APP_KEY
APP_DEBUG
APP_URL

DB_CONNECTION
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD

MAIL_MAILER
MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
MAIL_ENCRYPTION
MAIL_FROM_ADDRESS
MAIL_FROM_NAME

QUEUE_CONNECTION

CACHE_STORE

SESSION_DRIVER

FILESYSTEM_DISK

PAYMENT_GATEWAY

RAZORPAY_KEY
RAZORPAY_SECRET

STRIPE_KEY
STRIPE_SECRET

---

# 55. MAIL TESTING

Provide development support for:

* Mailpit or similar local mail testing
* SMTP

Document how to configure Gmail SMTP or production SMTP.

Never commit SMTP credentials.

---

# 56. TESTING

Create automated tests.

Feature tests:

* Registration
* Login
* Authorization
* Hotel booking
* Event booking
* Adventure booking
* Darshan booking
* Pooja booking
* Cancellation
* Coupon
* Payment callback
* Admin access
* API authentication

Unit tests:

* Booking service
* Coupon service
* Payment service
* Availability service

Test security-sensitive functionality.

---

# 57. CODE QUALITY

Follow:

* PSR-12
* Laravel conventions
* SOLID principles
* DRY
* Clean code
* Meaningful naming
* Small methods
* Thin controllers
* Reusable components

Avoid:

* Massive controllers
* Duplicate code
* Hardcoded values
* Inline SQL where Eloquent/query builder is sufficient
* Plain-text passwords
* Exposed secrets
* Unvalidated requests

---

# 58. README

Create a professional README.md.

Include:

1. Project overview
2. Features
3. Requirements
4. Installation
5. Environment setup
6. Database setup
7. Migration
8. Seeder
9. Storage link
10. Queue setup
11. Scheduler setup
12. Mail configuration
13. Payment configuration
14. Testing
15. Production deployment
16. Security checklist

---

# 59. INSTALLATION

The final project must work with:

composer install

npm install

cp .env.example .env

php artisan key:generate

php artisan migrate --seed

php artisan storage:link

npm run build

php artisan serve

Provide exact setup instructions.

---

# 60. PRODUCTION DEPLOYMENT

Prepare the project for deployment.

Document:

* Apache/Nginx
* PHP-FPM
* MySQL
* SSL
* Queue worker
* Cron scheduler
* Storage permissions
* Cache configuration
* Production environment
* APP_DEBUG=false

Example scheduler cron:

* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1

Queue worker configuration should support Supervisor.

---

# 61. ADMIN SECURITY

Admin login must have additional security considerations.

Implement:

* Separate admin middleware
* Role checking
* Permission checking
* Login throttling
* Session regeneration
* Logout session invalidation
* Optional 2FA architecture
* Activity logging
* Secure password policy

Never rely on hiding admin buttons as security.

---

# 62. TWO-FACTOR AUTHENTICATION

Implement or architect optional 2FA for administrators.

Support:

* Authenticator application
* Recovery codes
* Enable/disable
* Verification

2FA should be configurable.

---

# 63. PRIVACY

Create:

* Privacy Policy page
* Terms & Conditions
* Cancellation Policy
* Refund Policy
* Cookie Policy

Do not collect unnecessary personal information.

---

# 64. BOOKING RECEIPT

After successful booking/payment:

Generate downloadable booking receipt/invoice.

Include:

* Trip Sathi logo
* Booking number
* Customer details
* Booking details
* Date
* Quantity
* Price
* Tax
* Discount
* Total
* Payment status
* Terms

Use a PDF generation library compatible with Laravel.

---

# 65. ADMIN REPORTS

Create reports:

* Booking report
* Revenue report
* User report
* Destination report
* Product/service performance
* Cancellation report
* Payment report

Allow filtering by:

* Date
* Category
* Status
* Destination

Provide CSV export.

---

# 66. DASHBOARD ANALYTICS

Admin dashboard should display useful KPIs.

Examples:

Total Revenue
This Month Revenue
Total Bookings
Today's Bookings
Pending Bookings
Confirmed Bookings
Cancelled Bookings
Total Users

Charts should be responsive.

---

# 67. FINAL QUALITY REQUIREMENT

Before considering the project complete:

Run:

php artisan migrate:fresh --seed

php artisan test

npm run build

Check:

php artisan route:list

php artisan config:clear

php artisan cache:clear

php artisan view:clear

Test:

* Registration
* Login
* Email verification
* Password reset
* User dashboard
* Search
* Hotel booking
* Event booking
* Adventure booking
* Darshan booking
* Pooja booking
* Trip planner
* Wishlist
* Reviews
* Coupons
* Payments
* Emails
* Notifications
* Admin login
* Admin CRUD
* Roles
* Permissions
* Reports
* Exports
* Mobile responsiveness

Fix all errors before completion.

---

# 68. IMPORTANT DEVELOPMENT RULES

DO NOT:

* Build only frontend screens
* Leave TODO placeholders
* Use fake booking logic
* Skip migrations
* Skip validation
* Skip authorization
* Skip security
* Hardcode API keys
* Hardcode payment credentials
* Store passwords in plain text
* Trust frontend payment status
* Trust frontend availability
* Create duplicate business logic
* Ignore mobile responsiveness
* Ignore database constraints

Every button must perform its intended function.

Every form must save/update real database data.

Every admin CRUD must work.

Every booking must have a proper lifecycle.

---

# 69. DEVELOPMENT ORDER

Build in this exact logical order:

PHASE 1
Laravel 11 project setup

PHASE 2
Database architecture

PHASE 3
Models + relationships

PHASE 4
Authentication

PHASE 5
Admin authentication + RBAC

PHASE 6
Admin dashboard

PHASE 7
Destination management

PHASE 8
Hotel + room management

PHASE 9
Restaurant + food experience

PHASE 10
Events

PHASE 11
Adventure activities

PHASE 12
VIP Darshan

PHASE 13
Pooja Vidhi

PHASE 14
Reusable booking engine

PHASE 15
Trip planner

PHASE 16
Wishlist + reviews

PHASE 17
Coupons

PHASE 18
Payment architecture

PHASE 19
Email system

PHASE 20
Notifications

PHASE 21
Reports

PHASE 22
REST API

PHASE 23
Security hardening

PHASE 24
Performance optimization

PHASE 25
Automated tests

PHASE 26
Production deployment preparation

PHASE 27
Final QA

---

# 70. FINAL DELIVERABLE

The final output must be a complete Laravel 11 application.

Deliver:

* Complete source code
* Migrations
* Models
* Controllers
* Services
* Requests
* Policies
* Middleware
* Jobs
* Events
* Listeners
* Notifications
* Mail classes
* Blade views
* Components
* CSS
* JavaScript
* Routes
* API
* Seeders
* Factories
* Tests
* PDF invoice system
* Admin panel
* User dashboard
* Booking system
* Trip planner
* Payment architecture
* Email system
* Notification system
* Security implementation
* README
* .env.example

The project must be runnable after following the README installation steps.

---

# FINAL INSTRUCTION TO THE AI CODING AGENT

Do not stop after creating the architecture.

Actually implement the application.

After each major phase, verify that the previous functionality still works.

Do not break existing functionality while adding new features.

If a requirement is ambiguous, choose the most production-appropriate Laravel 11 implementation and document the decision.

Prioritize:

1. Security
2. Correctness
3. Database integrity
4. Booking reliability
5. User experience
6. Admin control
7. Performance
8. Maintainability
9. Visual quality

Build Trip Sathi as a **real production-quality travel booking platform**, not as a student-level static project.

