# 🛠 SVP System Laravel Development Roadmap

This roadmap outlines the phases, tools, and Laravel packages that will be used to develop the Web-Based Small Value Procurement (SVP) system for Cagayan State University.

---

## 🚀 Phase 1: Project Setup and Architecture

- [x] Install Laravel via Composer
- [x] Configure `.env` for database and mail
- [x] Set up version control (Git) and repository

**Packages:**
- [x] `laravel/breeze` – for user authentication and scaffolding
- [x] `barryvdh/laravel-debugbar` – for debugging
- [x] `spatie/laravel-permission` – for role and permission management

---

## 🔐 Phase 2: User Management & Role-Based Access

- [x] Implement login, registration, and password reset
- [x] Define user roles: Requestor, Approver, Procurement Officer, Admin
- [x] Restrict access to pages based on roles

**Packages:**
- [x] `spatie/laravel-permission` – to handle RBAC
- [x] `laravel/breeze` – for simple auth scaffolding

---

## 📝 Phase 3: Purchase Request (PR) Module

- [x] Create PR submission form
- [x] Enable file uploads for PPMP documents
- [x] Store PR data in `purchase_requests` table

**Packages:**
- [x] `laravelcollective/html` – for building forms
- [ ] `intervention/image` – for document upload validation (if needed)

---

## 🧮 Phase 4: Budget Approval Workflow

- [x] Build PR approval queue for Budget Officers
- [x] Allow status updates (Approved/Rejected)
- [x] Attach approved budget amount

---

## 📋 Phase 5: BAC Evaluation and Supplier Canvassing

- [x] Create interfaces for BAC members to evaluate specs
- [x] Build RFQ generation feature
- [x] Link supplier database to RFQ items

**Packages:**
- [ ] `maatwebsite/excel` – for exporting/importing supplier data and quotations
- [x] `dompdf/dompdf` or `barryvdh/laravel-dompdf` – to generate printable RFQs and AOQs

---

## 🛍️ Phase 6: Document Generation (AOQ, PO, DV)

- [x] Automate generation of Abstracts of Quotation, Purchase Orders, Disbursement Vouchers
- [x] Implement print/export buttons for each document

---

## 📦 Phase 7: Supplier Management Module

- [x] Add/edit/delete supplier entries
- [x] Tag suppliers by procurement category
- [x] Track supplier performance history

---

## 🖥️ Phase 8: Admin Dashboard & Reporting

- [x] Create dashboards for all user roles
- [x] Add tables for tracking pending, approved, and rejected PRs
- [x] Build reporting feature with filters by date, department, category

**Packages:**
- [x] `yajra/laravel-datatables` – for searchable, paginated data tables
- [x] `spatie/laravel-activitylog` – for audit trail logging
- [x] `barryvdh/laravel-dompdf` – for report PDF export

---

## 🔔 Phase 9: Notification System

- [x] Implement email notifications for key actions (PR submitted, approved, rejected)
- [x] Add in-app notifications using Laravel Broadcast or Pusher (optional)

**Packages:**
- [x] `laravel-notification-channels/webpush` – for real-time in-app notifications
- [x] `pusher/pusher-php-server` – for event broadcasting (optional)

---

## 🔍 Phase 10: Testing and Security

- [ ] Write unit tests for models and controllers
- [x] Test form validation, file uploads, and access control
- [ ] Conduct penetration testing and ensure HTTPS is enabled

**Packages:**
- [ ] `phpunit/phpunit` – built-in Laravel testing
- [x] `fzaninotto/faker` – for seeding mock data

---

## 🌐 Phase 11: Deployment and Optimization

- [ ] Deploy to production server (DigitalOcean, shared hosting, etc.)
- [x] Run migrations and seeders
- [ ] Set up caching, queue workers, and daily backups

**Tools:**
- [ ] Laravel Forge / Envoyer (optional for CI/CD)
- [ ] `spatie/laravel-backup` – for automated backups

---

## ✅ Optional Enhancements

- [ ] Multi-campus module support
- [ ] Supplier ratings and historical performance analytics
- [ ] SMS integration for urgent procurement alerts
