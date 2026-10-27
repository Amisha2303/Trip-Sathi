# Trip Sathi — Travel Planning & Booking Platform

> **"Plan Your Journey. Create Beautiful Memories."**  
> An all-in-one travel planning and booking platform built with **Laravel 11**, **PHP 8.2+**, **Bootstrap 5**, and **MySQL**.

---

## 🌟 Key Highlights

- **Multi-Vertical Catalogue**: Discover and book **Hotels**, **Adventures**, **Events**, **Food Experiences**, plus dedicated **Spiritual Tourism** (**VIP Darshan** and **Pooja Vidhi**).
- **Concurrency-Safe Inventory**: Slot and room bookings use atomic database transactions with row-level locks (`SELECT ... FOR UPDATE`) to eliminate race conditions.
- **Exact-Precision Financials**: Monetary amounts stored in integer minor units (paise) to prevent float precision drift.
- **Interactive Trip Planner**: Day-by-day itinerary builder with budget tracking and community trip sharing.
- **Multi-Gateway Payments**: Support for **Razorpay**, **Stripe**, and **Offline Mock** gateways with transaction audit logging and refund tracking.
- **Verified Reviews**: Reviews can only be submitted by verified travellers with a completed booking for that item.
- **Comprehensive Admin Panel**: Real-time KPI dashboards, catalogue CRUD, booking state machine transitions, and support inbox.

---

## 📖 Complete Technical Documentation

A full architectural breakdown, system flows, ERD diagrams, and detailed feature specifications are available in:

👉 **[PROJECT_DOCUMENTATION.md](./PROJECT_DOCUMENTATION.md)**

### Table of Contents in `PROJECT_DOCUMENTATION.md`:
1. Executive Summary & Core Concept
2. Technology Stack & System Dependencies
3. Repository Directory & Structural Blueprint
4. Domain Model & Catalogue Architecture (Enforced Morph Maps)
5. End-to-End User & Business Flows (6 Mermaid Diagrams)
6. Detailed Feature Specifications
7. Database Schema & Entity Relationship Diagram (ERD)
8. Security, Performance & Concurrency Invariants
9. API Architecture
10. Local Development, Seeding & Deployment

---

## 🚀 Quick Start Guide

### 1. Prerequisites
- **PHP 8.2+** with `pdo_mysql`, `mbstring`, `bcmath`, `gd`, `zip`
- **Composer 2.x+**
- **Node.js 18+** & **npm**
- **MySQL 8.0+**

### 2. Setup

```bash
# Clone the repository
git clone https://github.com/Amisha2303/Trip-Sathi.git
cd "Trip Sathi"

# Install PHP dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env and run migrations with demo data
php artisan migrate --seed

# Create storage symlink
php artisan storage:link

# Install and build frontend assets
npm install
npm run build

# Start the local development server
php artisan serve
```

### 3. Default Demo Accounts

| Role | Email | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@tripsathi.test` | `Admin@12345` | `/admin/dashboard` (Full system control) |
| **Traveller** | `test@example.com` | `password` | `/dashboard` (Customer bookings & trips) |

---

## 📄 License

The Trip Sathi application is open-sourced software licensed under the [MIT license](LICENSE).
